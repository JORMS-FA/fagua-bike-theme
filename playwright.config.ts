import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright config para Bicicletería Fagua theme.
 *
 * La URL objetivo (BASE_URL) se define por variable de entorno.
 * Por defecto apunta al LocalWP local.
 */
const BASE_URL = process.env.BASE_URL || 'http://bicicleteriafagua.local:10016';

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: process.env.CI
        ? [['html'], ['github']]
        : [['list'], ['html', { open: 'never' }]],

    use: {
        baseURL: BASE_URL,
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
        // Timeouts
        actionTimeout: 10_000,
        navigationTimeout: 30_000,
    },

    projects: [
        {
            name: 'desktop-chrome',
            use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 } },
        },
        {
            name: 'tablet-ipad',
            use: { ...devices['iPad (gen 7)'] },
        },
        {
            name: 'mobile-iphone',
            use: { ...devices['iPhone 13'] },
        },
    ],

    // No iniciar servidor web local (usar LocalWP del desarrollador o URL externa)
    webServer: undefined,
});
