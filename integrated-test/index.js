'use strict';

const puppeteer = require('puppeteer');
const devices = require('puppeteer/DeviceDescriptors');

(async() => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();

  // iPhone 5
  await page.emulate(devices['iPhone 5']);
  await page.goto('http://127.0.0.1:4000/');
  await page.screenshot({path: 'iPhone-5.png', fullPage: true});
  // iPhone 6/7/8
  await page.emulate(devices['iPhone 6']);
  await page.goto('http://127.0.0.1:4000/');
  await page.screenshot({path: 'iPhone-6.png', fullPage: true});
  // iPhone 6/7/8 Plus
  await page.emulate(devices['iPhone 6 Plus']);
  await page.goto('http://127.0.0.1:4000/');
  await page.screenshot({path: 'iPhone-6-plus.png', fullPage: true});
  // iPhone X
  await page.emulate(devices['iPhone X']);
  await page.goto('http://127.0.0.1:4000/');
  await page.screenshot({path: 'iPhone-X.png', fullPage: true});
  // Pixel 2
  await page.emulate(devices['Pixel 2']);
  await page.goto('http://127.0.0.1:4000/');
  await page.screenshot({path: 'Pixel-2.png', fullPage: true});

  await browser.close();
})();
