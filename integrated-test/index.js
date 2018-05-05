'use strict';

const puppeteer = require('puppeteer');
const devices = require('puppeteer/DeviceDescriptors');

(async() => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  await page.goto('http://127.0.0.1:4000/');
  // iPhone 5
  await page.emulate(devices['iPhone 5']);
  await page.screenshot({path: 'screenshots/iPhone-5.png', fullPage: true});
  // iPhone 6/7/8
  await page.emulate(devices['iPhone 6']);
  await page.screenshot({path: 'screenshots/iPhone-6.png', fullPage: true});
  // iPhone 6/7/8 Plus
  await page.emulate(devices['iPhone 6 Plus']);
  await page.screenshot({path: 'screenshots/iPhone-6-plus.png', fullPage: true});
  // iPhone X
  await page.emulate(devices['iPhone X']);
  await page.screenshot({path: 'screenshots/iPhone-X.png', fullPage: true});
  // Nexus 7
  await page.emulate(devices['Nexus 7']);
  await page.screenshot({path: 'screenshots/Nexus-7.png', fullPage: true});

  await browser.close();
})();
