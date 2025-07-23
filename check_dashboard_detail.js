import { chromium } from 'playwright';

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
    
    // Get the page HTML
    const html = await page.content();
    console.log('=== First 500 chars of HTML ===');
    console.log(html.substring(0, 500));
    
    // Check if sidebar exists
    const sidebar = await page.$('.lg\\:w-72');
    if (sidebar) {
      console.log('Sidebar found!');
      const sidebarBox = await sidebar.boundingBox();
      console.log('Sidebar dimensions:', sidebarBox);
    } else {
      console.log('Sidebar NOT found!');
    }
    
    // Check for any heroicons
    const heroicons = await page.$$('[class*="heroicon"]');
    console.log(`Found ${heroicons.length} heroicon elements`);
    
    // Take full screenshot
    await page.screenshot({ path: 'dashboard-detail.png', fullPage: false });
    
    // Scroll down a bit and take another
    await page.evaluate(() => window.scrollBy(0, 300));
    await page.screenshot({ path: 'dashboard-detail-scrolled.png', fullPage: false });
    
  } catch (error) {
    console.error('Error:', error);
  } finally {
    await browser.close();
  }
})();