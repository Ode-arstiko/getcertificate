import chromium from '@sparticuz/chromium'
import { chromium as playwrightChromium } from 'playwright-core'
import { createClient } from '@supabase/supabase-js'

const supabase = createClient(
  process.env.SUPABASE_URL,
  process.env.SUPABASE_ANON_KEY
)

export default async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method not allowed' })
  }

  try {
    const { html, filename } = req.body

    const browser = await playwrightChromium.launch({
      args: chromium.args,
      executablePath: await chromium.executablePath(),
      headless: true // ✅ FIX UTAMA DI SINI
    })

    const page = await browser.newPage()
    await page.setContent(html, { waitUntil: 'load' })

    const pdfBuffer = await page.pdf({
      format: 'A4',
      landscape: true,
      printBackground: true
    })

    await browser.close()

    const { error } = await supabase.storage
      .from('pdf')
      .upload(filename, pdfBuffer, {
        contentType: 'application/pdf',
        upsert: true
      })

    if (error) throw error

    res.json({ success: true, filename })
  } catch (err) {
    console.error(err)
    res.status(500).json({
      success: false,
      message: err.message
    })
  }
}