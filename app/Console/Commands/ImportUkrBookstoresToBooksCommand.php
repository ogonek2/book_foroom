<?php

namespace App\Console\Commands;

use App\Jobs\ImportUkrBookstoresToBooks;
use App\Models\UkrBookstoreListing;
use App\Services\UkrBookstoresService;
use Illuminate\Console\Command;

class ImportUkrBookstoresToBooksCommand extends Command
{
    protected $signature = 'books:import-ukr-bookstores-to-books
                            {--limit=50 : Скільки записів з ukr_bookstore_listings обробити (завантажити сторінку товару і додати в books)}
                            {--store= : Лише цей store_key (наприклад yakaboo)}
                            {--queue : Поставити імпорт у чергу замість виконання в цьому процесі}';

    protected $description = 'Імпорт книг з ukr_bookstore_listings у таблиці books, authors, categories (парсинг сторінки товару для кожної посилання)';

    public function __construct(
        protected UkrBookstoresService $ukrBookstores,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $storeKey = $this->option('store');
        $useQueue = $this->option('queue');

        $query = UkrBookstoreListing::query()->orderBy('id');
        if ($storeKey !== null && $storeKey !== '') {
            $query->where('store_key', $storeKey);
        }
        $listings = $query->limit($limit)->get();

        if ($listings->isEmpty()) {
            $this->warn('Немає записів у ukr_bookstore_listings (або фільтр по store не знайшов записів). Спочатку запустіть books:fetch-ukr-bookstores.');
            return self::SUCCESS;
        }

        $this->info("Обробка {$listings->count()} записів (парсинг сторінки товару → books/authors/categories)...");
        $delaySeconds = config('ukr_bookstores.fetch_delay_seconds', 1);
        $chunkSize = config('ukr_bookstores.import_to_books_chunk_size', 20);

        $collected = [];
        $bar = $this->output->createProgressBar($listings->count());
        $bar->start();

        foreach ($listings as $listing) {
            $data = $this->ukrBookstores->fetchBookDetailsFromProductUrl($listing->store_key, $listing->url);
            if ($data) {
                $collected[] = $data;
            }
            $bar->advance();
            if ($delaySeconds > 0) {
                usleep((int) ($delaySeconds * 1_000_000));
            }
        }

        $bar->finish();
        $this->newLine();

        if (empty($collected)) {
            $this->warn('Не вдалося розпарсити жодної сторінки товару (перевірте product_page_parser або структуру сторінок).');
            return self::SUCCESS;
        }

        $this->info("Розпарсено книг: " . count($collected) . ".");

        if ($useQueue) {
            $chunks = array_chunk($collected, $chunkSize);
            foreach ($chunks as $chunk) {
                ImportUkrBookstoresToBooks::dispatch($chunk);
            }
            $this->info("Джоби імпорту поставлено в чергу: " . count($chunks) . " (по {$chunkSize} книг). Запустіть: php artisan queue:work");
        } else {
            $chunks = array_chunk($collected, $chunkSize);
            foreach ($chunks as $chunk) {
                $job = new ImportUkrBookstoresToBooks($chunk);
                $job->handle();
            }
            $this->info("Імпорт завершено. Додано/оновлено книг у БД: " . count($collected) . ".");
        }

        return self::SUCCESS;
    }
}
