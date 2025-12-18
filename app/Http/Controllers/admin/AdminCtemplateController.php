<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ctemplates;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminCtemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'content' => 'admin.ctemplate.index',
            'ctemplate' => Ctemplates::latest()->get()
        ];
        return view('layouts.admin.wrapper', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'content' => 'admin.ctemplate.add',
            'canvas_json' => Ctemplates::first()
        ];
        return view('layouts.admin.wrapper', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'template_name' => 'required|string',
                'elements' => 'required'
            ]);

            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['message' => 'Token tidak ditemukan'], 401);
            }

            $payload = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));

            if ($payload->type !== 'iframe') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            Ctemplates::create([
                'template_name' => $request->template_name,
                'elements' => json_encode($request->elements)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil disimpan'
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan template',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $data = [
            'content' => 'admin.ctemplate.edit',
            'ctemplate' => Ctemplates::find($id)
        ];
        return view('layouts.admin.wrapper', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $request->validate([
            'template_name' => 'required',
            'elements' => 'required'
        ]);

        $ctemplate = Ctemplates::find($id);
        $data = [
            'template_name' => $request->template_name,
            'elements' => json_encode($request->elements)
        ];

        $ctemplate->update($data);
        return redirect()->back()->with('updateSuccess', 'Template has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $id = decrypt($id);
        $where = Ctemplates::find($id);
        $where->delete();
        return redirect()->back();
    }

    public function uploadImage(Request $request)
    {
        // Validasi
        $request->validate([
            'image' => 'required|image|max:5120' // max 5MB
        ]);

        // Simpan file ke public/images/canvas/
        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Pastikan folder ada
        $path = public_path('images/canvas');
        if (!file_exists($path)) {
            mkdir($path, 0775, true);
        }

        // Simpan file
        $file->move($path, $filename);

        // Return URL untuk canvas
        return response()->json([
            'url' => '/images/canvas/' . $filename,
            'filename' => $filename
        ]);
    }
}
