# Spec 10: ClockRecord Policy

## Metadata

- **User Story**: US-001
- **Area**: Backend (Laravel Admin Panel)
- **Effort**: S
- **Depends on**: Spec 4
- **Branch**: `feature/20260511_user_management_and_application_bootstrap`
- **PR**: Bitbucket
- **Date**: 2026-05-12

---

## Overview

Create `App\Policies\ClockRecordPolicy` ensuring employees can only view their own `clock_records`. In Laravel 11, policies are auto-discovered following the `{Model}Policy` naming convention. No manual registration needed.

---

## Architecture Context

- `app/Policies/ClockRecordPolicy.php`
- Policy enforces that `$user->id === $clockRecord->user_id`
- Applied in `DashboardController` via `$this->authorize('view', $clockRecord)` when displaying individual records, and via the Eloquent scope in the list query (defence in depth)

---

## Implementation Steps

### Step 0: Verify on feature branch

Confirm you are on `feature/20260511_user_management_and_application_bootstrap` before making any changes.

---

### Step 1: Generate policy

```bash
php artisan make:policy ClockRecordPolicy --model=ClockRecord
```

---

### Step 2: Implement `view` method

```php
public function view(User $user, ClockRecord $clockRecord): bool
{
    return $user->id === $clockRecord->user_id;
}
```

---

### Step 3: Confirm auto-discovery works

- In Laravel 11, `AppServiceProvider::boot()` can optionally call `Gate::policy(ClockRecord::class, ClockRecordPolicy::class)` if auto-discovery is not picking it up.
- Run `php artisan test` to confirm.

---

### Step 4: Apply policy in DashboardController (defence in depth)

The list query already scopes by `user_id`. Additionally, if individual record access is ever added in a future route, the policy gate will enforce ownership.

---

### Step LAST: No `rules/` changes needed.

---

## Unit Test Specifications (TDD)

Write tests FIRST before implementing any production code.

### File: `tests/Unit/Policies/ClockRecordPolicyTest.php`

Use `RefreshDatabase`.

| Test | Description |
|------|-------------|
| `test_user_can_view_own_clock_record` | Arrange: create user + clock record owned by user. Assert: `$user->can('view', $clockRecord)` returns true. |
| `test_user_cannot_view_other_users_clock_record` | Arrange: create two users, clock record owned by user2. Assert: `$user1->can('view', $clockRecord)` returns false. |
| `test_admin_cannot_view_employee_clock_record_via_policy` | Arrange: admin user, employee clock record. Assert: `$admin->can('view', $clockRecord)` returns false (admin accesses via Filament, not the policy). |

---

## Acceptance Criteria

- [ ] `ClockRecordPolicy::view()` returns true only when `$user->id === $clockRecord->user_id`.
- [ ] Policy is auto-discovered (or registered in `AppServiceProvider`).
- [ ] All 3 policy tests pass.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket.
