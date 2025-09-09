<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $menus = \App\Models\Menu::all();
    return view('app', ['menus' => $menus]);
});
