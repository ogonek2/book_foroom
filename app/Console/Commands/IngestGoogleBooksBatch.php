<?php

namespace App\Console\Commands;

use App\Jobs\ImportGoogleBooksBatch;
use App\Models\GoogleBooksImportState;
use App\Services\GoogleBooksService;
use Illuminate\Console\Command;

class IngestGoogleBooksBatch extends Command
{
    protected $signature = 'books:ingest-google-batch
                            {query? : Поисковый запрос для Google Books (по умолчанию из конфигурации)}
                            {--batch=500 : Минимальное количество книг, которые нужно попытаться набрать за запуск}
                            {--max-pages=10 : Максимальное количество шагов (страниц) за один запуск}';

    protected $description = 'Получить партию книг из Google Books (без русских авторов) и поставить их в очередь на импорт в БД';

    public function __construct(
        protected GoogleBooksService $googleBooks,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = $this->argument('query') ?? config('googlebooks.default_query', 'subject:books');
        $targetMin = (int) $this->option('batch');
        $maxPages = (int) $this->option('max-pages');

        $targetMin = $targetMin > 0 ? $targetMin : 500;
        $maxPages = $maxPages > 0 ? $maxPages : 10;

        $state = GoogleBooksImportState::firstOrCreate(
            ['query' => $query],
            ['last_start_index' => 0]
        );

        $startIndex = $state->last_start_index ?? 0;
        $collectedTotal = 0;
        $page = 0;

        $this->info("Получаем книги из Google Books до достижения минимума: {$targetMin}");
        $this->line("Запрос: {$query}");
        $this->line("Стартовый индекс: {$startIndex}");
        $this->line("Максимум шагов за запуск: {$maxPages}");

        $allVolumes = [];

        while ($collectedTotal < $targetMin && $page < $maxPages) {
            $page++;

            $this->line("Шаг {$page}: запрос с startIndex={$startIndex}");

            $volumes = $this->googleBooks
                ->fetchBatchExcludingRussian($query, 500, $startIndex)
                ->toArray();

            $count = count($volumes);

            if ($count === 0) {
                $this->warn("На шаге {$page} новых книг не найдено (после фильтрации). Останавливаемся.");
                break;
            }

            $this->line("  Найдено новых книг на этом шаге: {$count}");

            $allVolumes = array_merge($allVolumes, $volumes);
            $collectedTotal += $count;
            $startIndex += 500;
        }

        $count = count($allVolumes);

        if ($count === 0) {
            $this->warn('Новых книг не найдено (после фильтрации).');
            $state->update([
                'last_start_index' => $startIndex,
                'last_run_at' => now(),
            ]);
            return self::SUCCESS;
        }

        ImportGoogleBooksBatch::dispatch($allVolumes);

        $this->info("В очередь на импорт поставлено книг: {$count}");

        $state->update([
            'last_start_index' => $startIndex,
            'last_run_at' => now(),
        ]);

        return self::SUCCESS;
    }
}

