<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\UkrBookstoresService;
use Illuminate\Console\Command;

class RefreshUkrBookstoreBooksCommand extends Command
{
    protected $signature = 'books:refresh-ukr-bookstore-books
                            {--limit= : Максимум книг оновити (за замовчуванням — всі з ukr_store_url)}
                            {--delay=1 : Затримка в секундах між запитами}
                            {--dry-run : Не змінювати БД, тільки показати, що було б оновлено}';

    protected $description = 'Перезаписати книги, імпортовані з українських книгарень: знову завантажити сторінку товару, застосувати виправлені парсери (обкладинка книги, опис без реклами, назва без ціни)';

    public function __construct(
        protected UkrBookstoresService $ukrBookstores,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $delay = max(0, (float) $this->option('delay'));
        $dryRun = $this->option('dry-run');

        $query = Book::whereNotNull('ukr_store_url')->where('ukr_store_url', '!=', '');
        $total = $query->count();
        $books = $limit ? $query->limit($limit)->get() : $query->get();

        if ($books->isEmpty()) {
            $this->warn('Немає книг з ukr_store_url.');
            return self::SUCCESS;
        }

        $this->info("Книг для оновлення: {$books->count()}" . ($limit ? " (з {$total})" : '') . ($dryRun ? ' [dry-run]' : ''));
        $bar = $this->output->createProgressBar($books->count());
        $bar->start();

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($books as $book) {
            $url = $book->ukr_store_url;
            $storeKey = $this->ukrBookstores->getStoreKeyFromProductUrl($url);
            if (! $storeKey) {
                $bar->advance();
                $skipped++;
                continue;
            }

            $data = $this->ukrBookstores->fetchBookDetailsFromProductUrl($storeKey, $url);
            if (! $data) {
                $bar->advance();
                $errors++;
                if ($delay > 0) {
                    usleep((int) ($delay * 1_000_000));
                }
                continue;
            }

            if (! $dryRun) {
                $book->title = $data['title'];
                $book->book_name_ua = $data['title'];
                $book->annotation = $data['description'] ?? $book->annotation;
                if (isset($data['cover_image']) && $data['cover_image'] !== '') {
                    $book->cover_image = $data['cover_image'];
                }
                $book->annotation_source = $book->annotation_source ?: 'ukr_bookstore';
                if (! empty($data['authors'])) {
                    $book->author = is_string($data['authors'][0]) ? $data['authors'][0] : ($data['authors'][0]['name'] ?? $book->author);
                }
                if (isset($data['isbn'])) {
                    $book->isbn = $data['isbn'];
                }
                if (isset($data['publisher'])) {
                    $book->publisher = $data['publisher'];
                }
                if (isset($data['publication_year'])) {
                    $book->publication_year = $data['publication_year'];
                    $book->first_publish_year = $data['publication_year'];
                }
                $book->save();
                $updated++;
            } else {
                $updated++;
            }

            $bar->advance();
            if ($delay > 0) {
                usleep((int) ($delay * 1_000_000));
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("Готово. Оновлено: {$updated}, пропущено (невідомий магазин): {$skipped}, помилок/немає даних: {$errors}.");

        return self::SUCCESS;
    }
}
