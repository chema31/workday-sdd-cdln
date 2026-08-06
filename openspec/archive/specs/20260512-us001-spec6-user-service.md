# Spec 6: UserService

## Metadata

| Field       | Value                                                             |
|-------------|-------------------------------------------------------------------|
| US          | US-001                                                            |
| Area        | Backend (Laravel Admin Panel)                                     |
| Effort      | M                                                                 |
| Depends on  | Spec 3 (User model, UserRole enum, migrations)                    |
| Branch      | `feature/20260511_user_management_and_application_bootstrap`      |
| PR          | Bitbucket                                                         |
| Date        | 2026-05-12                                                        |

---

## Overview

Create `App\Services\User\UserService` with three public methods:

| Method                | Purpose                                                                     |
|-----------------------|-----------------------------------------------------------------------------|
| `createEmployee()`    | Validate password rules, hash it, and persist a new employee `User`.        |
| `updateEmployee()`    | Update name, email, is_active; conditionally re-hash the password.          |
| `isProtectedAdmin()`  | Return `true` if the given user is the configured admin account.            |

The service contains **no HTTP knowledge** (no `Request`, no responses). It is injected via constructor dependency injection into Filament resources and controllers.

---

## Architecture Context

Per `rules/backend-standards.mdc`:

- Business logic lives in `app/Services/`.
- Services are plain PHP classes resolved by the service container.
- No static calls (`User::create()` is acceptable inside a service; `request()` is not).
- Constructor DI example:

```php
public function __construct(private readonly UserService $userService) {}
```

---

## Implementation Steps

> **Write the tests first (TDD).** The steps below follow after the tests are in place and failing.

### Step 1 — Create the service file

Create the directory and file:

```
app/Services/User/UserService.php
```

---

### Step 2 — Implement `createEmployee(array $data): User`

Password validation rules (throw `\InvalidArgumentException` if any rule fails):

1. `$data['password']` must be present and non-empty.
2. Minimum length: 8 characters.
3. Must contain at least one digit (regex: `/[0-9]/`).

User creation:

```php
return User::create([
    'name'      => $data['name'],
    'email'     => $data['email'],
    'password'  => Hash::make($data['password']),
    'role'      => UserRole::Employee,
    'is_active' => $data['is_active'] ?? true,
]);
```

---

### Step 3 — Implement `updateEmployee(User $user, array $data): User`

Password handling:

- If `$data['password']` is non-empty: validate same rules as `createEmployee`, then hash and include in the update.
- If `$data['password']` is empty or not present: **do not touch** the password column.

Always update: `name`, `email`, `is_active`.

Return the updated `$user` instance after `$user->save()`.

---

### Step 4 — Implement `isProtectedAdmin(User $user): bool`

```php
public function isProtectedAdmin(User $user): bool
{
    return $user->email === config('app.admin_email');
}
```

---

### Step 5 — Full class skeleton

```php
<?php

namespace App\Services\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createEmployee(array $data): User
    {
        $this->validatePassword($data['password'] ?? null);

        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => UserRole::Employee,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updateEmployee(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $this->validatePassword($data['password']);
            $user->password = Hash::make($data['password']);
        }

        $user->name      = $data['name'];
        $user->email     = $data['email'];
        $user->is_active = $data['is_active'];
        $user->save();

        return $user;
    }

    public function isProtectedAdmin(User $user): bool
    {
        return $user->email === config('app.admin_email');
    }

    private function validatePassword(?string $password): void
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('Password is required.');
        }

        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters.');
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one number.');
        }
    }
}
```

---

### Step 6 — Run tests

```bash
php artisan test
```

All 8 unit tests in `tests/Unit/Services/UserServiceTest.php` must pass.

---

## Tests

Write these tests **before** implementing. File: `tests/Unit/Services/UserServiceTest.php`

Use `RefreshDatabase` trait. Use `User::factory()->create()` to create existing users for update tests.

| # | Test name                                              | What it asserts                                                                                      |
|---|--------------------------------------------------------|------------------------------------------------------------------------------------------------------|
| 1 | `test_create_employee_creates_user_with_employee_role` | The created user has `role = UserRole::Employee`                                                     |
| 2 | `test_create_employee_hashes_password`                 | `Hash::check($plainPassword, $user->password)` returns `true`                                       |
| 3 | `test_create_employee_throws_when_password_too_short`  | Passing a password shorter than 8 chars throws `\InvalidArgumentException`                           |
| 4 | `test_create_employee_throws_when_password_has_no_number` | Passing a password with no digit throws `\InvalidArgumentException`                               |
| 5 | `test_update_employee_changes_password_when_provided`  | After update with a new password, `Hash::check($newPassword, $user->fresh()->password)` is `true`   |
| 6 | `test_update_employee_preserves_password_when_not_provided` | After update with `password = null`, the stored hash is unchanged                              |
| 7 | `test_is_protected_admin_returns_true_for_admin_email` | Returns `true` when `$user->email` matches `config('app.admin_email')`                              |
| 8 | `test_is_protected_admin_returns_false_for_other_emails` | Returns `false` for a user whose email does not match the admin email                              |

---

## Acceptance Criteria

- [ ] `App\Services\User\UserService` exists with exactly three public methods.
- [ ] `createEmployee()` enforces the three password rules and throws `\InvalidArgumentException` on failure.
- [ ] `updateEmployee()` skips password update when `$data['password']` is empty or null.
- [ ] `isProtectedAdmin()` compares against `config('app.admin_email')`, not `env()`.
- [ ] No HTTP logic (`Request`, `response()`, `redirect()`, etc.) anywhere in the service.
- [ ] All 8 unit tests pass.
