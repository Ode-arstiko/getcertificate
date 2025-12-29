<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Certificates;
use App\Models\Ctemplates;
use App\Models\Zips;
use App\Jobs\GenerateCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $zip = Zips::latest()->get();
        $template = Ctemplates::where('client_id', $request->get('app_id'))->latest()->get();

        return response()->json([
            'zips' => $zip,
            'template' => $template
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
            'zip_name' => $certificateName . '-' . date('Y-m-d'),
            'client_id' => $request->get('app_id')
        ]);

        // folder html
        $htmlDir = storage_path('app/html');
        if (!File::exists($htmlDir)) {
            File::makeDirectory($htmlDir, 0755, true);
        }

        GenerateCertificate::dispatch($names, $juaras, $template, $zip->id, $htmlDir, $certificateName);

        return response()->json([
            'success' => true,
            'message' => 'Certificate is on process'
        ]);
    }

    public function delete($id)
    {
        $whereZip = Zips::find($id);
        $certificates = Certificates::where('zip_id', $whereZip->id)->get();
        // return response()->json(['respon' => $whereZip]);

        foreach ($certificates as $cer) {
            $pdfPath = public_path('pdf/' . $cer->certificate_name);
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }

        $whereZip->delete();
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
