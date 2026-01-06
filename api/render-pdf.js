import { chromium } from 'playwright'
import { createClient } from '@supabase/supabase-js'

const supabase = createClient(
  process.env.SUPABASE_URL,
  process.env.SUPABASE_ANON_KEY
)

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
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method not allowed' })
  }

  try {
    const { body, fileName } = req.body

    if (!body || !fileName) {
      return res.status(400).json({
        success: false,
        message: 'body dan fileName wajib diisi'
      })
    }

    const browser = await chromium.launch({
      headless: true,
      args: ['--no-sandbox']
    })

    const page = await browser.newPage()

    await page.setContent(buildHtml(body), {
      waitUntil: 'load'
    })

    const pdfBuffer = await page.pdf({
      format: 'A4',
      landscape: true,
      printBackground: true
    })

    await browser.close()

    // ⬆️ upload langsung ke Supabase Storage
    const { error } = await supabase.storage
      .from('pdf')
      .upload(fileName, pdfBuffer, {
        contentType: 'application/pdf',
        upsert: true
      })

    if (error) throw error

    return res.json({
      success: true,
      fileName
    })
  } catch (err) {
    console.error(err)
    return res.status(500).json({
      success: false,
      message: err.message
    })
  }
}