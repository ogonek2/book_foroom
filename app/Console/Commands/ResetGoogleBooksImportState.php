<?php

namespace App\Console\Commands;

use App\Models\GoogleBooksImportState;
use Illuminate\Console\Command;

class ResetGoogleBooksImportState extends Command
{
    protected $signature = 'books:reset-google-books-state
                            {--query= : Сбросить только этот запрос (без значения — все)}';

    protected $description = 'Сбросить last_start_index для Google Books (начать заново с начала выдачи)';

    public function handle(): int
    {
        $query = $this->option('query');

        if ($query !== null) {
            $state = GoogleBooksImportState::where('query', $query)->first();
            if (! $state) {
                $this->warn("Запрос «{$query}» не найден в состоянии.");
                return self::SUCCESS;
            }
            $state->update(['last_start_index' => 0]);
            $this->info("Сброшен startIndex для запроса: {$query}");
            return self::SUCCESS;
        }

        $updated = GoogleBooksImportState::query()->update(['last_start_index' => 0]);
        $this->info("Сброшен startIndex для всех запросов (обновлено записей: {$updated}).");

        return self::SUCCESS;
    }
}
