<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Constructor untuk debug
     */
    public function __construct()
    {
        Log::channel('single')->info('🔧 Cors Middleware CONSTRUCTED');
    }

    /**
     * Handle an incoming request.
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $allowedOrigins = [
    //         'https://localhost:5173',
    //     ];

    //     $origin = $request->headers->get('Origin');

    //     // Log untuk debugging
    //     Log::channel('single')->info('=== CUSTOM CORS MIDDLEWARE ===', [
    //         'origin' => $origin,
    //         'path' => $request->path(),
    //         'method' => $request->method(),
    //     ]);

    //     $headers = [];

    //     if ($origin && in_array($origin, $allowedOrigins)) {
    //         $headers = [
    //             'Access-Control-Allow-Origin' => $origin,
    //             'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
    //             'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-XSRF-TOKEN, X-Requested-With, Accept',
    //             'Access-Control-Allow-Credentials' => 'true',
    //             'Access-Control-Max-Age' => '86400',
    //         ];

    //         Log::channel('single')->info('✅ CORS Headers SET', [
    //             'origin_set' => $origin
    //         ]);
    //     } else {
    //         if (!$origin) {
    //             Log::channel('single')->warning('⚠️ No Origin header (direct access or curl without -H Origin)', [
    //                 'user_agent' => $request->userAgent(),
    //                 'referer' => $request->header('referer'),
    //             ]);
    //         } else {
    //             Log::channel('single')->warning('❌ Origin NOT in whitelist', [
    //                 'origin' => $origin,
    //                 'allowed' => $allowedOrigins
    //             ]);
    //         }
    //     }

    //     // Handle preflight OPTIONS
    //     if ($request->getMethod() === 'OPTIONS') {
    //         Log::channel('single')->info('Preflight OPTIONS request');
    //         return response()->json(['status' => 'OK'], 200, $headers);
    //     }

    //     // Process request
    //     $response = $next($request);

    //     // Set CORS headers to response
    //     foreach ($headers as $key => $value) {
    //         $response->headers->set($key, $value);
    //     }

    //     // Log final headers
    //     Log::channel('single')->info('Response CORS Headers', [
    //         'Access-Control-Allow-Origin' => $response->headers->get('Access-Control-Allow-Origin'),
    //         'Access-Control-Allow-Credentials' => $response->headers->get('Access-Control-Allow-Credentials'),
    //     ]);

    //     return $response;
    // }
}
