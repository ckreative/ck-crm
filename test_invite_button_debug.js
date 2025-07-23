import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false, slowMo: 100 });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  // Listen for console messages
  page.on('console', msg => {
    if (msg.type() === 'error') {
      console.log('Browser console error:', msg.text());
    }
  });
  
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
    
    // Check if Alpine is loaded
    const alpineLoaded = await page.evaluate(() => {
      return typeof window.Alpine !== 'undefined';
    });
    console.log('Alpine.js loaded:', alpineLoaded);
    
    // Check if modal exists in DOM
    const modalExists = await page.locator('div[x-data*="show"]').count();
    console.log('Modal components in DOM:', modalExists);
    
    // Try clicking the button
    console.log('\nClicking Invite User button...');
    await page.click('button:has-text("Invite User")');
    
    // Wait a bit
    await page.waitForTimeout(1000);
    
    // Check if modal is visible
    const modalVisible = await page.isVisible('h2:has-text("Invite New User")');
    console.log('Modal visible after click:', modalVisible);
    
    // Try manual dispatch
    console.log('\nTrying manual dispatch...');
    await page.evaluate(() => {
      window.dispatchEvent(new CustomEvent('open-modal', { detail: 'invite-user' }));
    });
    
    await page.waitForTimeout(1000);
    
    const modalVisibleAfterDispatch = await page.isVisible('h2:has-text("Invite New User")');
    console.log('Modal visible after manual dispatch:', modalVisibleAfterDispatch);
    
    // Check Alpine component data
    const alpineData = await page.evaluate(() => {
      const modal = document.querySelector('[x-data*="show"]');
      if (modal && modal.__x) {
        return {
          hasAlpine: true,
          showValue: modal.__x.$data.show
        };
      }
      return { hasAlpine: false };
    });
    console.log('\nAlpine component data:', alpineData);
    
    await page.screenshot({ path: 'test-screenshots/invite-button-debug.png' });
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
})();