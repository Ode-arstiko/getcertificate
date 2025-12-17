<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class VerifyTemporaryToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->token) {
            abort(403);
        }
    
        try {
            $data = Crypt::decrypt($request->token);
    
            if ($data['exp'] < now()->timestamp) {
                abort(403);
            }
    
        } catch (\Exception $e) {
            abort(403);
        }
    
        return $next($request);
    }
}
