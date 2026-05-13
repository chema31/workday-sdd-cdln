# Spec 3: UserRole Enum and User Model

## Metadata

| Field       | Value                                                              |
|-------------|--------------------------------------------------------------------|
| User Story  | US-001                                                             |
| Type        | Backend (Laravel Admin Panel)                                      |
| Branch      | feature/20260511_user_management_and_application_bootstrap         |
| PR Platform | Bitbucket                                                          |
| Effort      | S                                                                  |
| Depends on  | Spec 2                                                             |
| Date        | 2026-05-12                                                         |

---

## Overview

Create the `App\Enums\UserRole` PHP 8.1 backed enum and update the `User` Eloquent model to use it. Update `UserFactory` with named states.

---

## Architecture Context

| Artifact                                | Responsibility                                                                                  |
|-----------------------------------------|-------------------------------------------------------------------------------------------------|
| `App\Enums\UserRole`                    | PHP 8.1 string-backed enum. Cases: `Admin = 'admin'`, `Employee = 'employee'`.                  |
| `App\Models\User`                       | Adds `role` and `is_active` to `$fillable` and `$casts`; implements `FilamentUser`; adds query scopes. |
| `database/factories/UserFactory.php`   | Named states: `admin()`, `employee()` (default), `inactive()`.                                  |

---

## Implementation Steps

### Step 0 — Verify branch

```bash
git branch --show-current
# Expected: feature/20260511_user_management_and_application_bootstrap
```

---

### Step 1 — Create `App\Enums\UserRole`

Create the file `app/Enums/UserRole.php`:

```php
<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin    = 'admin';
    case Employee = 'employee';
}
```

---

### Step 2 — Update `App\Models\User`

Edit `app/Models/User.php`:

1. Add imports at the top:

```php
use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
```

2. Implement the `FilamentUser` interface on the class declaration:

```php
class User extends Authenticatable implements FilamentUser
```

3. Add `'role'` and `'is_active'` to `$fillable`.

4. Add to `$casts`:

```php
'role'      => UserRole::class,
'is_active' => 'boolean',
```

5. Implement the Filament panel access method:

```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->role === UserRole::Admin;
}
```

6. Add query scopes:

```php
public function scopeActive(Builder $query): Builder
{
    return $query->where('is_active', true);
}

public function scopeEmployees(Builder $query): Builder
{
    return $query->where('role', UserRole::Employee);
}
```

---

### Step 3 — Update `UserFactory`

Edit `database/factories/UserFactory.php`:

1. Import the enum at the top:

```php
use App\Enums\UserRole;
```

2. In the default `definition()` method, set:

```php
'role'      => UserRole::Employee,
'is_active' => true,
```

3. Add named states:

```php
public function admin(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => UserRole::Admin,
    ]);
}

public function inactive(): static
{
    return $this->state(fn (array $attributes) => [
        'is_active' => false,
    ]);
}
```

> Note: The `employee()` state is the default `definition()`. No explicit named state is required unless desired for clarity.

---

### Step 4 — Run test suite

```bash
php artisan test
```

All previous tests (including those from Spec 2) must still pass with no regressions.

---

### Step LAST — Documentation

No `rules/` changes are needed for this spec. The model behaviour is fully derivable from the source code and already covered by Spec 2's data model update.

---

## Unit Test Specifications (TDD)

Write **all tests first** (they must fail) before implementing any production code.

File: `tests/Unit/Models/UserModelTest.php`

```php
<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_enum_has_admin_case(): void
    {
        $this->assertSame('admin', UserRole::Admin->value);
    }

    public function test_user_role_enum_has_employee_case(): void
    {
        $this->assertSame('employee', UserRole::Employee->value);
    }

    public function test_admin_user_can_access_filament_panel(): void
    {
        $user = User::factory()->admin()->create();
        $panel = app(Panel::class);

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_employee_user_cannot_access_filament_panel(): void
    {
        $user = User::factory()->create(); // default = employee
        $panel = app(Panel::class);

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_active_scope_returns_only_active_users(): void
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->inactive()->create();

        $activeUsers = User::active()->get();

        $this->assertCount(3, $activeUsers);
        $activeUsers->each(fn (User $u) => $this->assertTrue($u->is_active));
    }

    public function test_employees_scope_excludes_admins(): void
    {
        User::factory()->count(2)->create(); // default = employee
        User::factory()->admin()->create();

        $employees = User::employees()->get();

        $this->assertCount(2, $employees);
        $employees->each(fn (User $u) => $this->assertSame(UserRole::Employee, $u->role));
    }

    public function test_factory_admin_state_sets_admin_role(): void
    {
        $user = User::factory()->admin()->make();

        $this->assertSame(UserRole::Admin, $user->role);
    }

    public function test_factory_inactive_state_sets_is_active_false(): void
    {
        $user = User::factory()->inactive()->make();

        $this->assertFalse($user->is_active);
    }
}
```

Use `RefreshDatabase` throughout.

---

## Acceptance Criteria

- [ ] `App\Enums\UserRole` exists with `Admin = 'admin'` and `Employee = 'employee'` cases.
- [ ] `User` model implements the `FilamentUser` interface.
- [ ] `canAccessPanel()` returns `true` only when `role === UserRole::Admin`.
- [ ] `$casts` includes `role` → `UserRole::class` and `is_active` → `'boolean'`.
- [ ] `UserFactory` has `admin()` and `inactive()` named states.
- [ ] All 8 unit tests pass.
