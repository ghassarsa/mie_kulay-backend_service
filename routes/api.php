<?php

use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\Pesanan_DetailController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'users');
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', 'user');
        Route::put('/updateProfile', 'updateProfile');
    });
});
Route::controller(PengeluaranController::class)->group(function () {
    Route::get('/pengeluaran', 'index');
    Route::post('/pengeluaran', 'store');
});

Route::get('index', [AnalisisController::class, 'index'])->middleware(['auth:sanctum', 'role:owner']);
Route::get('monthly-income', [AnalisisController::class, 'monthlyIncome']);
Route::get('monthly-expenses', [AnalisisController::class, 'monthlyExpenses']);
Route::get('monthly-orders', [AnalisisController::class, 'monthlyOrders']);
Route::get('favorite-menu', [AnalisisController::class, 'favoriteMenu']);

Route::controller(CategoryController::class)->group(function () {
    Route::get('/category', 'index');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/category', 'store');
        Route::put('/category/{id}', 'update');
        Route::delete('/category/{id}', 'destroy');
    });
});

Route::controller(BahanController::class)->group(function () {
    Route::get('/bahan', 'index');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/tambah/bahan', 'store');
        Route::put('/edit/bahan/{id}', 'update');
        Route::delete('/hapus/bahan/{id}', 'destroy');
    });
});

Route::controller(MenuController::class)->group(function () {
    Route::get('/menu', 'index');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/menu', 'store');
        Route::get('/menu/{id}', 'show');
        Route::put('/menu/{id}', 'update');
        Route::delete('/menu/{id}', 'destroy');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(Pesanan_DetailController::class)->group(function () {
        Route::get('/pesanan_detail', 'index');
        Route::post('/pesanan_detail', 'store');
        Route::get('/pesanan_detail/{id}', 'show');
        Route::put('/pesanan_detail/{id}', 'update');
        Route::delete('/pesanan_detail/{id}', 'destroy');
    });
    Route::controller(PesananController::class)->group(function () {
        Route::get('/pesanan', 'index');
        Route::get('/pesanan/{id}', 'show');
        Route::put('/pesanan/{id}', 'update');
        Route::delete('/pesanan/{id}', 'destroy');
    });
});
