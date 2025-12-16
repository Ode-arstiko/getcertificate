const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
    const htmlPath = process.argv[2];
    const outputPdf = process.argv[3];

    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    const html = fs.readFileSync(htmlPath, 'utf8');
    await page.setContent(html, { waitUntil: 'load' });

    await page.waitForTimeout(2000);

    await page.pdf({
        path: outputPdf,
        format: 'A4',
        landscape: true,
        printBackground: true
    });

    fs.unlink(htmlPath, (err) => {
        if (err) console.error('Gagal menghapus HTML:', err);
    });

    await browser.close();
})();