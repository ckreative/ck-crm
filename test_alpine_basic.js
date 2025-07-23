import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  await page.setViewportSize({ width: 1440, height: 900 });
  
  try {
    await page.goto('http://localhost:8000/test-alpine');
    await page.waitForLoadState('networkidle');
    
    // Test simple toggle
    console.log('Testing simple Alpine toggle...');
    await page.click('button:has-text("Toggle (Simple)")');
    await page.waitForTimeout(500);
    
    const simpleVisible = await page.isVisible('text="Simple toggle works!"');
    console.log('Simple toggle visible:', simpleVisible);
    
    // Test modal
    console.log('\nTesting modal dispatch...');
    await page.click('button:has-text("Open Modal")');
    await page.waitForTimeout(500);
    
    const modalVisible = await page.isVisible('h2:has-text("Test Modal")');
    console.log('Modal visible:', modalVisible);
    
    await page.screenshot({ path: 'test-screenshots/alpine-test.png' });
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
})();