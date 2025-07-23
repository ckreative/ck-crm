import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false, slowMo: 100 });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // Step 1: Admin login
    console.log('1. Logging in as admin...');
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
    console.log('✓ Admin logged in\n');
    
    // Step 2: Navigate to users page
    console.log('2. Navigating to users page...');
    await page.goto('http://localhost:8000/app-settings/users');
    await page.waitForLoadState('networkidle');
    console.log('✓ On users page\n');
    
    // Step 3: Click invite button and wait for modal
    console.log('3. Opening invite modal...');
    await page.click('button:has-text("Invite User")');
    
    // Wait for modal to be visible
    await page.waitForSelector('h2:has-text("Invite New User")', { state: 'visible', timeout: 5000 });
    console.log('✓ Modal opened\n');
    
    // Step 4: Fill form and send invitation
    console.log('4. Sending invitation...');
    const testEmail = `test_${Date.now()}@example.com`;
    await page.fill('input#email', testEmail);
    await page.screenshot({ path: 'test-screenshots/modal-filled.png' });
    
    // Click the send button in the modal
    await page.click('button:has-text("Send Invitation")');
    
    // Wait for page reload or success message
    await page.waitForLoadState('networkidle');
    console.log(`✓ Invitation sent to ${testEmail}\n`);
    
    // Step 5: Check pending invitations
    console.log('5. Checking pending invitations...');
    await page.click('button:has-text("Pending Invitations")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: 'test-screenshots/pending-invitations.png' });
    
    // Check if invitation appears
    const pendingEmail = await page.isVisible(`text="${testEmail}"`);
    if (pendingEmail) {
      console.log('✓ Invitation appears in pending list\n');
    } else {
      console.log('⚠ Invitation not found in pending list\n');
    }
    
    console.log('=== Basic Test Complete ===');
    
  } catch (error) {
    console.error('\n❌ Error:', error.message);
    await page.screenshot({ path: 'test-screenshots/error.png' });
  } finally {
    await browser.close();
  }
})();