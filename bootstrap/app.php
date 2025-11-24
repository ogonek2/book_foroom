<?php

use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\HandleAppearance;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
        ]);

        // Регистрируем middleware для ролей и разрешений
        $middleware->alias([
            'role' => CheckRole::class,
            'permission' => CheckPermission::class,
            'admin' => CheckAdmin::class,
        ]);

        // Исключаем API маршруты из CSRF проверки
        $middleware->validateCsrfTokens(except: [
            'api/reading-status/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Отключаем Phiki при отображении ошибок, чтобы избежать FailedToInitializePatternSearchException
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Если это ошибка Phiki, просто возвращаем простой ответ без подсветки синтаксиса
            if (str_contains($e->getMessage(), 'FailedToInitializePatternSearchException') || 
                $e instanceof \Phiki\Exceptions\FailedToInitializePatternSearchException) {
                return response()->view('errors.500', [
                    'message' => 'Помилка при обробці запиту. Будь ласка, спробуйте пізніше.',
                ], 500);
            }
        });
    })->create();
