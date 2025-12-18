<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AppClients;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AdminAppClientsController extends Controller
{
    public function index() {
        $data = [
            'content' => 'admin.appClients.index',
            'appClients' => AppClients::latest()->get()
        ];
        return view('layouts.admin.wrapper', $data);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required'
        ]);
        
        $app_id = 'app_' . Str::uuid();
        $appSecretPlain = Str::random(64);

        AppClients::create([
            'name' => $request->name,
            'app_id' => $app_id,
            'app_secret' => $appSecretPlain
        ]);

        return redirect()->back()->with('storeSuccess', 'Success');
    }

    public function delete($id) {
        $id = decrypt($id);
        $where = AppClients::find($id);
        $where->delete();
        return redirect()->back()->with('deleteSuccess', 'Success');
    }
}
