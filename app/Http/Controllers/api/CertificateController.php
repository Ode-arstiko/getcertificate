<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Certificates;
use App\Models\Ctemplates;
use App\Models\Zips;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index() {
        $zips = Zips::latest()->get();
        $template = Ctemplates::latest()->get();

        return response()->json([
            'template' => $template,
            'zips' => $zips
        ]);
    }

    public function zipDetails($id) {
        $id = decrypt($id);
        $certificates = Certificates::where('zip_id', $id)->get();

        return response()->json([
            'certificates' => $certificates
        ]);
    }

    public function delete($id) {
        $where = Zips::find(decrypt($id));
        $where->delete();
        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
