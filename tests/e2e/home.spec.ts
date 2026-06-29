import { test, expect } from '@playwright/test';

/**
 * Tests E2E para la home de Bicicletería Fagua.
 *
 * Requisitos:
 *   - BASE_URL apuntando a un sitio WordPress con el tema activo
 *   - El sitio debe tener productos en el catálogo WC
 *
 * Ejecutar:
 *   npm run test:e2e
 *   BASE_URL=https://mi-sitio.com npm run test:e2e
 */

test.describe('Home', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/');
    });

    test('carga la home sin errores de consola', async ({ page }) => {
        const errors: string[] = [];
        page.on('pageerror', (err) => errors.push(err.message));
        page.on('console', (msg) => {
            if (msg.type() === 'error') errors.push(msg.text());
        });

        await page.waitForLoadState('networkidle');
        expect(errors).toEqual([]);
    });

    test('muestra el logo FAGUA en el header', async ({ page }) => {
        const logo = page.locator('.bf-logo, header img, header svg').first();
        await expect(logo).toBeVisible();
    });

    test('muestra al menos 6 categorías', async ({ page }) => {
        const cats = page.locator('.bf-cat-card, [class*="category"]');
        const count = await cats.count();
        expect(count).toBeGreaterThanOrEqual(6);
    });

    test('muestra al menos 4 productos', async ({ page }) => {
        const products = page.locator('.bf-product-card, [class*="product"]');
        const count = await products.count();
        expect(count).toBeGreaterThanOrEqual(4);
    });

    test('no tiene scroll horizontal', async ({ page }) => {
        const overflow = await page.evaluate(() => {
            return document.documentElement.scrollWidth > document.documentElement.clientWidth;
        });
        expect(overflow).toBe(false);
    });

    test('CLS razonable (menor a 0.1)', async ({ page }) => {
        // Esperar a que carguen fuentes e imágenes
        await page.waitForLoadState('networkidle');
        const cls = await page.evaluate(() => {
            return new Promise<number>((resolve) => {
                let clsValue = 0;
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        const layoutShift = entry as PerformanceEntry & { value: number; hadRecentInput: boolean };
                        if (!layoutShift.hadRecentInput) {
                            clsValue += layoutShift.value;
                        }
                    }
                });
                try {
                    observer.observe({ type: 'layout-shift', buffered: true });
                } catch {
                    resolve(0);
                    return;
                }
                setTimeout(() => {
                    observer.disconnect();
                    resolve(clsValue);
                }, 1000);
            });
        });
        expect(cls).toBeLessThan(0.1);
    });
});
