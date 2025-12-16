<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Certificates;
use App\Models\Ctemplates;
use App\Models\Zips;
use App\Jobs\GenerateCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

        GenerateCertificate::dispatch($names, $juaras, $template, $zip->id, $htmlDir, $certificateName);

        return response()->json([
            'success' => true,
            'message' => 'Certificate is on process'
        ]);
    }

    public function zipDetails($id)
    {
        $id = decrypt($id);
        $certificates = Certificates::where('zip_id', $id)->get();

        return response()->json([
            'certificates' => $certificates
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
}
