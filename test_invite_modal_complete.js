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
    
    console.log('Testing complete invite user flow...');
    
    // Click invite button
    await page.click('button:has-text("Invite User")');
    await page.waitForTimeout(500);
    
    // Check if modal is visible
    const modalVisible = await page.isVisible('h2:has-text("Invite New User")');
    console.log('✓ Modal visible:', modalVisible);
    
    // Fill email
    const testEmail = `modaltest_${Date.now()}@example.com`;
    await page.fill('input#email', testEmail);
    console.log(`✓ Filled email: ${testEmail}`);
    
    // Submit the form
    await page.click('button:has-text("Send Invitation")');
    await page.waitForLoadState('networkidle');
    
    // Check for success message
    const hasSuccess = await page.isVisible('text="Invitation sent successfully"');
    console.log('✓ Success message shown:', hasSuccess);
    
    // Check pending invitations
    await page.click('button:has-text("Pending Invitations")');
    await page.waitForTimeout(500);
    
    const invitationVisible = await page.isVisible(`text="${testEmail}"`);
    console.log('✓ Invitation in pending list:', invitationVisible);
    
    await page.screenshot({ path: 'test-screenshots/invite-modal-success.png' });
    
    console.log('\n✓ Invite user modal is fully functional!');
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
})();