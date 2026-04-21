<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            "admin" => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/notification',
        ]);

        // Shop maintenance check on all web requests
        $middleware->web(append: [
            \App\Http\Middleware\ShopStatusMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tidak ditemukan.'], 404);
            }
            return response()->view('error-page', [
                'code' => 404,
                'title' => 'Halaman Tidak Ditemukan',
                'message' => 'Maaf, halaman yang kamu cari tidak ada atau sudah dipindahkan.',
            ], 404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Akses ditolak.'], 403);
                }
                return response()->view('error-page', [
                    'code' => 403,
                    'title' => 'Akses Ditolak',
                    'message' => 'Kamu tidak memiliki izin untuk mengakses halaman ini.',
                ], 403);
            }

            if ($e->getStatusCode() === 429) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Terlalu banyak permintaan. Coba lagi nanti.'], 429);
                }
                return response()->view('error-page', [
                    'code' => 429,
                    'title' => 'Terlalu Banyak Permintaan',
                    'message' => 'Kamu terlalu sering melakukan aksi ini. Silakan tunggu beberapa saat.',
                ], 429);
            }
        });

        // 500 error handler (production only)
        $exceptions->render(function (\Throwable $e, $request) {
            if (!app()->hasDebugModeEnabled() && !$request->expectsJson()) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                if ($statusCode >= 500) {
                    return response()->view('error-page', [
                        'code' => 500,
                        'title' => 'Terjadi Kesalahan',
                        'message' => 'Maaf, terjadi kesalahan pada server. Silakan coba lagi nanti.',
                    ], 500);
                }
            }
        });
    })
    ->create();
