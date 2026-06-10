<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class SyncBookAuthorsPivot extends Command
{
    protected $signature = 'books:sync-authors-pivot';

    protected $description = 'Синхронізує author_book з author_id для існуючих книг';

    public function handle(): int
    {
        $synced = 0;

        Book::query()
            ->whereNotNull('author_id')
            ->chunkById(200, function ($books) use (&$synced) {
                foreach ($books as $book) {
                    $book->authors()->syncWithoutDetaching([$book->author_id]);
                    $synced++;
                }
            });

        $this->info("Синхронізовано {$synced} книг.");

        return self::SUCCESS;
    }
}
