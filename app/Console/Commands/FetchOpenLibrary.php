<?php

namespace App\Console\Commands;

use App\Jobs\ImportOpenLibraryBatch;
use App\Services\OpenLibraryService;
use Illuminate\Console\Command;

class FetchOpenLibrary extends Command
{
    protected $signature = 'books:fetch-open-library
                            {--target=500 : Сколько книг набрать и поставить в очередь}';

    protected $description = 'Набрать книги из Open Library (несколько запросов по очереди) и поставить партию в очередь на импорт';

    public function __construct(
        protected OpenLibraryService $openLibrary,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $target = max(1, (int) $this->option('target'));
        $queries = config('openlibrary.queries_for_fetch', ['fiction', 'history', 'science']);

        $this->info("Цель: набрать {$target} книг из Open Library.");
        $this->line('Запросы: ' . implode(', ', $queries));

        $collected = [];
        $seenIds = [];

        foreach ($queries as $query) {
            if (count($collected) >= $target) {
                break;
            }

            $need = $target - count($collected);
            $this->line("Запрос «{$query}» (нужно ещё {$need})...");

            $batch = $this->openLibrary->fetchBatch($query, min(200, $need), 1);

            $added = 0;
            foreach ($batch as $item) {
                $id = $item['id'] ?? $item['work_key'] ?? null;
                if ($id === null || isset($seenIds[$id])) {
                    continue;
                }
                $seenIds[$id] = true;
                $collected[] = $item;
                $added++;
                if (count($collected) >= $target) {
                    break;
                }
            }

            $this->line("  Получено: {$added}, всего: " . count($collected));

            if ($added === 0) {
                continue;
            }
        }

        $count = count($collected);
        if ($count === 0) {
            $this->warn('Не удалось набрать ни одной книги.');
            return self::SUCCESS;
        }

        $chunkSize = (int) config('openlibrary.import_chunk_size', 50);
        $chunkSize = $chunkSize > 0 ? $chunkSize : 50;
        $chunks = array_chunk($collected, $chunkSize);
        foreach ($chunks as $chunk) {
            ImportOpenLibraryBatch::dispatch($chunk);
        }
        $this->info("В очередь на импорт поставлено {$count} книг (" . count($chunks) . " джоб по {$chunkSize}).");

        return self::SUCCESS;
    }
}
