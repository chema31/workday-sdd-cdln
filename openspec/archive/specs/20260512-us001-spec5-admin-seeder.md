# Spec 5: Admin Seeder

## Metadata

| Field       | Value                                                             |
|-------------|-------------------------------------------------------------------|
| US          | US-001                                                            |
| Area        | Backend (Laravel Admin Panel)                                     |
| Effort      | S                                                                 |
| Depends on  | Spec 3 (User model, UserRole enum, migrations)                    |
| Branch      | `feature/20260511_user_management_and_application_bootstrap`      |
| PR          | Bitbucket                                                         |
| Date        | 2026-05-12                                                        |

---

## Overview

Create an idempotent `AdminSeeder` that reads admin credentials from environment variables via the `config()` helper and creates the admin user using `User::firstOrCreate`. Wire it into `DatabaseSeeder`. Update `.env.example` with the three required variables.

---

## Implementation Steps

> **Write the tests first (TDD).** The steps below follow after the tests are in place and failing.

### Step 1 — Generate the seeder

```bash
php artisan make:seeder AdminSeeder
```

---

### Step 2 — Implement `AdminSeeder::run()`

File: `database/seeders/AdminSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('app.admin_email')],
            [
                'name'      => config('app.admin_name'),
                'password'  => Hash::make(config('app.admin_password')),
                'role'      => UserRole::Admin,
                'is_active' => true,
            ]
        );
    }
}
```

---

### Step 3 — Expose admin config values

Add the following keys to `config/app.php` (inside the returned array):

```php
'admin_email'    => env('ADMIN_EMAIL'),
'admin_name'     => env('ADMIN_NAME', 'Administrador'),
'admin_password' => env('ADMIN_PASSWORD'),
```

> **Why `config()` and not `env()` directly?**
> In production, Laravel caches the configuration (`php artisan config:cache`), making `env()` calls return `null`. Always read environment values through `config()` in application code.

---

### Step 4 — Register in `DatabaseSeeder`

File: `database/seeders/DatabaseSeeder.php`

```php
public function run(): void
{
    $this->call(AdminSeeder::class);
}
```

---

### Step 5 — Update `.env.example`

Add the following lines to `.env.example`:

```dotenv
ADMIN_EMAIL=
ADMIN_NAME=Administrador
ADMIN_PASSWORD=
```

---

### Step 6 — Verify idempotency manually

```bash
php artisan db:seed          # First run — user is created
php artisan db:seed          # Second run — no duplicate, no error
```

---

## Tests

Write these tests **before** implementing. File: `tests/Feature/Admin/AdminSeederTest.php`

Use `RefreshDatabase` trait. Set config values in the test via `config(['app.admin_email' => ..., ...])` before running the seeder.

| # | Test name                                             | What it asserts                                                                                         |
|---|-------------------------------------------------------|---------------------------------------------------------------------------------------------------------|
| 1 | `test_admin_seeder_creates_user_from_env_values`      | After running the seeder, `assertDatabaseHas('users', ['email' => config('app.admin_email')])` is true  |
| 2 | `test_admin_seeder_is_idempotent`                     | Running the seeder twice results in exactly one user row with the admin email                            |
| 3 | `test_seeded_admin_has_admin_role`                    | The created user has `role = UserRole::Admin`                                                            |
| 4 | `test_seeded_admin_is_active`                         | The created user has `is_active = true`                                                                  |

---

## Acceptance Criteria

- [ ] `AdminSeeder` uses `config()` helper, never `env()` directly.
- [ ] `User::firstOrCreate` prevents duplicate admin on repeated seeding.
- [ ] All three config keys (`admin_email`, `admin_name`, `admin_password`) are present in `config/app.php`.
- [ ] All three variables are documented in `.env.example`.
- [ ] All 4 feature tests pass.
