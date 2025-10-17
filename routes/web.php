<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where web routes are registered for the application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Route::get('/test-cors', function (\Illuminate\Http\Request $request) {
//     Log::debug('Request masuk ke Laravel!', [
//         'headers' => $request->headers->all(),
//         'cookies' => $request->cookies->all(),
//         'origin' => $request->header('Origin'),
//     ]);

//     return response()->json(['status' => 'ok']);
// });

// Route::get('/test-cors', function () {
//     Log::debug('CORS test hit!', request()->headers->all());
//     return response()->json(['status' => 'ok']);
// });

// Route::get('/sanctum/csrf-cookie', function (Request $request) {
//     return response()->json(['status' => 'ok'])
//         ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
//         ->header('Access-Control-Allow-Credentials', 'true')
//         ->header('Access-Control-Allow-Headers', 'X-XSRF-TOKEN, Content-Type')
//         ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
// });

// Route::get('/sanctum/csrf-cookie', function () {
//     return response()->json(['message' => 'CSRF cookie set']);
// });
