<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeLibrary extends Command
{
    protected $signature = 'books:purge-library {--force : Удалить книги/авторов/категории и связи без подтверждения}';

    protected $description = 'Очистить книги, авторов, категории и все их связи (pivot + зависимые записи, где есть CASCADE)';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $confirmed = $this->confirm(
                'Это удалит ВСЕ книги, авторов, категории и связи. Продолжить?',
                false
            );
            if (! $confirmed) {
                $this->warn('Отменено.');
                return self::SUCCESS;
            }
        }

        $driver = DB::getDriverName();

        $tables = [
            // pivot / relations
            'author_book',
            'book_category',
            'library_book',
            'user_libraries',
            'book_reading_statuses',
            'ratings',
            // content linked to books
            'reviews',
            'quotes',
            'facts',
            'book_prices',
            // main entities
            'books',
            'authors',
            'categories',
        ];

        DB::beginTransaction();
        try {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } elseif ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            foreach ($tables as $t) {
                DB::table($t)->delete();
                $this->line("Очищено: {$t}");
            }

            if ($driver === 'sqlite') {
                // сброс автоинкремента
                foreach ($tables as $t) {
                    DB::statement('DELETE FROM sqlite_sequence WHERE name = ?', [$t]);
                }
            } elseif ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            try {
                if ($driver === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } elseif ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON;');
                }
            } catch (\Throwable) {
                // ignore
            }

            $this->error('Ошибка очистки: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Готово: библиотека очищена.');
        return self::SUCCESS;
    }
}

