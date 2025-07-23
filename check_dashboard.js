import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  // Set viewport to desktop size
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    // First, let's create a test user using artisan tinker
    console.log('Creating test user...');
    
    // Go to login page
    await page.goto('http://localhost:8000/login');
    await page.waitForLoadState('networkidle');
    
    // Fill in login form with test credentials
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    
    // Click login button
    await page.click('button[type="submit"]');
    
    // Wait for navigation to dashboard
    await page.waitForURL('**/dashboard', { timeout: 10000 });
    await page.waitForLoadState('networkidle');
    
    // Take screenshot of dashboard
    await page.screenshot({ path: 'dashboard.png', fullPage: true });
    console.log('Dashboard screenshot saved as dashboard.png');
    
    // Also take a mobile view
    await page.setViewportSize({ width: 375, height: 812 });
    await page.screenshot({ path: 'dashboard-mobile.png', fullPage: true });
    console.log('Mobile dashboard screenshot saved as dashboard-mobile.png');
    
  } catch (error) {
    console.error('Error:', error);
    // Take error screenshot
    await page.screenshot({ path: 'error.png', fullPage: true });
  } finally {
    await browser.close();
  }
})();