import { chromium } from 'playwright'
import { createClient } from '@supabase/supabase-js'

const supabase = createClient(
  process.env.SUPABASE_URL,
  process.env.SUPABASE_ANON_KEY // sesuai yang kemarin kamu pakai
)

export default async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method not allowed' })
  }

  try {
    const { html, fileName } = req.body

    const browser = await chromium.launch({
      headless: true,
      args: ['--no-sandbox']
    })

    const page = await browser.newPage()
    await page.setContent(html, { waitUntil: 'load' })

    const pdfBuffer = await page.pdf({
      format: 'A4',
      landscape: true,
      printBackground: true
    })

    await browser.close()

    // upload langsung ke Supabase
    const { error } = await supabase.storage
      .from('pdf')
      .upload(fileName, pdfBuffer, {
        contentType: 'application/pdf',
        upsert: true
      })

    if (error) throw error

    return res.json({ success: true, fileName })
  } catch (err) {
    return res.status(500).json({
      success: false,
      message: err.message
    })
  }
}