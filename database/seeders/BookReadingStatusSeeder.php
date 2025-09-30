<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookReadingStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookReadingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->count() === 0 || $books->count() === 0) {
            $this->command->warn('Нет пользователей или книг для создания статусов чтения');
            return;
        }

        $statuses = ['read', 'reading', 'want_to_read'];
        
        foreach ($users as $user) {
            // Случайно выбираем 5-15 книг для каждого пользователя
            $userBooks = $books->random(rand(5, min(15, $books->count())));
            
            foreach ($userBooks as $book) {
                $status = $statuses[array_rand($statuses)];
                
                $readingStatus = BookReadingStatus::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'status' => $status,
                    'rating' => $status === 'read' ? rand(1, 10) : null,
                    'review' => $status === 'read' && rand(0, 1) ? $this->generateReview() : null,
                    'started_at' => $status !== 'want_to_read' ? now()->subDays(rand(1, 30)) : null,
                    'finished_at' => $status === 'read' ? now()->subDays(rand(1, 10)) : null,
                ]);
            }
        }

        $this->command->info('Статусы чтения книг созданы успешно!');
    }

    private function generateReview(): string
    {
        $reviews = [
            'Отличная книга! Очень рекомендую к прочтению.',
            'Интересный сюжет, но концовка немного разочаровала.',
            'Потрясающая книга! Не мог оторваться до самого конца.',
            'Хорошая книга, но есть моменты, которые можно было бы улучшить.',
            'Классика жанра! Обязательно к прочтению.',
            'Не очень понравилась, слишком затянуто.',
            'Великолепная книга! Автор мастерски передал атмосферу.',
            'Интересная идея, но исполнение подкачало.',
            'Одна из лучших книг, которые я читал!',
            'Неплохая книга, но ожидал большего.',
        ];

        return $reviews[array_rand($reviews)];
    }
}