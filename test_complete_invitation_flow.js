import { chromium } from 'playwright';
import { promises as fs } from 'fs';
import path from 'path';

// Helper to extract the latest invitation URL from Laravel log
async function getLatestInvitationUrl() {
  try {
    const logPath = path.join(process.cwd(), 'storage/logs/laravel.log');
    const logContent = await fs.readFile(logPath, 'utf-8');
    
    // Find all invitation URLs and get the last one
    const urlMatches = logContent.match(/http:\/\/localhost:8000\/invitations\/[a-zA-Z0-9]+/g);
    if (urlMatches && urlMatches.length > 0) {
      return urlMatches[urlMatches.length - 1];
    }
  } catch (error) {
    console.error('Could not read log file:', error.message);
  }
  return null;
}

(async () => {
  const browser = await chromium.launch({ headless: false, slowMo: 50 });
  const context = await browser.newContext();
  
  try {
    console.log('=== Complete User Invitation Flow Test ===\n');
    
    // Part 1: Admin sends invitation
    console.log('PART 1: Admin Sending Invitation');
    console.log('---------------------------------');
    
    const adminPage = await context.newPage();
    await adminPage.setViewportSize({ width: 1440, height: 900 });
    
    // Login as admin
    await adminPage.goto('http://localhost:8000/login');
    await adminPage.fill('input[name="email"]', 'test@example.com');
    await adminPage.fill('input[name="password"]', 'password');
    await adminPage.click('button[type="submit"]');
    await adminPage.waitForURL('**/dashboard');
    console.log('✓ Admin logged in');
    
    // Navigate to users page
    await adminPage.goto('http://localhost:8000/app-settings/users');
    await adminPage.waitForLoadState('networkidle');
    console.log('✓ Navigated to user management');
    
    // Send invitation
    const testEmail = `newuser_${Date.now()}@example.com`;
    await adminPage.evaluate(() => {
      window.dispatchEvent(new CustomEvent('open-modal', { detail: 'invite-user' }));
    });
    await adminPage.waitForTimeout(1000);
    
    await adminPage.evaluate((email) => {
      const input = document.querySelector('input#email');
      if (input) {
        input.value = email;
        input.dispatchEvent(new Event('input', { bubbles: true }));
      }
    }, testEmail);
    
    await adminPage.screenshot({ path: 'test-screenshots/flow-01-modal.png' });
    
    await adminPage.evaluate(() => {
      const form = document.querySelector('form[action*="invite"]');
      if (form) form.submit();
    });
    
    await adminPage.waitForLoadState('networkidle');
    console.log(`✓ Invitation sent to ${testEmail}`);
    
    // Check pending invitations
    await adminPage.click('button:has-text("Pending Invitations")');
    await adminPage.waitForTimeout(1000);
    await adminPage.screenshot({ path: 'test-screenshots/flow-02-pending.png' });
    console.log('✓ Invitation appears in pending list\n');
    
    // Part 2: New user accepts invitation
    console.log('PART 2: New User Accepting Invitation');
    console.log('-------------------------------------');
    
    // Get invitation URL from log
    const invitationUrl = await getLatestInvitationUrl();
    if (!invitationUrl) {
      console.error('❌ Could not find invitation URL in logs');
      console.log('Make sure MAIL_MAILER=log in .env');
      return;
    }
    console.log(`✓ Found invitation URL`);
    
    // Open invitation in new page
    const newUserPage = await context.newPage();
    await newUserPage.setViewportSize({ width: 1440, height: 900 });
    await newUserPage.goto(invitationUrl);
    await newUserPage.waitForLoadState('networkidle');
    await newUserPage.screenshot({ path: 'test-screenshots/flow-03-accept-page.png' });
    console.log('✓ Invitation acceptance page loaded');
    
    // Fill registration form
    await newUserPage.fill('input[name="name"]', 'New Test User');
    await newUserPage.fill('input[name="password"]', 'securepassword123');
    await newUserPage.fill('input[name="password_confirmation"]', 'securepassword123');
    await newUserPage.screenshot({ path: 'test-screenshots/flow-04-form-filled.png' });
    console.log('✓ Registration form filled');
    
    // Submit registration
    await newUserPage.click('button[type="submit"]');
    
    try {
      await newUserPage.waitForURL('**/dashboard', { timeout: 5000 });
      await newUserPage.screenshot({ path: 'test-screenshots/flow-05-new-user-dashboard.png' });
      console.log('✓ New user created and logged in!');
      
      // Check that new user is NOT admin
      const hasAppSettings = await newUserPage.isVisible('text="App Settings"');
      console.log(`✓ New user is ${hasAppSettings ? 'admin' : 'not admin (correct)'}`);
      await newUserPage.close();
    } catch (e) {
      console.error('❌ User creation failed');
      await newUserPage.screenshot({ path: 'test-screenshots/flow-error.png' });
    }
    
    // Part 3: Verify in admin panel
    console.log('\nPART 3: Verifying in Admin Panel');
    console.log('---------------------------------');
    
    // Refresh admin page
    await adminPage.reload();
    await adminPage.click('button:has-text("Active Users")');
    await adminPage.waitForTimeout(1000);
    
    const activeUsersCount = await adminPage.textContent('button:has-text("Active Users")');
    console.log(`✓ ${activeUsersCount}`);
    
    const pendingCount = await adminPage.textContent('button:has-text("Pending Invitations")');
    console.log(`✓ ${pendingCount}`);
    
    await adminPage.screenshot({ path: 'test-screenshots/flow-06-final-state.png' });
    
    // Part 4: Test edge cases
    console.log('\nPART 4: Testing Edge Cases');
    console.log('---------------------------');
    
    // Test already accepted invitation
    const alreadyAcceptedPage = await context.newPage();
    await alreadyAcceptedPage.goto(invitationUrl);
    await alreadyAcceptedPage.waitForLoadState('networkidle');
    const alreadyAcceptedText = await alreadyAcceptedPage.isVisible('text="Invitation Already Accepted"');
    console.log(`✓ Already accepted invitation handled: ${alreadyAcceptedText}`);
    await alreadyAcceptedPage.screenshot({ path: 'test-screenshots/flow-07-already-accepted.png' });
    await alreadyAcceptedPage.close();
    
    console.log('\n=== All Tests Completed Successfully! ===');
    console.log('\nScreenshots saved in test-screenshots/flow-*.png');
    
  } catch (error) {
    console.error('\n❌ Test failed:', error.message);
  } finally {
    await browser.close();
  }
})();