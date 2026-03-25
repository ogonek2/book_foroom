<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TranslateExistingBooks extends Command
{
    protected $signature = 'books:translate-existing
                            {--limit=500 : Максимальное количество книг за один запуск}';

    protected $description = 'Перевести существующие английские заголовки и описания книг на украинский и обновить slug';

    public function handle(): int
    {
        /** @var TranslationService $translator */
        $translator = app(TranslationService::class);

        $limit = (int) $this->option('limit');
        $limit = $limit > 0 ? $limit : 500;

        $this->info("Переводим существующие книги (максимум {$limit} за запуск)...");

        $processed = 0;

        Book::query()
            ->where('language', 'en')
            ->orderBy('id')
            ->chunkById(100, function ($books) use (&$processed, $limit, $translator) {
                foreach ($books as $book) {
                    if ($processed >= $limit) {
                        return false; // остановить chunkById
                    }

                    // Пропускаем, если уже есть украинское название
                    if ($book->book_name_ua && $book->book_name_ua !== $book->title) {
                        continue;
                    }

                    $originalTitle = $book->title;
                    $originalDescription = $book->annotation;

                    $translatedTitle = $translator->translateToUkrainian($originalTitle);
                    $translatedDescription = $translator->translateToUkrainian($originalDescription);

                    $book->book_name_ua = $translatedTitle;
                    $book->annotation = $translatedDescription;
                    $book->annotation_source = 'translated_uk';

                    // Перегенерируем slug из украинского названия с учетом уникальности
                    $sourceTitle = $book->book_name_ua ?: $book->title;
                    $baseSlug = Str::slug($sourceTitle);
                    $slug = $baseSlug;
                    $counter = 1;

                    while (
                        Book::where('slug', $slug)
                            ->where('id', '!=', $book->id)
                            ->exists()
                    ) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    $book->slug = $slug;

                    $book->save();
                    $processed++;

                    $this->line("Обновлена книга #{$book->id}: {$book->title} -> {$book->book_name_ua}");

                    if ($processed >= $limit) {
                        return false;
                    }
                }
            });

        $this->info("Всего обновлено книг: {$processed}");

        return self::SUCCESS;
    }
}

