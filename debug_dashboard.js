import { chromium } from 'playwright';
import fs from 'fs';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // Login
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Wait for dashboard
    await page.waitForURL('**/dashboard', { timeout: 10000 });
    await page.waitForLoadState('networkidle');
    
    // Save the full HTML
    const html = await page.content();
    fs.writeFileSync('dashboard-output.html', html);
    console.log('Full HTML saved to dashboard-output.html');
    
    // Check for SVG elements
    const svgs = await page.$$('svg');
    console.log(`Found ${svgs.length} SVG elements`);
    
    // Get computed styles of body
    const bodyStyles = await page.evaluate(() => {
      const body = document.body;
      const computed = window.getComputedStyle(body);
      return {
        height: computed.height,
        overflow: computed.overflow,
        position: computed.position
      };
    });
    console.log('Body styles:', bodyStyles);
    
    // Check if Alpine.js is loaded
    const alpineLoaded = await page.evaluate(() => typeof window.Alpine !== 'undefined');
    console.log('Alpine.js loaded:', alpineLoaded);
    
  } catch (error) {
    console.error('Error:', error);
  } finally {
    await browser.close();
  }
})();