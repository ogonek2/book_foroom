<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $publications = [
            [
                'title' => 'Размышления о современной литературе',
                'content' => 'В современной литературе мы наблюдаем интересные тенденции...',
                'type' => 'article',
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Обзор книги "1984" Джорджа Оруэлла',
                'content' => 'Роман "1984" остается актуальным и сегодня...',
                'type' => 'review',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Мои впечатления от чтения "Войны и мира"',
                'content' => 'Толстой создал поистине эпическое произведение...',
                'type' => 'essay',
                'status' => 'published',
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Короткий рассказ: "Последний день"',
                'content' => 'Он проснулся от звука дождя...',
                'type' => 'story',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Анализ творчества Достоевского',
                'content' => 'Достоевский - один из величайших писателей...',
                'type' => 'article',
                'status' => 'draft',
                'published_at' => null,
            ],
            [
                'title' => 'Рецензия на "Мастера и Маргариту"',
                'content' => 'Булгаков создал уникальное произведение...',
                'type' => 'review',
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($publications as $publicationData) {
            Publication::create([
                'user_id' => $users->random()->id,
                'title' => $publicationData['title'],
                'content' => $publicationData['content'],
                'type' => $publicationData['type'],
                'status' => $publicationData['status'],
                'published_at' => $publicationData['published_at'],
            ]);
        }
    }
}
