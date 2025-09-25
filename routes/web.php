<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/debug-session', function (\Illuminate\Http\Request $request) {
    $cookie = $request->cookie('laravel_session');
    return [
        'cookie' => $cookie,
        'session_id' => Session::getId(),
        'in_db' => DB::table('sessions')->where('id', Session::getId())->exists(),
    ];
});
