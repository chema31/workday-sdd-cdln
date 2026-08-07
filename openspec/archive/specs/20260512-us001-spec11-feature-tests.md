# Spec 11: Feature Tests (US-001 Full Suite)

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Backend (Laravel Admin Panel)
- **Branch**: feature/20260511_user_management_and_application_bootstrap
- **Estimated effort**: M
- **Depends on**: All previous specs (0–10)

## Overview
Write and verify all 11 feature tests defined in US-001. These tests are the TDD anchors for the entire story — they should have been written first (red) during each spec's implementation. This spec audits, consolidates, and ensures every test is green before the story can be closed.

## Architecture Context
- `tests/Feature/` — all feature tests
- `tests/Unit/` — all unit tests
- `phpunit.xml` — SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`)
- All tests use `RefreshDatabase` trait
- Factories: `UserFactory` (with admin/inactive states), `ClockRecordFactory` (with withClockOut state)

## Implementation Steps

### Step 0: Verify on feature branch. Confirm `phpunit.xml` uses SQLite in-memory.

### Step 1: Audit existing tests from previous specs
Run `php artisan test --verbose` and list all currently passing tests. Any failing test must be fixed before proceeding.

### Step 2: Write/confirm the 11 canonical US-001 tests

The following 11 tests must exist and pass. Organise them in the most logical test file (listed below):

**File**: `tests/Feature/AdminSeederTest.php`
1. `test_admin_seeder_creates_user_from_env_values`
   - Set `config(['app.admin_email' => 'test@example.com', 'app.admin_name' => 'Test Admin', 'app.admin_password' => 'Password1'])`.
   - Run `(new AdminSeeder)->run()`.
   - `assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'admin'])`.

**File**: `tests/Feature/Auth/AdminAuthTest.php`
2. `test_admin_can_login_to_admin_panel`
   - Create admin user with factory.
   - POST `/login` with credentials.
   - Assert redirected to `/admin`.

3. `test_employee_cannot_access_admin_panel`
   - Create employee user.
   - `actingAs($employee)->get('/admin')` — assert 403 or redirect away from admin.

**File**: `tests/Feature/Auth/EmployeeAuthTest.php`
4. `test_inactive_employee_is_rejected_at_login`
   - Create user with `is_active = false`.
   - POST `/login`.
   - Assert validation error message: `'Your account has been deactivated. Contact the administrator.'`

**File**: `tests/Feature/Admin/EmployeeResourceTest.php`
5. `test_admin_can_create_employee_via_filament`
   - Act as admin, use Filament Livewire component or HTTP to submit create form.
   - `assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => 'employee'])`.

6. `test_admin_can_edit_employee_via_filament`
   - Create an employee, act as admin, submit edit form with new name.
   - `assertDatabaseHas('users', ['name' => 'Updated Name'])`.

7. `test_admin_cannot_delete_seeded_admin_account`
   - Seed admin. Act as admin. Attempt delete of admin user via Filament.
   - `assertDatabaseHas('users', ['email' => config('app.admin_email')])` (still exists).

**File**: `tests/Feature/Dashboard/DashboardTest.php`
8. `test_employee_sees_only_own_clock_records_on_dashboard`
   - Create 2 employees, 2 clock records for each (current month).
   - Login as employee 1, GET `/dashboard`.
   - Assert employee 1's records appear; employee 2's records do NOT appear in response.

9. `test_dashboard_button_shows_fichar_entrada_when_no_open_record`
   - Login as employee with no clock records today.
   - GET `/dashboard`.
   - Assert response contains text "Fichar entrada".

10. `test_dashboard_button_shows_fichar_salida_when_open_record_exists`
    - Create open clock record for today (clocked_out_at = null).
    - Login as that employee, GET `/dashboard`.
    - Assert response contains text "Fichar salida".

**File**: `tests/Feature/Admin/EmployeePasswordValidationTest.php`
11. `test_admin_cannot_create_employee_with_weak_password`
    - Act as admin. Attempt to create employee with password `'password'` (no number) and also `'12345'` (too short).
    - Assert validation error on password field for both.

### Step 3: Run full suite
```bash
php artisan test --verbose
```
All tests must be green. Fix any failures before marking spec complete.

### Step LAST: Update Documentation
- No `rules/` changes needed.
- Ensure `README.md` still has correct test run command.

## Acceptance Criteria
- [ ] All 11 canonical US-001 feature tests exist and pass.
- [ ] `php artisan test` exits with 0 (all green).
- [ ] No debug code (`dd()`, `var_dump()`) anywhere.
- [ ] SQLite in-memory confirmed in `phpunit.xml`.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket.
