<?php

namespace App\Providers;

use App\Models\Book;
use App\Observers\BookObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        
        App::setLocale('uk');
        Carbon::setLocale('uk');

        Book::observe(BookObserver::class);
    }
}
