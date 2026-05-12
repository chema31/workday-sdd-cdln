# Spec: Playwright Functional Tests (US-001)

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Frontend (E2E — Playwright)
- **Branch**: feature/20260511_user_management_and_application_bootstrap
- **Estimated effort**: M
- **Depends on**: All specs 0–12 (full app must be running)

## Overview
Implement four end-to-end Playwright test scenarios covering the main user flows of US-001. Tests run against the local Laravel dev server (`php artisan serve`).

## Architecture Context
- Test file: `tests/e2e/user-management.spec.ts`
- Playwright config: `playwright.config.ts` (project root)
- Base URL: `http://localhost:8000`
- Per-test setup: seed the database via `php artisan db:seed` or direct API calls to create known users
- Language: TypeScript

## Implementation Steps

### Step 0: Install Playwright
```bash
npm init playwright@latest
# Choose: TypeScript, tests/e2e directory, add GitHub Actions: no
```
Creates `playwright.config.ts` and `tests/e2e/` directory.

### Step 1: Configure `playwright.config.ts`
```ts
import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    use: {
        baseURL: 'http://localhost:8000',
        headless: true,
    },
    webServer: {
        command: 'php artisan serve',
        url: 'http://localhost:8000',
        reuseExistingServer: !process.env.CI,
    },
});
```

### Step 2: Create test helper for user seeding
- **File**: `tests/e2e/helpers/seed.ts`
- Use `execSync` to run `php artisan tinker --execute="..."` or define Artisan routes for testing only.
- Simpler approach: use a dedicated test-seeding Artisan command `php artisan db:seed --class=E2ESeeder`.

### Step 3: Create `tests/e2e/user-management.spec.ts`
Write the following four test scenarios:

```ts
import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

test.beforeEach(() => {
    execSync('php artisan migrate:fresh --seed --env=testing');
});

test.describe('US-001 — User Management', () => {

    test('employee login navigates to dashboard', async ({ page }) => {
        // Arrange: employee created by seeder (email: employee@test.com, pw: Employee1)
        await page.goto('/login');
        await page.getByLabel('Email').fill('employee@test.com');
        await page.getByLabel('Password').fill('Employee1');
        await page.getByRole('button', { name: 'Iniciar sesión' }).click();

        await expect(page).toHaveURL('/dashboard');
        await expect(page.getByText(/Buenas/)).toBeVisible();
        await expect(page.getByText('Fichaje')).toBeVisible();
    });

    test('inactive employee is blocked at login', async ({ page }) => {
        // Arrange: inactive employee created by seeder (email: inactive@test.com)
        await page.goto('/login');
        await page.getByLabel('Email').fill('inactive@test.com');
        await page.getByLabel('Password').fill('Employee1');
        await page.getByRole('button', { name: 'Iniciar sesión' }).click();

        await expect(page).toHaveURL('/login');
        await expect(page.getByText('Your account has been deactivated. Contact the administrator.')).toBeVisible();
    });

    test('admin login redirects to Filament admin panel', async ({ page }) => {
        // Arrange: admin created by AdminSeeder
        await page.goto('/login');
        await page.getByLabel('Email').fill(process.env.ADMIN_EMAIL ?? 'desarrollo@we-accom.com');
        await page.getByLabel('Password').fill(process.env.ADMIN_PASSWORD ?? 'Admin1234');
        await page.getByRole('button', { name: 'Iniciar sesión' }).click();

        await expect(page).toHaveURL(/\/admin/);
        await expect(page.getByText('Dashboard')).toBeVisible(); // Filament admin dashboard
    });

    test('admin can create, edit and cannot delete protected admin via Filament', async ({ page }) => {
        // Login as admin
        await page.goto('/login');
        await page.getByLabel('Email').fill(process.env.ADMIN_EMAIL ?? 'desarrollo@we-accom.com');
        await page.getByLabel('Password').fill(process.env.ADMIN_PASSWORD ?? 'Admin1234');
        await page.getByRole('button', { name: 'Iniciar sesión' }).click();
        await expect(page).toHaveURL(/\/admin/);

        // Navigate to employee list
        await page.goto('/admin/employees');
        await expect(page).toHaveURL(/\/admin\/employees/);

        // Create employee
        await page.getByRole('link', { name: 'New employee' }).click();
        await page.getByLabel('Name').fill('Test Employee');
        await page.getByLabel('Email').fill('test-e2e@example.com');
        await page.getByLabel('Password').fill('TestPass1');
        await page.getByRole('button', { name: 'Create' }).click();
        await expect(page.getByText('Test Employee')).toBeVisible();

        // Edit employee
        await page.getByRole('link', { name: 'Edit' }).first().click();
        await page.getByLabel('Name').fill('Test Employee Updated');
        await page.getByRole('button', { name: 'Save changes' }).click();
        await expect(page.getByText('Test Employee Updated')).toBeVisible();

        // Attempt to delete the protected admin — admin should NOT appear in list
        // Confirm admin email is not visible in the employee table
        await page.goto('/admin/employees');
        await expect(page.getByText(process.env.ADMIN_EMAIL ?? 'desarrollo@we-accom.com')).not.toBeVisible();
    });

});
```

### Step 4: Create E2E seeder
- **File**: `database/seeders/E2ESeeder.php`
```php
class E2ESeeder extends Seeder
{
    public function run(): void
    {
        // Admin (seeded by AdminSeeder reading from config)
        $this->call(AdminSeeder::class);

        // Active employee for E2E
        User::firstOrCreate(
            ['email' => 'employee@test.com'],
            ['name' => 'Test Employee', 'password' => Hash::make('Employee1'),
             'role' => UserRole::Employee, 'is_active' => true]
        );

        // Inactive employee for E2E
        User::firstOrCreate(
            ['email' => 'inactive@test.com'],
            ['name' => 'Inactive Employee', 'password' => Hash::make('Employee1'),
             'role' => UserRole::Employee, 'is_active' => false]
        );
    }
}
```

### Step 5: Run Playwright tests
```bash
npx playwright test
```
All 4 tests must pass.

### Step LAST: Update Documentation
- Add to `README.md`:
```markdown
## E2E Tests
- Install: `npm init playwright@latest` (first time)
- Run: `npx playwright test`
- Requires: `php artisan serve` running (auto-started by Playwright config)
```

## Acceptance Criteria
- [ ] `playwright.config.ts` exists and configures `baseURL = http://localhost:8000`.
- [ ] E2ESeeder creates the test users idempotently.
- [ ] All 4 Playwright scenarios pass (`npx playwright test`).
- [ ] Tests use user-visible locators (role, label, text) — no CSS selectors.
- [ ] No `waitForTimeout` calls.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket.
