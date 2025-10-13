<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        $quotes = [
            [
                'content' => 'В начале было Слово, и Слово было у Бога, и Слово было Бог.',
                'page_number' => 1,
            ],
            [
                'content' => 'Человек рождается свободным, а между тем везде он в оковах.',
                'page_number' => 15,
            ],
            [
                'content' => 'Быть или не быть — вот в чем вопрос.',
                'page_number' => 45,
            ],
            [
                'content' => 'Все счастливые семьи похожи друг на друга, каждая несчастливая семья несчастлива по-своему.',
                'page_number' => 1,
            ],
            [
                'content' => 'Война и мир — это не роман, еще менее историческая хроника, еще менее поэма.',
                'page_number' => 3,
            ],
            [
                'content' => 'Человек — это звучит гордо!',
                'page_number' => 78,
            ],
            [
                'content' => 'Красота спасет мир.',
                'page_number' => 156,
            ],
            [
                'content' => 'Любовь к родине начинается с семьи.',
                'page_number' => 23,
            ],
            [
                'content' => 'Знание — сила.',
                'page_number' => 12,
            ],
            [
                'content' => 'Время — деньги.',
                'page_number' => 67,
            ],
        ];

        foreach ($quotes as $quoteData) {
            $userId = $users->random()->id;
            $bookId = $books->random()->id;
            
            // Проверяем, нет ли уже такой же цитаты
            $existing = Quote::where('user_id', $userId)
                ->where('book_id', $bookId)
                ->where('content', $quoteData['content'])
                ->first();
                
            if (!$existing) {
                Quote::create([
                    'user_id' => $userId,
                    'book_id' => $bookId,
                    'content' => $quoteData['content'],
                    'page_number' => $quoteData['page_number'],
                    'is_public' => true,
                ]);
            }
        }
    }
}
