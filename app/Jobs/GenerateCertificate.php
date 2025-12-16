<?php

namespace App\Jobs;

use App\Helpers\FabricToHtml;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Certificates;

class GenerateCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $names;
    protected $juaras;
    protected $template;
    protected $zipId;
    protected $htmlDir;
    protected $certificateName;

    public function __construct($names, $juaras, $template, $zipId, $htmlDir, $certificateName)
    {
        $this->names = $names;
        $this->juaras = $juaras;
        $this->template = $template;
        $this->zipId = $zipId;
        $this->htmlDir = $htmlDir;
        $this->certificateName = $certificateName;
    }

    public function handle(): void
    {
        foreach ($this->names as $i => $nama) {

            // decode fabric json
            $json = json_decode($this->template->elements, true);
            if (is_string($json)) {
                $json = json_decode($json, true);
            }

            if (!isset($json['objects'])) {
                abort(500, 'FORMAT JSON TEMPLATE TIDAK VALID');
            }        

            // replace placeholder
            foreach ($json['objects'] as &$obj) {
                if (!empty($obj['text'])) {
                    $obj['text'] = str_replace('{nama}', $nama, $obj['text']);
                    $obj['text'] = str_replace('{juara}', $this->juaras[$i] ?? '', $obj['text']);
                }
            }

            // 🔥 render HTML dari Fabric
            $body = FabricToHtml::render($json);

            $html = <<<HTML
<!DOCTYPE html>
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
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Great+Vibes&display=swap">
</head>
<body>
$body
</body>
</html>
HTML;

            $htmlFile = uniqid('cert_') . '.html';
            $htmlPath = $this->htmlDir . '/' . $htmlFile;

            file_put_contents($htmlPath, $html);

            // output pdf
            $pdfName = str_replace(' ', '-', $this->certificateName)
                . '-' . str_replace(' ', '-', $nama)
                . '-' . time() . $i . '.pdf';

            $pdfPath = public_path('pdf/' . $pdfName);

            // 🔥 jalankan node
            $cmd = "node " . base_path('generator/render-pdf.js') . " "
                . escapeshellarg($htmlPath) . " "
                . escapeshellarg($pdfPath);

            pclose(popen("start /B $cmd", "r"));

            // simpan DB
            Certificates::create([
                'zip_id' => $this->zipId,
                'certificate_name' => $pdfName,
            ]);
        }
    }
}