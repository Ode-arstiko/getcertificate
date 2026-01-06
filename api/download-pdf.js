import { createClient } from "@supabase/supabase-js";

const supabase = createClient(
  process.env.SUPABASE_URL,
  process.env.SUPABASE_ANON_KEY
);

export default async function handler(req, res) {
  try {
    const { file } = req.query;

    if (!file) {
      return res.status(400).json({ message: "file param required" });
    }

    // download file
    const { data, error } = await supabase.storage
      .from("pdf")
      .download(file);

    if (error) throw error;

    const buffer = Buffer.from(await data.arrayBuffer());

    res.setHeader("Content-Type", "application/pdf");
    res.setHeader(
      "Content-Disposition",
      `attachment; filename="${file.split("/").pop()}"`
    );

    return res.status(200).send(buffer);
  } catch (err) {
    return res.status(500).json({
      message: "Download failed",
      error: err.message,
    });
  }
}