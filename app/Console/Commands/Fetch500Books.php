<?php

namespace App\Console\Commands;

use App\Jobs\ImportGoogleBooksBatch;
use App\Models\GoogleBooksImportState;
use App\Services\GoogleBooksService;
use Illuminate\Console\Command;

class Fetch500Books extends Command
{
    protected $signature = 'books:fetch-500
                            {--target=500 : Сколько книг набрать и поставить в очередь}';

    protected $description = 'Набрать до 500 книг из Google Books (несколько запросов по очереди) и поставить одну партию в очередь';

    public function __construct(
        protected GoogleBooksService $googleBooks,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $target = max(1, (int) $this->option('target'));
        $queries = config('googlebooks.queries_for_500', ['intitle:a', 'intitle:e', 'subject:fiction']);

        $this->info("Цель: набрать {$target} книг, перебирая запросы по очереди.");
        $this->line('Запросы: ' . implode(', ', $queries));

        $collected = [];
        $seenIds = [];

        foreach ($queries as $query) {
            if (count($collected) >= $target) {
                break;
            }

            $state = GoogleBooksImportState::firstOrCreate(
                ['query' => $query],
                ['last_start_index' => 0]
            );
            $startIndex = (int) ($state->last_start_index ?? 0);

            $this->line("Запрос «{$query}» с startIndex={$startIndex}...");

            $batch = $this->googleBooks
                ->fetchBatchExcludingRussian($query, 200, $startIndex)
                ->toArray();

            $added = 0;
            foreach ($batch as $volume) {
                $id = $volume['id'] ?? null;
                if ($id === null || isset($seenIds[$id])) {
                    continue;
                }
                $seenIds[$id] = true;
                $collected[] = $volume;
                $added++;
                if (count($collected) >= $target) {
                    break;
                }
            }

            // Сдвигаем индекс только если получили результаты — иначе не «сжигаем» позицию
            if ($added > 0) {
                $state->update([
                    'last_start_index' => $startIndex + 200,
                    'last_run_at' => now(),
                ]);
            }

            $this->line("  Получено: {$added} новых, всего: " . count($collected));
        }

        $count = count($collected);
        if ($count === 0) {
            $this->warn('Не удалось набрать ни одной книги (по всем запросам).');
            return self::SUCCESS;
        }

        $chunkSize = (int) config('googlebooks.import_chunk_size', 50);
        $chunkSize = $chunkSize > 0 ? $chunkSize : 50;
        $chunks = array_chunk($collected, $chunkSize);
        foreach ($chunks as $chunk) {
            ImportGoogleBooksBatch::dispatch($chunk);
        }
        $this->info("В очередь на импорт поставлено {$count} книг (" . count($chunks) . " джоб по {$chunkSize}).");

        return self::SUCCESS;
    }
}
