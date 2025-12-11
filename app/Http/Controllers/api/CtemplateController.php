<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Ctemplates;
use Illuminate\Http\Request;

class CtemplateController extends Controller
{
    public function index() {
        $ctemplates = Ctemplates::latest()->get();

        return response()->json($ctemplates);
    }

    public function edit($id) {
        $ctemplate = Ctemplates::find(decrypt($id));

        return response()->json([
            'ctemplates' => $ctemplate
        ]);
    }
}
