import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

const ADMIN_EMAIL = process.env.ADMIN_EMAIL ?? 'desarrollo@we-accom.com';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD ?? 'Admin1234';

/**
 * --env=testing keeps this pointed at the fichaccom_e2e database, never the
 * development one. --seeder is required: a bare --seed would run
 * DatabaseSeeder, which does not create the E2E employees.
 */
test.beforeEach(() => {
    execSync('php artisan migrate:fresh --seed --seeder=E2ESeeder --env=testing', {
        stdio: 'pipe',
    });
});

/** The login form is in Spanish, per Spec 12. */
async function login(page, email: string, password: string) {
    await page.goto('/login');
    await page.getByLabel('Correo electrónico').fill(email);
    await page.getByLabel('Contraseña').fill(password);
    await page.getByRole('button', { name: 'Iniciar sesión' }).click();
}

test.describe('US-001 — User Management', () => {
    test('employee login navigates to dashboard', async ({ page }) => {
        await login(page, 'employee@test.com', 'Employee1');

        await expect(page).toHaveURL('/dashboard');
        // Covers all three greetings: ¡Buenos días / ¡Buenas tardes / ¡Buenas noches
        await expect(page.getByText(/¡Buen(os|as)/)).toBeVisible();
        await expect(page.getByText('Fichaje')).toBeVisible();
        await expect(page.getByText('Registros del mes actual')).toBeVisible();
    });

    test('inactive employee is blocked at login', async ({ page }) => {
        await login(page, 'inactive@test.com', 'Employee1');

        await expect(page).toHaveURL('/login');
        await expect(
            page.getByText('Your account has been deactivated. Contact the administrator.')
        ).toBeVisible();
    });

    test('admin login redirects to Filament admin panel', async ({ page }) => {
        await login(page, ADMIN_EMAIL, ADMIN_PASSWORD);

        await expect(page).toHaveURL(/\/admin/);
        await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible();
    });

    test('admin can create and edit an employee, and never sees the protected admin', async ({
        page,
    }) => {
        await login(page, ADMIN_EMAIL, ADMIN_PASSWORD);
        await expect(page).toHaveURL(/\/admin/);

        await page.goto('/admin/employees');

        // The protected admin account is excluded from the employee list.
        await expect(page.getByText(ADMIN_EMAIL)).toHaveCount(0);

        await page.getByRole('link', { name: 'New Employee' }).click();
        await page.getByLabel('Name').fill('Test Employee E2E');
        await page.getByLabel('Email').fill('test-e2e@example.com');
        await page.getByLabel('Password').fill('TestPass1');
        await page.getByRole('button', { name: 'Create', exact: true }).click();

        // Wait for the write to finish before navigating, otherwise the list is
        // fetched while the request is still in flight.
        await expect(page.getByText('Created')).toBeVisible();

        await page.goto('/admin/employees');
        await expect(page.getByText('Test Employee E2E')).toBeVisible();

        await page.getByRole('link', { name: 'Edit' }).first().click();
        await page.getByLabel('Name').fill('Test Employee Updated');
        await page.getByRole('button', { name: 'Save changes' }).click();

        await expect(page.getByText('Saved')).toBeVisible();

        await page.goto('/admin/employees');
        await expect(page.getByText('Test Employee Updated')).toBeVisible();
        await expect(page.getByText(ADMIN_EMAIL)).toHaveCount(0);
    });
});
