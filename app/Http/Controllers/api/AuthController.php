<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ])->cookie(
            'access_token',
            $token,
            10080,
            '/',
            null,
            true,
            true
        );
    }
}
