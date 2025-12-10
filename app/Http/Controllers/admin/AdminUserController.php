<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'content' => 'admin.user.index',
            'users'   => User::all(),
        ];
        return view('layouts.admin.wrapper', $data);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'role' => 'required',
            ]);

            User::create($data);
            return redirect()->route('admin.user')->with('success', 'User berhasil ditambahkan!');
        } catch (Exception $e) {
            return redirect()->route('admin.user')->with('error', 'Gagal menambahkan user!');
        }
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validasi (sesuai dengan form)
            $data = $request->validate([
                'name'  => 'required',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'role'  => 'required',
                'password' => 'nullable|min:4'
            ]);

            // Jika password diisi, hash baru
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']); // jangan ubah password
            }

            $user->update($data);

            return redirect()->route('admin.user')->with('success', 'User berhasil diupdate!');
        } catch (Exception $e) {
            return redirect()->route('admin.user')->with('error', 'Gagal mengupdate user!');
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return redirect('/admin/users')->with('success', 'User berhasil dihapus!');
        } catch (Exception $e) {
            return redirect('/admin/users')->with('danger', 'Gagal menghapus data pengguna!');
        }
    }
}
