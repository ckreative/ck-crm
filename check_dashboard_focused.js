import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false }); // Open browser visually
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
    
    // Wait a bit more for any dynamic content
    await page.waitForTimeout(2000);
    
    // Check page dimensions
    const dimensions = await page.evaluate(() => {
      return {
        bodyHeight: document.body.scrollHeight,
        bodyWidth: document.body.scrollWidth,
        viewportHeight: window.innerHeight,
        viewportWidth: window.innerWidth,
        htmlHeight: document.documentElement.scrollHeight
      };
    });
    console.log('Page dimensions:', dimensions);
    
    // Take screenshot of just the visible viewport
    await page.screenshot({ path: 'dashboard-viewport.png', fullPage: false });
    
    // Check for any errors in console
    page.on('console', msg => console.log('Console:', msg.text()));
    
    console.log('Browser will remain open for 30 seconds for inspection...');
    await page.waitForTimeout(30000);
    
  } catch (error) {
    console.error('Error:', error);
  } finally {
    await browser.close();
  }
})();