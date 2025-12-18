<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AppClients;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class GetTokenController extends Controller
{
    public function getToken(Request $request) {
        $request->validate([
            'app_id'     => 'required',
            'app_secret' => 'required',
        ]);

        // validasi app (ambil dari DB)
        $app = AppClients::where('app_id', $request->app_id)
            ->where('app_secret', $request->app_secret)
            ->first();

        if (!$app) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $payload = [
            'iss' => 'certificate-generator',
            'sub' => $app->id,
            'app' => $app->name,
            'iat' => time(),
            'exp' => time() + config('jwt.ttl'),
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'access_token' => $token,
            'expires_in' => config('jwt.ttl'),
        ]);
    }

    public function getTempToken(Request $request) {
        $request->validate([
            'app_id' => 'required',
            'app_secret' => 'required'
        ]);

        $app = AppClients::where('app_id', $request->app_id)
        ->where('app_secret', $request->app_secret)
        ->first();

        if(!$app) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $payload = [
            'iss' => 'template-generator',
            'type' => 'iframe',
            'sub' => $app->id,
            'app' => $app->name,
            'iat' => time(),
            'exp' => time() + 600,
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'access_token' => $token
        ]);
    }
}
