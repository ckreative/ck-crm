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
    
    console.log('Testing email sending with Resend...');
    
    // Debug screenshot before clicking
    await page.screenshot({ path: 'test-screenshots/before-click.png' });
    
    // Check if modal is already open
    const modalVisible = await page.isVisible('[x-show="showInviteModal"]');
    if (modalVisible) {
      console.log('Modal already visible, closing it first');
      await page.click('body'); // Click outside to close
      await page.waitForTimeout(500);
    }
    
    // Click invite button
    await page.click('button:has-text("Invite User")');
    await page.waitForTimeout(1000);
    
    // Fill email - use a real email address if you want to receive it
    const testEmail = 'test_resend@example.com'; // Change this to your email to test
    await page.fill('input#email', testEmail);
    
    console.log(`Sending invitation to: ${testEmail}`);
    
    // Submit the form
    await page.click('button:has-text("Send Invitation")');
    await page.waitForLoadState('networkidle');
    
    // Check for success or error
    await page.waitForTimeout(2000);
    
    const hasSuccess = await page.isVisible('text="Successfully saved!"');
    const hasError = await page.isVisible('text="Error"');
    
    if (hasSuccess) {
      console.log('✓ Email sent successfully!');
    } else if (hasError) {
      console.log('❌ Error sending email');
      // Try to get error details
      const errorText = await page.textContent('[role="alert"]');
      console.log('Error details:', errorText);
    } else {
      console.log('⚠ No clear success/error message');
    }
    
    await page.screenshot({ path: 'test-screenshots/resend-test.png' });
    
  } catch (error) {
    console.error('Test error:', error.message);
  } finally {
    await browser.close();
  }
})();