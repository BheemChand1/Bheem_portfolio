import { test, expect } from '@playwright/test';
for (const width of [360, 390, 768, 1440, 1920]) {
    test(`public layout fits ${width}px`, async ({ page }) => {
        await page.setViewportSize({ width, height: 900 });
        const errors = []; page.on('pageerror', error => errors.push(error.message));
        await page.goto('/');
        await expect(page.getByRole('heading', { level: 1 })).toContainText('Thoughtful code. Real-world impact.');
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBeTruthy();
        await page.screenshot({ path: `artifacts/home-${width}.png`, fullPage: true });
        await page.getByRole('button', { name: 'Switch to dark mode' }).click();
        await page.reload();
        await expect(page.locator('html')).toHaveAttribute('data-theme', 'dark');
        if (width === 1440) await page.screenshot({ path: 'artifacts/home-dark.png', fullPage: true });
        if (width < 761) { await page.getByRole('button', { name: 'Toggle navigation' }).click(); await expect(page.locator('#navigation')).toBeVisible(); }
        expect(errors).toEqual([]);
    });
}
test('project filter and details work', async ({ page }) => {
    await page.goto('/projects');
    await page.getByRole('link', { name: 'React.js', exact: true }).click();
    await expect(page.locator('.project-card')).toHaveCount(1);
    await page.getByRole('heading', { name: 'OBHS Feedback & Attendance Management' }).getByRole('link').click();
    await expect(page.getByRole('heading', { name: 'What it does' })).toBeVisible();
});
test('chat dialog handles unavailable backend and resets', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('button', { name: /Ask about Bheem/ }).click();
    await expect(page.getByRole('dialog')).toBeVisible();
    await page.getByRole('button', { name: 'What are his backend skills?' }).click();
    await expect(page.locator('#chat-messages')).toContainText('currently unavailable');
    await page.getByRole('button', { name: 'Reset conversation' }).click();
    await expect(page.locator('#chat-messages')).toBeEmpty();
    await page.keyboard.press('Escape');
    await expect(page.getByRole('dialog')).not.toBeVisible();
    await expect(page.getByRole('button', { name: /Ask about Bheem/ })).toBeFocused();
});
test('real browser contact validation and success', async ({ page }) => {
    await page.goto('/contact');
    await page.getByLabel('Your name').fill('Browser verification');
    await page.getByLabel('Email address').fill('browser-check@example.test');
    await page.getByLabel('What would you like to build?').fill('This is an automated browser verification of the portfolio contact form.');
    await page.waitForTimeout(3100);
    await page.getByRole('button', { name: 'Send message' }).click();
    await expect(page.getByRole('status')).toContainText('Your message has been received');
});
test('CSRF protection rejects an untrusted POST', async ({ request }) => {
    const response = await request.post('/contact', { data: { name: 'No token', email: 'check@example.test', message: 'A request without a CSRF token should fail.' } });
    expect(response.status()).toBe(419);
});
