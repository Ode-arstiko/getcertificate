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
        $ctemplates = Ctemplates::latest()->get();

        return response()->json($ctemplates);
    }

    public function delete($id)
    {
        $where = Ctemplates::find(($id));
        $where->delete();

        return response()->json([
            'success' => true,
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
            abort(403);
        }

        $data = [
            'token' => $token
        ];

        return view('canvas-editor', $data);
    }

    public function edit(Request $request, $id)
    {
        $ctemplate = Ctemplates::find($id);
        $token = $request->query('token');

        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $payload = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));

        if ($payload->type !== 'iframe') {
            abort(403);
        }

        $data = [
            'token' => $token,
            'ctemplate' => $ctemplate
        ];

        return view('canvas-editor-edit', $data);
    }

    public function update(Request $request, $id)
    {
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
}
