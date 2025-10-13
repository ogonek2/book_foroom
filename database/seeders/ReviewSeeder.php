<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        $reviews = [
            // Основные рецензии
            [
                'content' => 'Потрясающая книга! Автор мастерски создал атмосферу и закрутил интригу. Персонажи очень живые и реалистичные. Рекомендую всем любителям фантастики.',
                'rating' => 5,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Интересная идея, но исполнение подкачало. Середина книги растянута, а концовка слишком поспешная. В целом неплохо, но могло быть лучше.',
                'rating' => 3,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Классика жанра! Перечитываю уже третий раз и каждый раз нахожу что-то новое. Язык автора просто завораживает.',
                'rating' => 5,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Не смог дочитать до конца. Слишком сложно написано, постоянно приходится возвращаться к предыдущим страницам. Не для меня.',
                'rating' => 2,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Отличная книга для начинающих! Все объяснено простым языком, много примеров. Автор явно знает свое дело.',
                'rating' => 4,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Философская глубина поражает. Каждая страница заставляет задуматься о жизни. Рекомендую всем, кто ищет смысл.',
                'rating' => 5,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Хорошая книга, но есть несколько моментов, которые можно было бы улучшить. В целом рекомендую.',
                'rating' => 4,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Сюжет предсказуемый, персонажи шаблонные. Не впечатлило. Возможно, для подростков будет интересно.',
                'rating' => 2,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Мастерство автора на высоте! Каждое предложение продумано, каждая деталь важна. Истинное произведение искусства.',
                'rating' => 5,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Интересная концепция, но реализация подкачала. Много воды, мало сути. Возможно, сократить в два раза было бы лучше.',
                'rating' => 3,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Книга изменила мое мировоззрение. Рекомендую всем, кто готов к серьезному чтению. Не для развлечения.',
                'rating' => 5,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Неплохо написано, но не хватает динамики. Слишком много описаний, мало действия. Для терпеливых читателей.',
                'rating' => 3,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Отличный пример того, как нужно писать в этом жанре. Все элементы на месте: сюжет, персонажи, стиль.',
                'rating' => 4,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Разочарован. Ожидал большего от такого автора. Сюжет банальный, диалоги неестественные.',
                'rating' => 2,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
            [
                'content' => 'Книга затягивает с первых страниц! Не мог оторваться до самого конца. Обязательно прочитаю другие работы автора.',
                'rating' => 5,
                'book_id' => $books->random()->id,
                'user_id' => $users->random()->id,
            ],
        ];

        // Создаем основные рецензии
        foreach ($reviews as $reviewData) {
            // Проверяем, есть ли уже рецензия от этого пользователя на эту книгу
            $existing = Review::where('book_id', $reviewData['book_id'])
                ->where('user_id', $reviewData['user_id'])
                ->whereNull('parent_id')
                ->first();
            
            if (!$existing) {
                Review::create($reviewData);
            }
        }

        // Создаем ответы на рецензии (replies)
        $mainReviews = Review::whereNull('parent_id')->get();
        
        $replies = [
            'Согласен с вашим мнением!',
            'Интересная точка зрения.',
            'Не совсем согласен, но уважаю ваше мнение.',
            'Спасибо за отзыв!',
            'Хорошо подмечено.',
            'Полностью поддерживаю!',
            'Интересно, а что думают другие?',
            'Спасибо за детальный разбор.',
            'Очень полезный комментарий.',
            'Согласен на 100%!',
        ];

        // Создаем 2-3 ответа для каждой основной рецензии
        foreach ($mainReviews as $review) {
            $repliesCount = rand(1, 3);
            
            for ($i = 0; $i < $repliesCount; $i++) {
                Review::create([
                    'content' => $replies[array_rand($replies)],
                    'rating' => null, // Ответы обычно без рейтинга
                    'book_id' => $review->book_id,
                    'user_id' => $users->random()->id,
                    'parent_id' => $review->id,
                ]);
            }
        }

        // Создаем несколько гостевых рецензий (без user_id)
        $guestReviews = [
            [
                'content' => 'Отличная книга! Прочитал за один вечер.',
                'rating' => 4,
                'book_id' => $books->random()->id,
                'user_id' => null,
            ],
            [
                'content' => 'Не понравилось. Слишком сложно для понимания.',
                'rating' => 2,
                'book_id' => $books->random()->id,
                'user_id' => null,
            ],
            [
                'content' => 'Рекомендую всем! Очень интересно написано.',
                'rating' => 5,
                'book_id' => $books->random()->id,
                'user_id' => null,
            ],
        ];

        foreach ($guestReviews as $guestReview) {
            // Гостевые рецензии можно создавать всегда, так как у них нет user_id для проверки уникальности
            // Но добавим проверку на случай если контент дублируется
            $existing = Review::where('book_id', $guestReview['book_id'])
                ->where('content', $guestReview['content'])
                ->whereNull('user_id')
                ->first();
                
            if (!$existing) {
                Review::create($guestReview);
            }
        }
    }
}