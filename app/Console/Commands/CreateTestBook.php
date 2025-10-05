<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Console\Command;

class CreateTestBook extends Command
{
    protected $signature = 'test:create-book';
    protected $description = 'Create a test book if none exists';

    public function handle()
    {
        // Проверяем, есть ли книги
        $bookCount = Book::count();
        $this->info("Total books in database: {$bookCount}");
        
        // Проверяем конкретно книгу "kobzar"
        $kobzarBook = Book::where('slug', 'kobzar')->first();
        if ($kobzarBook) {
            $this->info("Book 'kobzar' exists with ID: {$kobzarBook->id}");
        } else {
            $this->warn("Book 'kobzar' does not exist!");
        }

        // Показываем все книги
        $books = Book::select('id', 'title', 'slug')->get();
        $this->info("All books:");
        foreach ($books as $book) {
            $this->line("- ID: {$book->id}, Title: {$book->title}, Slug: {$book->slug}");
        }

        // Создаем категорию, если её нет
        $category = Category::firstOrCreate(
            ['slug' => 'fiction'],
            [
                'name' => 'Художня література',
                'description' => 'Художні твори різних жанрів',
                'slug' => 'fiction',
                'is_active' => true
            ]
        );

        // Создаем книгу "Кобзар", если её нет
        if (!$kobzarBook) {
            $book = Book::create([
                'title' => 'Кобзар',
                'slug' => 'kobzar',
                'description' => 'Збірка поетичних творів великого українського поета, яка стала символом української літератури.',
                'author' => 'Тарас Шевченко',
                'isbn' => '978-966-03-1234-5',
                'publication_year' => 1840,
                'publisher' => 'Видавництво Шевченка',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'language' => 'uk',
                'pages' => 280,
                'rating' => 4.5,
                'reviews_count' => 0,
                'category_id' => $category->id,
                'is_featured' => true
            ]);

            $this->info("Book 'Кобзар' created with ID: {$book->id}");
        }
    }
}
