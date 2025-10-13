<?php

namespace Database\Seeders;

use App\Models\Bookstore;
use App\Models\BookPrice;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookstoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем магазины
        $yakaboo = Bookstore::create([
            'name' => 'Yakaboo',
            'description' => 'Онлайн книжковий магазин',
            'website_url' => 'https://www.yakaboo.ua',
            'is_active' => true,
        ]);

        $bookE = Bookstore::create([
            'name' => 'Book E',
            'description' => 'Електронні книги',
            'website_url' => 'https://www.booke.com',
            'is_active' => true,
        ]);

        $bukva = Bookstore::create([
            'name' => 'Буква',
            'description' => 'Книжковий магазин',
            'website_url' => 'https://www.bukva.com',
            'is_active' => true,
        ]);

        // Получаем первую книгу для тестирования
        $book = Book::first();
        
        if ($book) {
            // Создаем цены для первой книги
            BookPrice::create([
                'book_id' => $book->id,
                'bookstore_id' => $yakaboo->id,
                'price' => 250.00,
                'currency' => 'UAH',
                'product_url' => 'https://www.yakaboo.ua/books/' . $book->slug,
                'is_available' => true,
            ]);

            BookPrice::create([
                'book_id' => $book->id,
                'bookstore_id' => $bookE->id,
                'price' => 245.00,
                'currency' => 'UAH',
                'product_url' => 'https://www.booke.com/books/' . $book->slug,
                'is_available' => true,
            ]);

            BookPrice::create([
                'book_id' => $book->id,
                'bookstore_id' => $bukva->id,
                'price' => 265.00,
                'currency' => 'UAH',
                'product_url' => 'https://www.bukva.com/books/' . $book->slug,
                'is_available' => true,
            ]);
        }
    }
}
