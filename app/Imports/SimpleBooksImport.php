<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimpleBooksImport implements ToModel, WithHeadingRow, SkipsOnError, WithChunkReading
{
    use Importable;

    private $rowCount = 0;
    private $errors = [];
    private $warnings = [];

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Пропускаем пустые строки
        if (empty($row['nazvanie'])) {
            Log::info("Пропущена пустая строка");
            return null;
        }

        $this->rowCount++;
        Log::info("Обрабатываем строку {$this->rowCount}: " . ($row['nazvanie'] ?? 'Без названия'));

        try {
            return DB::transaction(function () use ($row) {
                // Обработка данных
                $data = $this->processRowData($row);
                
                // Создаем и сохраняем книгу
                $book = Book::create($data);
                
                Log::info("Создана книга: {$book->title} (ID: {$book->id})");
                return $book;
            });
            
        } catch (\Exception $e) {
            Log::error("Ошибка импорта строки {$this->rowCount}: " . $e->getMessage());
            $this->errors[] = "Строка {$this->rowCount}: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Обработка данных строки
     */
    private function processRowData(array $row): array
    {
        // Обработка названия
        $title = trim($row['nazvanie'] ?? '');
        if (empty($title)) {
            throw new \Exception('Название книги обязательно');
        }

        // Обработка слага
        $slug = !empty($row['slag']) ? Str::slug($row['slag']) : Str::slug($title);
        
        // Проверяем уникальность слага
        $originalSlug = $slug;
        $counter = 1;
        while (Book::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Обработка описания
        $description = !empty($row['opisanie']) ? trim($row['opisanie']) : null;

        // Обработка автора (старого поля)
        $author = !empty($row['avtor_staryy']) ? trim($row['avtor_staryy']) : null;

        // Обработка ISBN
        $isbn = !empty($row['isbn']) ? trim($row['isbn']) : null;

        // Обработка года издания
        $publicationYear = $this->extractYear($row['god_izdaniya'] ?? '');

        // Обработка издательства
        $publisher = !empty($row['izdatelstvo']) ? trim($row['izdatelstvo']) : null;

        // Обработка обложки
        $coverImage = !empty($row['oblozhka']) ? trim($row['oblozhka']) : null;

        // Обработка языка (максимум 5 символов)
        $language = $this->mapLanguage($row['yazyk'] ?? '');

        // Обработка страниц
        $pages = $this->extractNumber($row['stranitsy'] ?? '');

        // Обработка рейтинга
        $rating = $this->extractRating($row['reiting'] ?? '');

        // Обработка количества рецензий
        $reviewsCount = $this->extractNumber($row['kolichestvo_recenziy'] ?? '') ?? 0;

        // Обработка категории
        $categoryId = $this->getOrCreateCategory($row['kategoriya'] ?? '');
        
        // Если категория обязательна, проверяем её наличие
        if (empty($categoryId)) {
            throw new \Exception('Категория обязательна для создания книги');
        }

        // Обработка автора (нового поля)
        $authorId = $this->getOrCreateAuthor($row['avtor'] ?? '');

        // Обработка рекомендуемой
        $isFeatured = $this->parseBoolean($row['rekomenduemaya'] ?? '');

        return [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'author' => $author,
            'isbn' => $isbn,
            'publication_year' => $publicationYear,
            'publisher' => $publisher,
            'cover_image' => $coverImage,
            'language' => $language,
            'pages' => $pages,
            'rating' => $rating,
            'reviews_count' => $reviewsCount,
            'category_id' => $categoryId,
            'author_id' => $authorId,
            'is_featured' => $isFeatured,
        ];
    }

    /**
     * Извлечь год из строки
     */
    private function extractYear($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        // Ищем 4-значное число
        if (preg_match('/\b(19|20)\d{2}\b/', $value, $matches)) {
            $year = (int) $matches[0];
            if ($year >= 1900 && $year <= (date('Y') + 10)) {
                return $year;
            }
        }

        return null;
    }

    /**
     * Извлечь число из строки
     */
    private function extractNumber($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        // Удаляем все кроме цифр
        $number = preg_replace('/[^0-9]/', '', $value);
        
        return !empty($number) ? (int) $number : null;
    }

    /**
     * Извлечь рейтинг из строки
     */
    private function extractRating($value): float
    {
        if (empty($value)) {
            return 0.0;
        }

        // Заменяем запятую на точку
        $value = str_replace(',', '.', $value);
        
        // Извлекаем число
        if (preg_match('/(\d+\.?\d*)/', $value, $matches)) {
            $rating = (float) $matches[1];
            return min(5.0, max(0.0, $rating));
        }

        return 0.0;
    }

    /**
     * Получить или создать категорию
     */
    private function getOrCreateCategory($categoryName): ?int
    {
        if (empty($categoryName)) {
            return null;
        }

        $category = Category::where('name', trim($categoryName))->first();
        
        if (!$category) {
            $category = Category::create([
                'name' => trim($categoryName),
                'slug' => Str::slug($categoryName),
                'description' => 'Автоматически создана при импорте',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-book-open',
                'sort_order' => 0,
                'is_active' => true,
            ]);
            
            Log::info("Создана новая категория: {$category->name} (ID: {$category->id})");
        }

        return $category->id;
    }

    /**
     * Получить или создать автора
     */
    private function getOrCreateAuthor($authorName): ?int
    {
        if (empty($authorName)) {
            return null;
        }

        $authorName = trim($authorName);
        
        // Если только одно слово, добавляем "Неизвестный"
        if (str_word_count($authorName) == 1) {
            $authorName = $authorName . ' Неизвестный';
        }

        $nameParts = explode(' ', $authorName, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        $author = Author::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->first();

        if (!$author) {
            $author = Author::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'slug' => Str::slug($firstName . ' ' . $lastName),
                'biography' => 'Автоматически создан при импорте',
                'is_featured' => false,
            ]);
            
            Log::info("Создан новый автор: {$author->first_name} {$author->last_name} (ID: {$author->id})");
        }

        return $author->id;
    }

    /**
     * Маппинг языка
     */
    private function mapLanguage($value): string
    {
        if (empty($value)) {
            return 'ru';
        }

        $value = strtolower(trim($value));
        
        $languageMap = [
            'русский' => 'ru',
            'russian' => 'ru',
            'украинский' => 'uk',
            'ukrainian' => 'uk',
            'английский' => 'en',
            'english' => 'en',
            'немецкий' => 'de',
            'german' => 'de',
            'французский' => 'fr',
            'french' => 'fr',
            'испанский' => 'es',
            'spanish' => 'es',
        ];

        return $languageMap[$value] ?? substr($value, 0, 5);
    }

    /**
     * Парсинг булевого значения
     */
    private function parseBoolean($value): bool
    {
        if (empty($value)) {
            return false;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['да', 'yes', 'true', '1', 'on']);
    }

    /**
     * Получить количество обработанных строк
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Получить ошибки
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Получить предупреждения
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Размер чанка для чтения
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Обработка ошибок
     */
    public function onError(\Throwable $e): void
    {
        Log::error("Ошибка импорта: " . $e->getMessage());
    }

}
