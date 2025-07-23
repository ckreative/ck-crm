import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // Login as admin
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
    
    // Navigate to app settings
    await page.goto('http://localhost:8000/app-settings/users');
    await page.waitForLoadState('networkidle');
    
    // Take screenshot of active users tab
    await page.screenshot({ path: 'test-screenshots/settings-active-users.png' });
    console.log('✓ Active Users tab screenshot saved');
    
    // Click on Pending Invitations tab
    await page.click('button:has-text("Pending Invitations")');
    await page.waitForTimeout(500);
    
    // Take screenshot of pending invitations tab
    await page.screenshot({ path: 'test-screenshots/settings-pending-invitations.png' });
    console.log('✓ Pending Invitations tab screenshot saved');
    
    console.log('\nTab navigation test complete!');
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
})();