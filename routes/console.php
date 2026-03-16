<?php

use App\Console\Commands\IngestGoogleBooksBatch;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Планировщик: ежедневный импорт минимум 500 новых книг из Google Books.
 *
 * Команда сама хранит last_start_index по каждому query в БД
 * и продолжает с места, где остановилась в прошлый раз.
 */
Schedule::command(IngestGoogleBooksBatch::class, [
    '--batch' => 500,
    '--max-pages' => 10,
])->dailyAt('03:00')->withoutOverlapping()->environments(['production', 'staging']);

