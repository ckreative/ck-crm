import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  // Listen for console errors
  page.on('console', msg => {
    if (msg.type() === 'error') {
      console.log('Browser console error:', msg.text());
    }
  });
  
  // Listen for page errors
  page.on('pageerror', err => {
    console.log('Page error:', err.message);
  });
  
  try {
    console.log('Loading homepage...');
    const response = await page.goto('http://localhost:8000/', { waitUntil: 'networkidle' });
    
    console.log('Response status:', response.status());
    
    // Check if there's an error message on the page
    const errorTexts = [
      'Error', 'Exception', 'Fatal', 'Warning', '500', '404', 'Not Found'
    ];
    
    for (const errorText of errorTexts) {
      const hasError = await page.locator(`text=/${errorText}/i`).count();
      if (hasError > 0) {
        console.log(`Found error text: ${errorText}`);
      }
    }
    
    // Take screenshot
    await page.screenshot({ path: 'test-screenshots/homepage.png', fullPage: true });
    console.log('Screenshot saved');
    
    // Check page title
    const title = await page.title();
    console.log('Page title:', title);
    
    // Check if login link exists
    const hasLogin = await page.isVisible('text="Log in"');
    console.log('Login link visible:', hasLogin);
    
  } catch (error) {
    console.error('Test error:', error.message);
    await page.screenshot({ path: 'test-screenshots/homepage-error.png' });
  } finally {
    await browser.close();
  }
})();