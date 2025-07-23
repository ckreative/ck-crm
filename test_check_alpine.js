import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // Login and navigate
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
    
    await page.goto('http://localhost:8000/app-settings/users');
    await page.waitForLoadState('networkidle');
    
    // Check if Alpine is loaded
    const alpineLoaded = await page.evaluate(() => {
      return typeof window.Alpine !== 'undefined';
    });
    console.log('Alpine.js loaded:', alpineLoaded);
    
    // Check if modal dispatch works
    console.log('\nTrying to open modal via dispatch...');
    await page.evaluate(() => {
      // Try to dispatch the event directly
      window.dispatchEvent(new CustomEvent('open-modal', { detail: 'invite-user' }));
    });
    
    await page.waitForTimeout(1000);
    
    // Check modal visibility
    const modalVisible = await page.isVisible('h2:has-text("Invite New User")');
    console.log('Modal visible after dispatch:', modalVisible);
    
    // Try clicking button and check console errors
    console.log('\nClicking invite button...');
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log('Console error:', msg.text());
      }
    });
    
    await page.click('button:has-text("Invite User")');
    await page.waitForTimeout(2000);
    
    const modalVisibleAfterClick = await page.isVisible('h2:has-text("Invite New User")');
    console.log('Modal visible after button click:', modalVisibleAfterClick);
    
    // Check for any JavaScript errors
    const jsErrors = await page.evaluate(() => {
      return window.jsErrors || [];
    });
    console.log('\nJavaScript errors:', jsErrors);
    
    await page.screenshot({ path: 'alpine-debug.png' });
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
})();