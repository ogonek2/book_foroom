<?php

namespace Database\Seeders;

use App\Models\Award;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $awards = [
            [
                'name' => 'Первый отзыв',
                'description' => 'Написал свой первый отзыв на книгу',
                'image' => null,
                'color' => '#3b82f6',
                'points' => 10,
                'sort_order' => 1,
            ],
            [
                'name' => 'Активный читатель',
                'description' => 'Написал 10 отзывов',
                'image' => null,
                'color' => '#10b981',
                'points' => 50,
                'sort_order' => 2,
            ],
            [
                'name' => 'Критик',
                'description' => 'Написал 50 отзывов',
                'image' => null,
                'color' => '#f59e0b',
                'points' => 100,
                'sort_order' => 3,
            ],
            [
                'name' => 'Цитатник',
                'description' => 'Добавил свою первую цитату',
                'image' => null,
                'color' => '#8b5cf6',
                'points' => 15,
                'sort_order' => 4,
            ],
            [
                'name' => 'Коллекционер цитат',
                'description' => 'Добавил 20 цитат',
                'image' => null,
                'color' => '#ec4899',
                'points' => 75,
                'sort_order' => 5,
            ],
            [
                'name' => 'Фактолог',
                'description' => 'Добавил интересный факт о книге',
                'image' => null,
                'color' => '#f97316',
                'points' => 20,
                'sort_order' => 6,
            ],
            [
                'name' => 'Общительный',
                'description' => 'Создал свою первую дискуссию',
                'image' => null,
                'color' => '#06b6d4',
                'points' => 25,
                'sort_order' => 7,
            ],
            [
                'name' => 'Библиотекарь',
                'description' => 'Создал свою первую коллекцию книг',
                'image' => null,
                'color' => '#84cc16',
                'points' => 30,
                'sort_order' => 8,
            ],
            [
                'name' => 'Популярный автор',
                'description' => 'Получил 100 лайков за свои отзывы',
                'image' => null,
                'color' => '#ef4444',
                'points' => 150,
                'sort_order' => 9,
            ],
            [
                'name' => 'Эксперт',
                'description' => 'Получил 500 лайков за свои отзывы',
                'image' => null,
                'color' => '#8b5cf6',
                'points' => 300,
                'sort_order' => 10,
            ],
            [
                'name' => 'Ветеран',
                'description' => 'На сайте более 1 года',
                'image' => null,
                'color' => '#6b7280',
                'points' => 200,
                'sort_order' => 11,
            ],
            [
                'name' => 'Легенда',
                'description' => 'На сайте более 3 лет',
                'image' => null,
                'color' => '#fbbf24',
                'points' => 500,
                'sort_order' => 12,
            ],
        ];

        foreach ($awards as $award) {
            Award::create($award);
        }
    }
}
