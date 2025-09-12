<?php

use App\Http\Middleware\Cors;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(Cors::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('laporan:to-analisis-month')
            ->monthlyOn(1, '01:00')
            ->timezone('Asia/Jakarta')
            ->when(function () {
                return \App\Models\Pesanan_Detail::where('status', 'belum')->exists();
            })
            ->onSuccess(function () {
                Log::info('Monthly financial analysis completed successfully');
            })
            ->onFailure(function () {
                Log::error('Monthly financial analysis failed');
            });

        $schedule->command('laporan:to-analisis-year')
            ->yearlyOn(1, 1, '01:00')
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {
                Log::info('Yearly period update completed successfully');
            })
            ->onFailure(function () {
                Log::error('Yearly period update failed');
            });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
