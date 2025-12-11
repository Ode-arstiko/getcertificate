<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Api_tokens;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminMakeTokenController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'admin.make-token.index',
            'api_tokens' => Api_tokens::latest()->get()
        ];
        return view('layouts.admin.wrapper', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|unique:api_tokens',
        ]);

        $api_token = Str::random(60);

        Api_tokens::create([
            'app_name' => $request->app_name,
            'token' => $api_token
        ]);

        return redirect('/admin/make-token')->with('storeSuccess', 'Api token created successfully.');
    }

    public function delete($id) {
        $id = decrypt($id);
        $where = Api_tokens::find($id);
        $where->delete();
        return redirect()->back()->with('deleteSuccess', 'Api token deleted successfully.');
    }
}
