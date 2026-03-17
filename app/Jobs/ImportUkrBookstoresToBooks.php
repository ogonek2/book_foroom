<?php

namespace App\Jobs;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ImportUkrBookstoresToBooks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    /**
     * @var array<int, array{title: string, authors: array, description: ?string, cover_image: ?string, isbn: ?string, publisher: ?string, publication_year: ?int, categories: array, ukr_store_url: string}>
     */
    public array $books;

    public function __construct(array $books)
    {
        $this->books = $books;
    }

    public function handle(): void
    {
        foreach ($this->books as $data) {
            $this->importOne($data);
        }
    }

    /**
     * @param array{title: string, authors: array, description: ?string, cover_image: ?string, isbn: ?string, publisher: ?string, publication_year: ?int, categories: array, ukr_store_url: string} $data
     */
    private function importOne(array $data): void
    {
        $ukrStoreUrl = $data['ukr_store_url'] ?? null;
        $isbn = $data['isbn'] ?? null;
        $title = $data['title'] ?? '';
        $authors = $data['authors'] ?? [];

        if ($title === '') {
            return;
        }

        $book = null;
        if ($ukrStoreUrl) {
            $book = Book::where('ukr_store_url', $ukrStoreUrl)->first();
        }
        if (! $book && $isbn) {
            $book = Book::where('isbn', $isbn)->first();
        }
        if (! $book && ! empty($authors)) {
            $firstAuthor = is_array($authors[0] ?? null) ? ($authors[0]['name'] ?? '') : (string) ($authors[0] ?? '');
            $book = Book::where('title', $title)->where('author', $firstAuthor)->first();
        }

        $authorString = is_array($authors) && isset($authors[0]) ? (is_string($authors[0]) ? $authors[0] : ($authors[0]['name'] ?? 'Невідомий автор')) : 'Невідомий автор';

        if ($book) {
            $book->update([
                'title' => $data['title'],
                'book_name_ua' => $data['title'],
                'annotation' => $data['description'] ?? $book->annotation,
                'annotation_source' => $book->annotation_source ?: 'ukr_bookstore',
                'author' => $authorString,
                'isbn' => $data['isbn'] ?? $book->isbn,
                'publication_year' => $data['publication_year'] ?? $book->publication_year,
                'first_publish_year' => $data['publication_year'] ?? $book->first_publish_year,
                'publisher' => $data['publisher'] ?? $book->publisher,
                'cover_image' => $data['cover_image'] ?? $book->cover_image,
                'language' => $book->language ?: 'uk',
                'ukr_store_url' => $ukrStoreUrl ?? $book->ukr_store_url,
            ]);
        } else {
            $book = Book::create([
                'title' => $data['title'],
                'book_name_ua' => $data['title'],
                'annotation' => $data['description'] ?? null,
                'annotation_source' => 'ukr_bookstore',
                'author' => $authorString,
                'isbn' => $data['isbn'] ?? null,
                'publication_year' => $data['publication_year'] ?? null,
                'first_publish_year' => $data['publication_year'] ?? null,
                'publisher' => $data['publisher'] ?? null,
                'cover_image' => $data['cover_image'] ?? null,
                'language' => 'uk',
                'ukr_store_url' => $ukrStoreUrl,
                'rating' => 0,
                'reviews_count' => 0,
                'interesting_facts' => null,
                'synonyms' => [],
                'series' => null,
                'series_number' => null,
            ]);
        }

        $authorIds = [];
        $primaryAuthorId = null;
        foreach ($authors as $index => $name) {
            if (is_array($name)) {
                $name = $name['name'] ?? '';
            }
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $parts = preg_split('/\s+/u', $name, 2);
            $firstName = $parts[0] ?? $name;
            $lastName = $parts[1] ?? '';

            $author = Author::firstOrCreate(
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ],
                [
                    'middle_name' => null,
                    'biography' => null,
                    'birth_date' => null,
                    'death_date' => null,
                    'nationality' => null,
                    'photo' => null,
                    'website' => null,
                    'awards' => null,
                    'is_featured' => false,
                ]
            );
            $authorIds[] = $author->id;
            if ($primaryAuthorId === null) {
                $primaryAuthorId = $author->id;
            }
        }

        if ($primaryAuthorId !== null) {
            $book->author_id = $primaryAuthorId;
            $book->author = $authorString;
            $book->save();
        }
        if (! empty($authorIds)) {
            $book->authors()->syncWithoutDetaching($authorIds);
        }

        $categoryIds = [];
        foreach ($data['categories'] ?? [] as $categoryName) {
            if (! is_string($categoryName) || trim($categoryName) === '') {
                continue;
            }
            $slug = Str::slug($categoryName);
            $category = Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $categoryName,
                    'description' => null,
                    'color' => '#3B82F6',
                    'icon' => null,
                    'sort_order' => 0,
                    'is_active' => true,
                ]
            );
            $categoryIds[] = $category->id;
        }
        if (! empty($categoryIds)) {
            $book->categories()->syncWithoutDetaching($categoryIds);
        }
    }
}
