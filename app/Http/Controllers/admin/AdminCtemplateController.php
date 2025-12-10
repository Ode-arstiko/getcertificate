<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ctemplates;
use Illuminate\Http\Request;

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
        $request->validate([
            'template_name' => 'required',
            'elements' => 'required'
        ]);

        $ctemplate = new Ctemplates();
        $ctemplate->template_name = $request->template_name;
        $ctemplate->elements = json_encode($request->elements);
        $ctemplate->save();

        return redirect('/admin/ctemplate');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
