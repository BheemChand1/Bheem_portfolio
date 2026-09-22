import { defineConfig } from '@playwright/test';
export default defineConfig({
    testDir: './tests/Browser', fullyParallel: false, workers: 1,
    use: { baseURL: process.env.TEST_URL || 'http://127.0.0.1:8000', browserName: 'chromium', screenshot: 'only-on-failure', trace: 'retain-on-failure' },
    reporter: 'list',
});
