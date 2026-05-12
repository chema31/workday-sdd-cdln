# Spec 2: Database Migrations

## Metadata

| Field       | Value                                                              |
|-------------|--------------------------------------------------------------------|
| User Story  | US-001                                                             |
| Type        | Backend (Laravel Admin Panel)                                      |
| Branch      | feature/20260511_user_management_and_application_bootstrap         |
| PR Platform | Bitbucket                                                          |
| Effort      | S                                                                  |
| Depends on  | Spec 1                                                             |
| Date        | 2026-05-12                                                         |

---

## Overview

Create two Laravel migrations:

1. `add_role_and_is_active_to_users_table` — adds a `role` enum(`admin`, `employee`) with default `employee` and an `is_active` boolean with default `true` to the existing `users` table.
2. `create_clock_records_table` — creates the stub table: `id`, `user_id` (FK → users CASCADE DELETE), `clocked_in_at` (timestamp), `clocked_out_at` (timestamp nullable), `timestamps`.

---

## Implementation Steps

### Step 0 — Verify branch

Confirm you are on the correct feature branch before making any changes:

```bash
git branch --show-current
# Expected: feature/20260511_user_management_and_application_bootstrap
```

---

### Step 1 — Migration: add columns to users table

Generate the migration:

```bash
php artisan make:migration add_role_and_is_active_to_users_table --table=users
```

Fill in the generated file:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'employee'])->default('employee')->after('password');
        $table->boolean('is_active')->default(true)->after('role');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'is_active']);
    });
}
```

---

### Step 2 — Migration: create clock_records table

Generate the migration:

```bash
php artisan make:migration create_clock_records_table
```

Fill in the generated file:

```php
public function up(): void
{
    Schema::create('clock_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->timestamp('clocked_in_at');
        $table->timestamp('clocked_out_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('clock_records');
}
```

---

### Step 3 — Run migrations

```bash
php artisan migrate
```

Confirm both operations succeeded:
- `users` table now has `role` and `is_active` columns.
- `clock_records` table now exists.

---

### Step 4 — Run test suite

```bash
php artisan test
```

All pre-existing tests must still pass with no regressions.

---

### Step LAST — Update data model documentation

Update `rules/data-model.md` to reflect:

- **`users` table**: document the new `role` enum column (values: `admin`, `employee`; default: `employee`) and `is_active` boolean column (default: `true`).
- **`clock_records` table**: add the full stub table definition including all columns, the FK constraint to `users`, and the CASCADE DELETE behaviour.

---

## Unit Test Specifications (TDD)

Write **all tests first** (they must fail) before writing any migration code.

File: `tests/Feature/DatabaseMigrationTest.php`

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_role_column(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'role'));
    }

    public function test_users_table_has_is_active_column(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'is_active'));
    }

    public function test_clock_records_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('clock_records'));
    }

    public function test_clock_records_user_id_is_foreign_key(): void
    {
        // Arrange: create a user and a linked clock record
        $user = \App\Models\User::factory()->create();

        \DB::table('clock_records')->insert([
            'user_id'       => $user->id,
            'clocked_in_at' => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->assertDatabaseHas('clock_records', ['user_id' => $user->id]);

        // Act: delete the user
        $user->delete();

        // Assert: CASCADE removed the clock record
        $this->assertDatabaseMissing('clock_records', ['user_id' => $user->id]);
    }
}
```

Use `RefreshDatabase` and `Schema::hasColumn()` / `Schema::hasTable()` as shown above.

---

## Acceptance Criteria

- [ ] `users` table has `role` enum(`admin`, `employee`) column with default `employee`.
- [ ] `users` table has `is_active` boolean column with default `true`.
- [ ] `clock_records` table exists with the schema defined in Step 2.
- [ ] Deleting a user cascades and removes their `clock_records` rows.
- [ ] Both migrations have a complete and correct `down()` method.
- [ ] All 4 migration tests pass.
- [ ] `rules/data-model.md` updated with the new columns and stub table.
