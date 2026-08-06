# Spec 8: Employee Authentication

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Backend (Laravel Admin Panel)
- **Branch**: feature/20260511_user_management_and_application_bootstrap
- **Estimated effort**: M
- **Depends on**: Spec 6 (UserService), Spec 3 (User model with role/is_active)

## Overview
Customise Laravel Breeze's authentication to: (1) reject inactive employees with a specific error message, (2) redirect admins to `/admin` instead of `/dashboard` after login, and (3) ensure unauthenticated routes redirect to `/login`.

## Architecture Context
- `app/Http/Requests/Auth/LoginRequest.php` — override `authenticate()` to check `is_active`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` — override `store()` for role-based redirect
- `routes/web.php` — `/dashboard` route stays, protected by `auth` middleware
- `routes/auth.php` — already cleaned of unused routes (Spec 1)

## Implementation Steps

### Step 0: Verify on feature branch.

### Step 1: Override `LoginRequest::authenticate()` — inactive user check (AC-11)
- **File**: `app/Http/Requests/Auth/LoginRequest.php`
- After Breeze's default credential validation, add:
```php
public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    // Reject inactive users immediately after credential check
    if (! Auth::user()->is_active) {
        Auth::logout();
        throw ValidationException::withMessages([
            'email' => 'Your account has been deactivated. Contact the administrator.',
        ]);
    }

    RateLimiter::clearAttempts($this->throttleKey());
}
```

### Step 2: Override `AuthenticatedSessionController::store()` — role-based redirect (AC-09, AC-10)
- **File**: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- Replace the final redirect line:
```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // Redirect admins to the Filament panel, employees to the dashboard
    if (Auth::user()->role === \App\Enums\UserRole::Admin) {
        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }

    return redirect()->intended(route('dashboard'));
}
```

### Step 3: Verify route protection (AC-08, AC-12, AC-13)
- **File**: `routes/web.php`
- Confirm `/dashboard` is behind `auth` middleware:
```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```
- Confirm `/logout` uses POST (Breeze default) and redirects to `/login`.

### Step 4: Run tests
```bash
php artisan test
```

### Step LAST: Update Documentation
No `rules/` changes needed.

## Unit Test Specifications (TDD)

Write tests FIRST in `tests/Feature/Auth/EmployeeAuthTest.php`:

- `test_employee_can_login_and_is_redirected_to_dashboard`
  - Arrange: create active employee user.
  - Act: POST `/login` with valid credentials.
  - Assert: redirected to `/dashboard`.
- `test_admin_login_redirects_to_admin_panel`
  - Arrange: create admin user.
  - Act: POST `/login` with admin credentials.
  - Assert: redirected to `/admin`.
- `test_inactive_employee_is_rejected_at_login`
  - Arrange: create user with `is_active = false`.
  - Act: POST `/login`.
  - Assert: response has validation error with the exact message `'Your account has been deactivated. Contact the administrator.'`; session has no authenticated user.
- `test_unauthenticated_user_is_redirected_to_login`
  - Act: GET `/dashboard` without auth.
  - Assert: redirected to `/login`.
- `test_logout_destroys_session_and_redirects_to_login`
  - Arrange: login as employee.
  - Act: POST `/logout`.
  - Assert: unauthenticated, redirected to `/login`.

## Acceptance Criteria
- [ ] Inactive employee is rejected with exact message from AC-11.
- [ ] Admin login redirects to `/admin`.
- [ ] Employee login redirects to `/dashboard`.
- [ ] `GET /dashboard` without auth redirects to `/login`.
- [ ] Logout destroys session and redirects to `/login`.
- [ ] All 5 auth tests pass.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket.
