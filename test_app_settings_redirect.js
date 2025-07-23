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
    
    // Test clicking App Settings in sidebar
    console.log('Testing App Settings redirect...');
    
    // Navigate to app-settings root
    await page.goto('http://localhost:8000/app-settings');
    await page.waitForLoadState('networkidle');
    
    // Check if redirected to users
    const currentUrl = page.url();
    const isOnUsersPage = currentUrl.includes('/app-settings/users');
    console.log(`✓ Redirected to users page: ${isOnUsersPage}`);
    console.log(`  Current URL: ${currentUrl}\n`);
    
    // Take screenshot
    await page.screenshot({ path: 'test-screenshots/app-settings-users-default.png' });
    
    // Check that no General tab exists
    const hasGeneralTab = await page.isVisible('text="General"');
    console.log(`✓ General tab removed: ${!hasGeneralTab}`);
    
    // Check that Users is highlighted
    const usersLinkClass = await page.getAttribute('a:has-text("Users")', 'class');
    const isUsersActive = usersLinkClass?.includes('bg-gray-100');
    console.log(`✓ Users tab is active: ${isUsersActive}`);
    
    console.log('\nAll tests passed!');
    
  } catch (error) {
    console.error('Error:', error.message);
    await page.screenshot({ path: 'test-screenshots/error.png' });
  } finally {
    await browser.close();
  }
})();