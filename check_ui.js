import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  // Set viewport to desktop size
  await page.setViewportSize({ width: 1280, height: 720 });
  
  try {
    // Go to the Laravel app
    await page.goto('http://localhost:8000');
    await page.waitForLoadState('networkidle');
    
    // Take screenshot of the homepage
    await page.screenshot({ path: 'homepage.png', fullPage: true });
    console.log('Homepage screenshot saved as homepage.png');
    
    // Try to navigate to login if not already logged in
    const loginLink = await page.$('a[href*="login"]');
    if (loginLink) {
      await loginLink.click();
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: 'login.png', fullPage: true });
      console.log('Login page screenshot saved as login.png');
    }
    
  } catch (error) {
    console.error('Error:', error);
  } finally {
    await browser.close();
  }
})();