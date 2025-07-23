import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false, slowMo: 100 });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // Login
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
    
    // Go directly to users page
    await page.goto('http://localhost:8000/app-settings/users');
    await page.waitForLoadState('networkidle');
    
    // Manually open modal using JavaScript
    console.log('Opening modal...');
    await page.evaluate(() => {
      window.dispatchEvent(new CustomEvent('open-modal', { detail: 'invite-user' }));
    });
    
    // Wait a bit for animation
    await page.waitForTimeout(1000);
    
    // Now fill the form
    const testEmail = `manual_test_${Date.now()}@example.com`;
    console.log(`Filling form with: ${testEmail}`);
    
    // Force fill the input even if not fully visible
    await page.evaluate((email) => {
      const input = document.querySelector('input#email');
      if (input) {
        input.value = email;
        input.dispatchEvent(new Event('input', { bubbles: true }));
      }
    }, testEmail);
    
    await page.screenshot({ path: 'test-screenshots/manual-modal-filled.png', fullPage: true });
    
    // Submit the form
    console.log('Submitting form...');
    await page.evaluate(() => {
      const form = document.querySelector('form[action*="invite"]');
      if (form) {
        form.submit();
      }
    });
    
    // Wait for page reload
    await page.waitForLoadState('networkidle');
    console.log('Form submitted');
    
    // Check result
    await page.screenshot({ path: 'test-screenshots/manual-after-submit.png' });
    
    // Check pending invitations
    await page.click('button:has-text("Pending Invitations")');
    await page.waitForTimeout(1000);
    
    const hasPending = await page.isVisible(`text="${testEmail}"`);
    console.log(`\nInvitation visible in pending list: ${hasPending}`);
    
    await page.screenshot({ path: 'test-screenshots/manual-pending-list.png' });
    
    console.log('\nManual test complete!');
    
  } catch (error) {
    console.error('Error:', error.message);
    await page.screenshot({ path: 'test-screenshots/manual-error.png' });
  } finally {
    await browser.close();
  }
})();