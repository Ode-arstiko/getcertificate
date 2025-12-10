<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Certificates;
use App\Models\Ctemplates;
use App\Models\Zips;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use ZipArchive;

class AdminCertificateController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'admin.certificate.index',
            'zips' => Zips::latest()->get(),
            'template' => Ctemplates::latest()->get()
        ];
        return view('layouts.admin.wrapper', $data);
    }

    public function create($id)
    {
        $data = [
            'content' => 'admin.certificate.create',
            'template_id' => $id
        ];
        return view('layouts.admin.wrapper', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'juara' => 'required',
            'template_id' => 'required'
        ]);

        $template = Ctemplates::findOrFail(decrypt($request->template_id));
        $sertificate_name = $template->template_name;

        //simpan ke database
        $zip = Zips::create(['zip_name' => $sertificate_name . '-' . date('Y-m-d')]);

        // Pisahkan input menjadi array baris
        $names = preg_split('/\r\n|\r|\n/', trim($request->nama));
        $juaras = preg_split('/\r\n|\r|\n/', trim($request->juara));

        // Pastikan jumlahnya sama
        if (count($names) !== count($juaras)) {
            return back()->with('error', 'Jumlah nama dan juara harus sama.');
        }

        $result = [];

        foreach ($names as $i => $nama) {

            // Decode 1
            $json = json_decode($template->elements, true);

            // Jika hasil decode masih string → decode lagi
            if (is_string($json)) {
                $json = json_decode($json, true);
            }

            // Pastikan json valid
            if (!is_array($json) || !isset($json['objects'])) {
                dd("FORMAT JSON TIDAK VALID", $json);
            }

            // Replace {nama} dan {juara}
            foreach ($json['objects'] as &$obj) {
                if (!empty($obj['text'])) {

                    // nama
                    $obj['text'] = str_replace('{nama}', $nama, $obj['text']);

                    // juara (cek dulu apakah index juara ada)
                    $obj['text'] = str_replace(
                        '{juara}',
                        $juaras[$i] ?? "",   // kalau tidak ada juara, isi ""
                        $obj['text']
                    );
                }
            }

            // Simpan hasil
            $result[] = [
                "name" => $nama,
                "sertificate_name" => $sertificate_name,
                "zip_id" => $zip->id,
                "json" => json_encode($json)
            ];
        }
        return view('admin.certificate.generate', compact('result'));
    }

    public function saveCertificate(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'name' => 'required',
            'sertificate_name' => 'required'
        ]);

        $img = $request->image;
        $name = $request->name;
        $sertificate_name = $request->sertificate_name;

        // ambil base64
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = base64_decode($img);

        // simpan sementara PNG
        $pngPath = public_path("pdf/tmp-" . time() . ".png");
        file_put_contents($pngPath, $img);

        // buat PDF
        $pdf = Pdf::loadView('admin.certificate.certificate_pdf', [
            "image" => "pdf/" . basename($pngPath)
        ])->setPaper('a4', 'landscape');

        $filename = str_replace(" ", "-", $sertificate_name) . '-' . str_replace(" ", "-", $name) . '-' . time() . '.pdf';
        file_put_contents(public_path("pdf/" . $filename), $pdf->output());

        //simpan ke database
        $certificate_data = [
            'zip_id' => $request->zip_id,
            'certificate_name' => $filename,
        ];

        Certificates::create($certificate_data);

        unlink($pngPath);

        return "saved";
    }

    public function zipDetails($id) {
        $id = decrypt($id);
        $data = [
            'content' => 'admin.certificate.zip_details',
            'certificates' => Certificates::where('zip_id', $id)->latest()->get(),
            'zip' => Zips::find($id)
        ];
        return view('layouts.admin.wrapper', $data);
    }

    public function downlaodCertificate($id) {
        $id = decrypt($id);
        $certificate = Certificates::find($id)->certificate_name;
        $path = public_path('pdf/' . $certificate);
        
        if(!file_exists($path)) {
            abort(404);
        }
        
        return response()->download($path);
    }

    public function downloadZip($id) {
        $id = decrypt($id);
        $certificates = Certificates::where('zip_id', $id)->get();

        $filenames = [];
        $certificate_names = [];

        foreach ($certificates as $certificate) {
            $certificate_names[] = $certificate->certificate_name;
        }

        foreach ($certificate_names as $certificate_name) {
            $filenames[] = $certificate_name;
        }

        $zipName = $certificates->first()->zip->zip_name . '.zip';
        $zipPath = public_path('zip/' . $zipName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($filenames as $filename) {
                $filePath = public_path('pdf/' . $filename);

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
