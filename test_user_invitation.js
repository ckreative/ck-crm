import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // Login as admin
    console.log('Logging in as admin...');
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Wait for dashboard
    await page.waitForURL('**/dashboard');
    console.log('Logged in successfully');
    
    // Take dashboard screenshot
    await page.screenshot({ path: 'dashboard-with-app-settings.png' });
    console.log('Dashboard screenshot saved');
    
    // Navigate to App Settings
    await page.click('text=App Settings');
    await page.waitForURL('**/app-settings');
    await page.screenshot({ path: 'app-settings-general.png' });
    console.log('App Settings General page screenshot saved');
    
    // Navigate to Users
    await page.click('text=Users');
    await page.waitForURL('**/app-settings/users');
    await page.screenshot({ path: 'app-settings-users.png' });
    console.log('Users page screenshot saved');
    
    // Click invite user button
    await page.click('text=Invite User');
    await page.waitForTimeout(500); // Wait for modal
    await page.screenshot({ path: 'invite-user-modal.png' });
    console.log('Invite user modal screenshot saved');
    
  } catch (error) {
    console.error('Error:', error);
    await page.screenshot({ path: 'error-screenshot.png' });
  } finally {
    await browser.close();
  }
})();