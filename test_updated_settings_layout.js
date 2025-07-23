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
    console.log('✓ Logged in successfully\n');
    
    // Navigate to app settings
    console.log('Navigating to App Settings...');
    await page.goto('http://localhost:8000/app-settings');
    await page.waitForLoadState('networkidle');
    
    // Should redirect to users
    const currentUrl = page.url();
    console.log(`Current URL: ${currentUrl}`);
    console.log(`✓ On users page: ${currentUrl.includes('/users')}\n`);
    
    // Take screenshot of new layout
    await page.screenshot({ path: 'test-screenshots/new-settings-layout.png', fullPage: true });
    console.log('✓ Screenshot saved\n');
    
    // Check for new navigation style
    const hasSecondaryNav = await page.isVisible('nav:has-text("Users")');
    console.log(`✓ Secondary navigation present: ${hasSecondaryNav}`);
    
    // Check header structure
    const hasPageHeader = await page.isVisible('h2:has-text("User Management")');
    console.log(`✓ Page header with title: ${hasPageHeader}`);
    
    // Check modern table styling
    const hasModernTable = await page.locator('.ring-1').count() > 0;
    console.log(`✓ Modern table styling applied: ${hasModernTable}`);
    
    console.log('\nLayout update test complete!');
    
  } catch (error) {
    console.error('Error:', error.message);
    await page.screenshot({ path: 'test-screenshots/error.png' });
  } finally {
    await browser.close();
  }
})();