<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IframeJwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken()
            ?? $request->header('X-IFRAME-TOKEN');

        if (!$token) {
            abort(401);
        }

        try {
            $payload = JWT::decode(
                $token,
                new Key(config('jwt.secret'), 'HS256')
            );
        } catch (\Exception $e) {
            abort(401);
        }

        if ($payload->type !== 'iframe') {
            abort(403);
        }

        $request->merge([
            'app_id' => $payload->sub
        ]);

        return $next($request);
    }
}
