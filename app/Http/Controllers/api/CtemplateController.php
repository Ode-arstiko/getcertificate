<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Ctemplates;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CtemplateController extends Controller
{
    public function index()
    {
        return response()->json(
            Ctemplates::latest()->get()
        );
    }

    public function delete($id)
    {
        $ctemplate = Ctemplates::find($id);

        if (!$ctemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found'
            ], 404);
        }

        $ctemplate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted'
        ]);
    }

    public function create(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $payload = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));

        if ($payload->type !== 'iframe') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return view('canvas-editor', [
            'token' => $token
        ]);
    }

    public function edit(Request $request, $id)
    {
        $ctemplate = Ctemplates::find($id);

        if (!$ctemplate) {
            abort(404);
        }

        $token = $request->query('token');

        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $payload = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));

        if ($payload->type !== 'iframe') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return view('canvas-editor-edit', [
            'token' => $token,
            'ctemplate' => $ctemplate
        ]);
    }

    /**
     * STORE TEMPLATE (INI YANG DIPANGGIL FETCH)
     */
    public function store(Request $request)
    {
        $request->validate([
            'template_name' => 'required',
            'elements' => 'required'
        ]);

        $ctemplate = Ctemplates::create([
            'template_name' => $request->template_name,
            'elements' => json_encode($request->elements)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template has been stored!',
            'data' => $ctemplate
        ], 201);
    }

    /**
     * UPDATE TEMPLATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'template_name' => 'required',
            'elements' => 'required'
        ]);

        $ctemplate = Ctemplates::find($id);

        if (!$ctemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found'
            ], 404);
        }

        $ctemplate->update([
            'template_name' => $request->template_name,
            'elements' => json_encode($request->elements)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template has been updated!'
        ]);
    }
}
