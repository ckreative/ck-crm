import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: false, slowMo: 100 });
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
    
    // Wait for Alpine to be ready
    await page.waitForFunction(() => window.Alpine !== undefined);
    
    // Try to open modal using Alpine directly
    console.log('Opening modal using Alpine directly...');
    const opened = await page.evaluate(() => {
      // Find all Alpine components
      const components = document.querySelectorAll('[x-data]');
      let modalComponent = null;
      
      for (const comp of components) {
        if (comp.__x && comp.__x.$data && 'show' in comp.__x.$data) {
          modalComponent = comp;
          break;
        }
      }
      
      if (modalComponent && modalComponent.__x) {
        modalComponent.__x.$data.show = true;
        return true;
      }
      
      return false;
    });
    
    console.log('Modal opened via Alpine:', opened);
    
    await page.waitForTimeout(1000);
    await page.screenshot({ path: 'test-screenshots/modal-alpine-direct.png' });
    
    // Check visibility
    const modalVisible = await page.isVisible('h2:has-text("Invite New User")');
    console.log('Modal visible:', modalVisible);
    
    // Now test the normal button click after refresh
    console.log('\nRefreshing and testing button click...');
    await page.reload();
    await page.waitForLoadState('networkidle');
    await page.waitForFunction(() => window.Alpine !== undefined);
    
    // Wait a bit more for Alpine to initialize
    await page.waitForTimeout(500);
    
    // Click the button
    await page.click('button:has-text("Invite User")');
    await page.waitForTimeout(1000);
    
    const modalVisibleAfterClick = await page.isVisible('h2:has-text("Invite New User")');
    console.log('Modal visible after button click:', modalVisibleAfterClick);
    
    await page.screenshot({ path: 'test-screenshots/modal-button-click.png' });
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
})();