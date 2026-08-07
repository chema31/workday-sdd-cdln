import { defineConfig, devices } from '@playwright/test';

/**
 * The dev server runs with --env=testing so the application reads .env.testing
 * and talks to the fichaccom_e2e database. That keeps migrate:fresh in the
 * specs from ever touching the development database.
 */
export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: false,
    workers: 1,
    reporter: 'list',
    use: {
        baseURL: 'http://localhost:8000',
        headless: true,
        trace: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
    webServer: {
        command: 'php artisan serve --env=testing --port=8000',
        url: 'http://localhost:8000/login',
        reuseExistingServer: !process.env.CI,
        timeout: 60_000,
    },
});
