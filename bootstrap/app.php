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
        });
    })
    ->create();
