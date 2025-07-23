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
    
    console.log('Testing invite user modal...');
    
    // Click invite button
    await page.click('button:has-text("Invite User")');
    await page.waitForTimeout(500);
    
    // Check if modal is visible
    const modalVisible = await page.isVisible('h2:has-text("Invite New User")');
    console.log('✓ Modal visible:', modalVisible);
    
    if (modalVisible) {
      // Fill email
      await page.fill('input#email', 'testmodal@example.com');
      await page.screenshot({ path: 'test-screenshots/modal-working.png' });
      console.log('✓ Modal screenshot saved');
      
      // Test cancel button
      await page.click('button:has-text("Cancel")');
      await page.waitForTimeout(500);
      
      const modalHidden = !(await page.isVisible('h2:has-text("Invite New User")'))
      console.log('✓ Modal hidden after cancel:', modalHidden);
    }
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
})();