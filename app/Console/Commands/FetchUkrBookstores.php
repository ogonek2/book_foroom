<?php

namespace App\Console\Commands;

use App\Jobs\ImportUkrBookstoresBatch;
use App\Models\UkrBookstoreListing;
use App\Services\UkrBookstoresService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchUkrBookstores extends Command
{
    protected $signature = 'books:fetch-ukr-bookstores
                            {--query= : Один пошуковий запит (замість списку з конфігу)}
                            {--stores= : Через кому ключі магазинів (наприклад yakaboo,nashformat)}
                            {--queue : Поставити збереження в чергу (джобу) замість збереження в цьому процесі}';

    protected $description = 'Парсинг українських книгарень: паралельні запити по всіх магазинах та запис результатів у ukr_bookstore_listings';

    public function __construct(
        protected UkrBookstoresService $ukrBookstores,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $singleQuery = $this->option('query');
        $storeKeys = $this->option('stores');
        $useQueue = $this->option('queue');
        $queries = $singleQuery !== null && $singleQuery !== ''
            ? [$singleQuery]
            : config('ukr_bookstores.queries_for_fetch', [config('ukr_bookstores.default_query', 'книга')]);

        $storeKeys = $storeKeys !== null && $storeKeys !== ''
            ? array_map('trim', explode(',', $storeKeys))
            : null;

        $this->info('Парсинг українських книгарень (паралельні запити + запис у БД).');
        if ($useQueue) {
            $this->line('Режим: збереження через чергу (джоби).');
        }
        $this->line('Запити: ' . implode(', ', $queries));
        if ($storeKeys !== null) {
            $this->line('Магазини: ' . implode(', ', $storeKeys));
        } else {
            $this->line('Магазини: всі з конфігу');
        }

        $totalProcessed = 0;
        $countBefore = UkrBookstoreListing::count();

        foreach ($queries as $query) {
            $this->line("Запит: «{$query}»...");

            $results = $this->ukrBookstores->searchAll($query, $storeKeys);

            $itemCount = 0;
            foreach ($results as $storeKey => $data) {
                if (! is_array($data) || ! isset($data['items'])) {
                    continue;
                }
                $storeName = $data['store_name'] ?? $storeKey;
                $count = count($data['items']);
                $itemCount += $count;
                $this->line("  {$storeName}: {$count} результатів" . (isset($data['error']) && $data['error'] ? " ({$data['error']})" : ''));
            }

            if ($useQueue) {
                ImportUkrBookstoresBatch::dispatch($query, $results);
                $this->info("  Джобу для запиту «{$query}» поставлено в чергу (записів: {$itemCount}).");
            } else {
                $saved = $this->saveResults($query, $results);
                $this->info("  Збережено за запит «{$query}»: {$saved}");
            }
            $totalProcessed += $itemCount;
        }

        if (! $useQueue) {
            $countAfter = UkrBookstoreListing::count();
            $this->info("Усього оброблено записів: {$totalProcessed}. У БД зараз рядків у ukr_bookstore_listings: {$countAfter} (було {$countBefore}).");

            $conn = DB::connection();
            $driver = $conn->getDriverName();
            $dbName = $driver === 'sqlite' ? ($conn->getDatabaseName() ?: 'file') : ($conn->getConfig('database') ?? '?');
            $this->line("Підключення БД: driver={$driver}, database={$dbName}");

            $sample = UkrBookstoreListing::orderByDesc('updated_at')->first();
            if ($sample) {
                $this->line("Останній оновлений запис: id={$sample->id}, search_query=\"{$sample->search_query}\", updated_at={$sample->updated_at}");
            }
        } else {
            $this->info("Усього оброблено записів: {$totalProcessed}. Джоби поставлено в чергу — запустіть worker: php artisan queue:work");
        }

        return self::SUCCESS;
    }

    /**
     * @param array<string, array{store_name: string, items: array, error: ?string}> $results
     */
    private function saveResults(string $query, array $results): int
    {
        $saved = 0;
        DB::transaction(function () use ($query, $results, &$saved) {
            foreach ($results as $storeKey => $data) {
                if (! is_array($data) || ! isset($data['items'])) {
                    continue;
                }
                foreach ($data['items'] as $item) {
                    if (empty($item['title'] ?? '') || empty($item['url'] ?? '')) {
                        continue;
                    }
                    UkrBookstoreListing::updateOrCreate(
                        [
                            'store_key' => $storeKey,
                            'url' => $item['url'],
                        ],
                        [
                            'external_id' => $item['external_id'] ?? null,
                            'title' => $item['title'],
                            'price' => $item['price'] ?? null,
                            'image_url' => $item['image_url'] ?? null,
                            'author' => $item['author'] ?? null,
                            'search_query' => $query,
                        ]
                    );
                    $saved++;
                }
            }
        });

        return $saved;
    }
}
