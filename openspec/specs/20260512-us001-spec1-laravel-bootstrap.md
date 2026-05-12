# Spec 1: Laravel 11 Project Bootstrap

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Backend (Laravel Admin Panel)
- **Branch**: feature/20260511_user_management_and_application_bootstrap
- **Estimated effort**: M
- **Depends on**: none (first spec to run)

## Overview
Bootstrap the fresh Laravel 11 application: install Filament 3 for the admin panel, install Laravel Breeze (Blade stack) for the employee frontend, disable all unused Breeze routes, configure environment variables, and create the project README.

## Architecture Context
The project root (`/home/chema/IA Workspace/fichaccom/`) already contains `accom-spec-kit/`, `rules/`, `openspec/`, `scripts/`, and `prompts.md`. Laravel files will be added alongside these. Because `composer create-project` requires an empty directory, we install in a temp location and copy across.

## Implementation Steps

### Step 0: Create feature branch
```bash
git checkout main && git pull
git checkout -b feature/20260511_user_management_and_application_bootstrap
```

### Step 1: Install Laravel 11
```bash
# Install into a temporary directory
composer create-project laravel/laravel /tmp/fichaccom-base "11.*"

# Copy Laravel files into the project root (preserving existing files)
cp -rn /tmp/fichaccom-base/. .

# Clean up
rm -rf /tmp/fichaccom-base
```
- The `-n` flag (no-clobber) prevents overwriting existing files (CLAUDE.md, etc.).

### Step 2: Install Filament 3
```bash
composer require filament/filament:"^3.0" -W
php artisan filament:install --panels
```
- When prompted for a panel ID, enter: `admin`
- This creates `app/Providers/Filament/AdminPanelProvider.php`

### Step 3: Install Laravel Breeze (Blade stack)
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
```

### Step 4: Configure environment variables
- **File**: `.env.example`
- **Action**: Add the three admin variables below the `APP_*` block:
```dotenv
ADMIN_EMAIL=desarrollo@we-accom.com
ADMIN_PASSWORD=
ADMIN_NAME=Administrador
```
- Copy to `.env` and fill in real values for local development.

### Step 5: Disable unused Breeze routes
- **File**: `routes/auth.php`
- **Action**: Comment out or remove every route except login, logout. Remove:
  - Register routes (`/register`)
  - Password reset routes (`/forgot-password`, `/reset-password`)
  - Email verification routes (`/verify-email`, `/email/verification-notification`)
  - Profile routes (`/profile`)
- Keep only:
```php
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
```

### Step 6: Configure `phpunit.xml` for SQLite in-memory testing
- **File**: `phpunit.xml`
- **Action**: Add (or uncomment) the SQLite env vars:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Step 7: Create README.md
- **File**: `README.md` (project root)
- **Content**:
```markdown
# fichaccom

**Application type**: Laravel Admin Panel (monolithic)
**Framework**: Laravel 11.x | PHP 8.2+
**Admin panel**: Filament 3.x (`/admin`)
**Employee frontend**: Blade + Tailwind CSS + Alpine.js (`/login`, `/dashboard`)
**Database**: MySQL (local) / SQLite in-memory (tests)
**Testing**: PHPUnit (`php artisan test`)

## Setup
1. `composer install`
2. `npm install && npm run build`
3. `cp .env.example .env && php artisan key:generate`
4. Configure `.env` (DB credentials + ADMIN_* variables)
5. `php artisan migrate --seed`

## Environment variables
| Variable | Description |
|---|---|
| `ADMIN_EMAIL` | Email of the seeded administrator account |
| `ADMIN_PASSWORD` | Password for the administrator account |
| `ADMIN_NAME` | Display name for the administrator |

## Development
- `php artisan serve` — start the dev server
- `npm run dev` — start Vite asset watcher
- `php artisan test` — run the test suite
```

### Step 8: Run migrations and verify
```bash
php artisan migrate
php artisan test
```
- Base tests (Breeze default) should pass.

### Step LAST: Update Documentation
- `README.md` created in Step 7 — no further changes.
- No `rules/` changes needed; this is project scaffolding.

## Unit Test Specifications (TDD)
No unit tests specific to this spec. Verification is:
1. `php artisan test` passes (default Breeze tests green).
2. `/login` route returns 200.
3. `/admin` route is accessible (Filament installed).

## Acceptance Criteria
- [ ] `composer.json` requires `filament/filament ^3.0` and `laravel/breeze`.
- [ ] `php artisan filament:install --panels` created `AdminPanelProvider.php`.
- [ ] `.env.example` includes `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME`.
- [ ] Unused Breeze routes (register, password reset, profile) are removed from `routes/auth.php`.
- [ ] `phpunit.xml` is configured to use SQLite in-memory.
- [ ] `README.md` exists at project root with application type and setup instructions.
- [ ] `php artisan test` passes.
- [ ] Branch: `feature/20260511_user_management_and_application_bootstrap`.
- [ ] PR created in Bitbucket for review.
