<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\UserLibrary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        $statuses = ['want_to_read', 'reading', 'read', 'favorite'];

        // Для каждого пользователя добавляем случайные книги в библиотеку
        foreach ($users as $user) {
            // Каждый пользователь добавляет от 3 до 8 книг
            $booksCount = rand(3, 8);
            $userBooks = $books->random($booksCount);

            foreach ($userBooks as $book) {
                UserLibrary::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'status' => $statuses[array_rand($statuses)],
                    'added_at' => now()->subDays(rand(1, 365)), // Случайная дата в прошлом году
                ]);
            }
        }

        // Добавляем несколько популярных книг в библиотеки многих пользователей
        $popularBooks = $books->random(3);
        foreach ($popularBooks as $book) {
            $usersCount = rand(3, $users->count());
            $randomUsers = $users->random($usersCount);

            foreach ($randomUsers as $user) {
                // Проверяем, не добавлена ли уже эта книга
                if (!UserLibrary::where('user_id', $user->id)
                    ->where('book_id', $book->id)
                    ->exists()) {
                    
                    UserLibrary::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'status' => $statuses[array_rand($statuses)],
                        'added_at' => now()->subDays(rand(1, 180)),
                    ]);
                }
            }
        }
    }
}