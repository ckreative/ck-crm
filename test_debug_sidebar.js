import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // Login as admin
    console.log('Logging in as admin...');
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
    console.log('Logged in successfully');
    
    // Debug sidebar visibility
    const sidebarVisible = await page.isVisible('nav');
    console.log('Sidebar visible:', sidebarVisible);
    
    // Try to find App Settings link
    const appSettingsLinks = await page.locator('text=App Settings').count();
    console.log('App Settings links found:', appSettingsLinks);
    
    // Check if user is admin
    const isAdmin = await page.evaluate(() => {
      // This would check if the App Settings link exists in the DOM
      return document.querySelector('a[href*="app-settings"]') !== null;
    });
    console.log('User appears to be admin:', isAdmin);
    
    // Take screenshot
    await page.screenshot({ path: 'debug-sidebar.png' });
    console.log('Screenshot saved as debug-sidebar.png');
    
    // Try alternative navigation methods
    console.log('\nTrying direct navigation to App Settings...');
    await page.goto('http://localhost:8000/app-settings');
    await page.waitForLoadState('networkidle');
    
    const onAppSettings = page.url().includes('app-settings');
    console.log('Successfully navigated to App Settings:', onAppSettings);
    
    if (onAppSettings) {
      await page.screenshot({ path: 'app-settings-direct.png' });
    }
    
  } catch (error) {
    console.error('Error:', error);
    await page.screenshot({ path: 'error-screenshot.png' });
  } finally {
    await browser.close();
  }
})();