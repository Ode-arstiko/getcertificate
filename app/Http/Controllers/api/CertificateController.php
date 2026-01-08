<?php

namespace App\Http\Controllers\api;

use App\Helpers\FabricToHtml;
use App\Http\Controllers\Controller;
use App\Models\Certificates;
use App\Models\Ctemplates;
use App\Models\Zips;
use App\Jobs\GenerateCertificate;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use ZipStream\ZipStream;
use ZipStream\Option\Archive;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $zip = Zips::where('client_id', $request->get('app_id'))->latest()->get();

        return response()->json(
            $zip
        );
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

        if (count($names) > 5) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 5 data per request'
            ], 422);
        }

        // zip
        $zip = Zips::create([
            'zip_name' => $certificateName . '-' . date('Y-m-d'),
            'client_id' => $request->get('app_id')
        ]);

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

            Http::post('https://getcertificate-v1.vercel.app/api/render-pdf', [
                'body' => $body,
                'filename' => $certificateName . '-' . $nama . '.pdf',
            ]);

            Certificates::create([
                'zip_id' => $zip->id,
                'certificate_name' => $certificateName . '-' . $nama . '.pdf',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Certificate berhasil diproses'
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
                'success' => false,
                'message' => 'No certificates found'
            ], 404);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type'  => 'application/json',
        ])->post(
            env('SUPABASE_URL') . '/functions/v1/generate-zip',
            [
                'zip_id' => $id
            ]
        );

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed call edge function',
                'error'   => $response->body(),
            ], 500);
        }
    
        return response()->json($response->json());
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

        $filename = $certificate->certificate_name;
        $url = env('SUPABASE_URL') . "/storage/v1/object/public/pdf/" . $filename;

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}
