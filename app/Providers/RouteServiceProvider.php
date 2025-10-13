<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // API маршруты
            Route::middleware('web')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Web маршруты
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
                
            // Маршруты для книг
            Route::middleware('web')
                ->group(base_path('routes/books.php'));
                
            // Маршруты для аутентификации
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
                
            // Маршруты для настроек
            Route::middleware('web')
                ->group(base_path('routes/settings.php'));
        });
    }
}
