<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Certificates;
use App\Models\Ctemplates;
use App\Models\Zips;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\FabricToHtml;
use App\Jobs\GenerateCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class CertificateController extends Controller
{
    public function index()
    {
        $zip = Zips::latest()->get();
        $template = Ctemplates::latest()->get();

        return response()->json([
            'zips' => $zip,
            'template' => $template
        ]);
    }

    public function renderForPuppeteer(Request $request)
    {
        return view('puppeteer.generate', [
            'json' => $request->json_data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'juara' => 'required',
            'template_id' => 'required'
        ]);

        $template = Ctemplates::findOrFail($request->template_id);
        $certificateName = $template->template_name;

        $names  = preg_split('/\r\n|\r|\n/', trim($request->nama));
        $juaras = preg_split('/\r\n|\r|\n/', trim($request->juara));

        if (count($names) !== count($juaras)) {
            return back()->with('error', 'Jumlah nama dan juara harus sama.');
        }

        // zip
        $zip = Zips::create([
            'zip_name' => $certificateName . '-' . date('Y-m-d')
        ]);

        // folder html
        $htmlDir = storage_path('app/html');
        if (!File::exists($htmlDir)) {
            File::makeDirectory($htmlDir, 0755, true);
        }

        foreach ($names as $i => $nama) {
            // decode fabric json
            $json = json_decode($template->elements, true);
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
                    $obj['text'] = str_replace('{juara}', $juaras[$i] ?? '', $obj['text']);
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
            $htmlPath = $htmlDir . '/' . $htmlFile;

            file_put_contents($htmlPath, $html);

            // output pdf
            $pdfName = str_replace(' ', '-', $certificateName)
                . '-' . str_replace(' ', '-', $nama)
                . '-' . time() . '.pdf';

            $pdfPath = public_path('pdf/' . $pdfName);

            // 🔥 jalankan node
            $cmd = "node " . base_path('generator/render-pdf.js') . " "
                . escapeshellarg($htmlPath) . " "
                . escapeshellarg($pdfPath);

            pclose(popen("start /B $cmd", "r"));

            // simpan DB
            Certificates::create([
                'zip_id' => $zip->id,
                'certificate_name' => $pdfName,
            ]);
        }

        // GenerateCertificate::dispatch($names, $juaras, $template, $zip->id, $htmlDir, $certificateName);

        return response()->json([
            'success' => true,
            'message' => 'Certificate is on process'
        ]);
    }

    public function delete($id)
    {
        $where = Zips::find($id);
        $where->delete();
        return response()->json([
            'message' => 'delete success'
        ]);
    }

    public function downloadZip($id)
    {
        $certificates = Certificates::where('zip_id', $id)->get();

        if ($certificates->isEmpty()) {
            return response()->json([
                'message' => 'Certificate tidak ditemukan'
            ], 404);
        }

        if (!file_exists(public_path('zip'))) {
            mkdir(public_path('zip'), 0755, true);
        }

        $zipName = $certificates->first()->zip->zip_name . '.zip';
        $zipPath = public_path('zip/' . $zipName);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json(['message' => 'Gagal membuat zip'], 500);
        }

        foreach ($certificates as $certificate) {
            $filePath = public_path('pdf/' . $certificate->certificate_name);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($filePath));
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function zipDetails($id)
    {
        $zip = Zips::find($id);

        if (!$zip) {
            return response()->json([
                'message' => 'Zip not found'
            ], 404);
        }

        $certificates = Certificates::where('zip_id', $id)->latest()->get();

        return response()->json([
            'zip' => $zip,
            'certificates' => $certificates
        ]);
    }

    public function downloadCertificate($id)
    {
        $certificate = Certificates::find($id);

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found'
            ], 404);
        }

        $path = public_path('pdf/' . $certificate->certificate_name);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return response()->download(
            $path,
            $certificate->certificate_name, // 🔥 nama file asli
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }
}
