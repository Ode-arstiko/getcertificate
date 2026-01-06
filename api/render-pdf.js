import chromium from "@sparticuz/chromium";
import puppeteer from "puppeteer-core";
import { createClient } from '@supabase/supabase-js';

const supabase = createClient(
  process.env.SUPABASE_URL,
  process.env.SUPABASE_SERVICE_ROLE
);

function buildHtml(body) {
  return `<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 landscape; margin: 0; }
body {
  width: 1600px;
  height: 1131px;
  position: relative;
  margin: 0;
}
.fabric-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}
.fabric-canvas {
  position: relative;
  width: 100%;
  height: 100%;
}
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2
?family=Montserrat:wght@400;600;700;800;900
&family=Great+Vibes
&family=Playfair+Display:wght@400;700
&family=Libre+Baskerville:wght@400;700
&family=Cormorant+Garamond:wght@400;700
&family=Merriweather:wght@400;700
&family=Allura
&family=Alex+Brush
&family=Pacifico
&family=Lato:wght@400;700
&family=Poppins:wght@400;700
&family=Raleway:wght@400;700
&family=Open+Sans:wght@400;700
&display=swap"
rel="stylesheet">
</head>
<body>
${body}
</body>
</html>`
}

export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ message: "Method Not Allowed" });
  }

  try {
    const { body, filename } = req.body;

    if (!body) {
      return res.status(400).json({ message: "body is required" });
    }

    const html = buildHtml(body);

    const browser = await puppeteer.launch({
      args: chromium.args,
      defaultViewport: chromium.defaultViewport,
      executablePath: await chromium.executablePath(),
      headless: true,
    });

    const page = await browser.newPage();
    await page.setContent(html, { waitUntil: "networkidle0" });

    const pdfBuffer = await page.pdf({
      format: "A4",
      printBackground: true,
    });

    await browser.close();

    const { error: uploadError } = await supabase.storage
      .from("pdf")
      .upload(filename, pdfBuffer, {
        contentType: "application/pdf",
        upsert: false,
      });

    if (uploadError) {
      throw uploadError;
    }

    const { data } = supabase.storage
      .from("pdf")
      .getPublicUrl(filename);

    return res.status(200).json({
      success: true,
      url: data.publicUrl,
    });
  } catch (err) {
    console.error(err);
    return res.status(500).json({
      message: "PDF render failed",
      error: err.message,
    });
  }
}
