import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1280, height: 720 });
  
  try {
    await page.goto('http://localhost:8000/test-icons');
    await page.waitForLoadState('networkidle');
    
    await page.screenshot({ path: 'test-icons.png', fullPage: true });
    console.log('Test icons screenshot saved as test-icons.png');
    
  } catch (error) {
    console.error('Error:', error);
  } finally {
    await browser.close();
  }
})();