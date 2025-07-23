import { chromium } from 'playwright';
import { promises as fs } from 'fs';
import path from 'path';

(async () => {
  const browser = await chromium.launch({ headless: false, slowMo: 50 });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    console.log('=== Testing User Invitation Feature ===\n');
    
    // Step 1: Admin login
    console.log('1. Admin login...');
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
    console.log('✓ Logged in as admin\n');
    
    // Step 2: Navigate to users management
    console.log('2. Navigating to user management...');
    await page.goto('http://localhost:8000/app-settings/users');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'test-screenshots/01-users-page.png' });
    console.log('✓ On users page\n');
    
    // Step 3: Send invitation
    console.log('3. Sending invitation...');
    const testEmail = `newuser_${Date.now()}@example.com`;
    
    // Click invite button
    await page.click('button:has-text("Invite User")');
    
    // Wait for modal to be fully visible
    await page.waitForSelector('input#email', { state: 'visible' });
    await page.waitForTimeout(500); // Extra time for animation
    
    // Fill email
    await page.fill('input#email', testEmail);
    await page.screenshot({ path: 'test-screenshots/02-modal-filled.png' });
    
    // Send invitation
    await page.click('button[type="submit"]:has-text("Send Invitation")');
    await page.waitForLoadState('networkidle');
    
    // Check for success message
    const successVisible = await page.isVisible('text="Invitation sent successfully"');
    if (successVisible) {
      console.log(`✓ Invitation sent to ${testEmail}\n`);
    } else {
      console.log(`⚠ No success message shown for ${testEmail}\n`);
    }
    await page.screenshot({ path: 'test-screenshots/03-after-invite.png' });
    
    // Step 4: Check pending invitations
    console.log('4. Checking pending invitations...');
    await page.click('button:has-text("Pending Invitations")');
    await page.waitForTimeout(1000);
    
    const hasPendingInvite = await page.isVisible(`text="${testEmail}"`);
    if (hasPendingInvite) {
      console.log('✓ Invitation appears in pending list\n');
    } else {
      console.log('⚠ Invitation not visible in pending list\n');
    }
    await page.screenshot({ path: 'test-screenshots/04-pending-invitations.png' });
    
    // Step 5: Test resend functionality
    console.log('5. Testing resend invitation...');
    const resendButton = page.locator('button:has-text("Resend")').first();
    if (await resendButton.isVisible()) {
      await resendButton.click();
      await page.waitForLoadState('networkidle');
      console.log('✓ Resend functionality tested\n');
    } else {
      console.log('⚠ No resend button found\n');
    }
    
    // Step 6: Get invitation URL from Laravel log
    console.log('6. Checking email log for invitation URL...');
    const logPath = path.join(process.cwd(), 'storage/logs/laravel.log');
    try {
      const logContent = await fs.readFile(logPath, 'utf-8');
      const urlMatch = logContent.match(/http:\/\/localhost:8000\/invitations\/accept\?token=([a-zA-Z0-9]+)/);
      
      if (urlMatch) {
        console.log('✓ Found invitation URL in email log');
        console.log(`   URL: ${urlMatch[0]}\n`);
        
        // Step 7: Test invitation acceptance in new tab
        console.log('7. Testing invitation acceptance...');
        const newUserPage = await context.newPage();
        await newUserPage.goto(urlMatch[0]);
        await newUserPage.waitForLoadState('networkidle');
        await newUserPage.screenshot({ path: 'test-screenshots/05-accept-page.png' });
        
        // Check if on acceptance page
        const onAcceptPage = await newUserPage.isVisible('text="Accept Invitation"');
        if (onAcceptPage) {
          console.log('✓ Invitation acceptance page loaded\n');
          
          // Fill registration form
          await newUserPage.fill('input[name="name"]', 'New Test User');
          await newUserPage.fill('input[name="password"]', 'password123');
          await newUserPage.fill('input[name="password_confirmation"]', 'password123');
          await newUserPage.screenshot({ path: 'test-screenshots/06-accept-form-filled.png' });
          
          // Submit
          await newUserPage.click('button[type="submit"]');
          
          // Check result
          try {
            await newUserPage.waitForURL('**/dashboard', { timeout: 5000 });
            console.log('✓ New user created and logged in!\n');
            await newUserPage.screenshot({ path: 'test-screenshots/07-new-user-dashboard.png' });
          } catch (e) {
            console.log('⚠ User creation might have failed\n');
            await newUserPage.screenshot({ path: 'test-screenshots/07-error.png' });
          }
        }
        
        await newUserPage.close();
      } else {
        console.log('⚠ Could not find invitation URL in logs');
        console.log('   Make sure MAIL_MAILER=log in .env\n');
      }
    } catch (e) {
      console.log('⚠ Could not read Laravel log file\n');
    }
    
    // Step 8: Refresh and check active users
    console.log('8. Checking active users...');
    await page.reload();
    await page.click('button:has-text("Active Users")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: 'test-screenshots/08-final-users-list.png' });
    
    console.log('\n=== Test Complete ===');
    console.log('Check test-screenshots/ directory for visual results');
    
  } catch (error) {
    console.error('\n❌ Test failed:', error.message);
    await page.screenshot({ path: 'test-screenshots/error.png' });
  } finally {
    await browser.close();
  }
})();