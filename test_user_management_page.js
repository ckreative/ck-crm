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
    
    // Navigate directly to users page
    console.log('Navigating to users page...');
    await page.goto('http://localhost:8000/app-settings/users');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'users-page-full.png', fullPage: true });
    console.log('Screenshot saved');
    
    // Check what elements are on the page
    const buttons = await page.locator('button').allTextContents();
    console.log('\nButtons found on page:');
    buttons.forEach((btn, idx) => console.log(`  ${idx + 1}. "${btn.trim()}"`));
    
    const links = await page.locator('a').allTextContents();
    console.log('\nLinks found on page:');
    links.forEach((link, idx) => {
      if (link.trim()) console.log(`  ${idx + 1}. "${link.trim()}"`)
    });
    
  } catch (error) {
    console.error('Error:', error);
    await page.screenshot({ path: 'error-screenshot.png' });
  } finally {
    await browser.close();
  }
})();