<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            // Художественная литература
            [
                'title' => '1984',
                'author' => 'Джордж Оруэлл',
                'description' => 'Антиутопический роман о тоталитарном обществе будущего.',
                'publication_year' => 1949,
                'publisher' => 'Secker & Warburg',
                'pages' => 328,
                'isbn' => '978-0-452-28423-4',
                'category_slug' => 'fiction',
                'is_featured' => true,
            ],
            [
                'title' => 'Мастер и Маргарита',
                'author' => 'Михаил Булгаков',
                'description' => 'Философский роман о добре и зле, любви и предательстве.',
                'publication_year' => 1967,
                'publisher' => 'Московский рабочий',
                'pages' => 384,
                'isbn' => '978-5-17-090666-0',
                'category_slug' => 'fiction',
                'is_featured' => true,
            ],
            [
                'title' => 'Война и мир',
                'author' => 'Лев Толстой',
                'description' => 'Эпический роман о русском обществе эпохи войн против Наполеона.',
                'publication_year' => 1869,
                'publisher' => 'Русский вестник',
                'pages' => 1225,
                'isbn' => '978-5-17-090666-1',
                'category_slug' => 'fiction',
                'is_featured' => true,
            ],

            // Научная литература
            [
                'title' => 'Краткая история времени',
                'author' => 'Стивен Хокинг',
                'description' => 'Популярное изложение космологии и физики.',
                'publication_year' => 1988,
                'publisher' => 'Bantam Books',
                'pages' => 256,
                'isbn' => '978-0-553-38016-3',
                'category_slug' => 'science',
                'is_featured' => true,
            ],
            [
                'title' => 'Происхождение видов',
                'author' => 'Чарльз Дарвин',
                'description' => 'Фундаментальный труд по теории эволюции.',
                'publication_year' => 1859,
                'publisher' => 'John Murray',
                'pages' => 502,
                'isbn' => '978-0-14-043912-0',
                'category_slug' => 'science',
                'is_featured' => false,
            ],

            // Бизнес и саморазвитие
            [
                'title' => 'Богатый папа, бедный папа',
                'author' => 'Роберт Кийосаки',
                'description' => 'Книга о финансовой грамотности и инвестициях.',
                'publication_year' => 1997,
                'publisher' => 'Warner Books',
                'pages' => 207,
                'isbn' => '978-0-446-67745-0',
                'category_slug' => 'business',
                'is_featured' => true,
            ],
            [
                'title' => '7 навыков высокоэффективных людей',
                'author' => 'Стивен Кови',
                'description' => 'Классика личностного развития и лидерства.',
                'publication_year' => 1989,
                'publisher' => 'Free Press',
                'pages' => 432,
                'isbn' => '978-1-982-13736-2',
                'category_slug' => 'business',
                'is_featured' => true,
            ],

            // История и биографии
            [
                'title' => 'История России',
                'author' => 'Сергей Соловьев',
                'description' => 'Многотомный труд по истории России.',
                'publication_year' => 1851,
                'publisher' => 'Типография И. Н. Скороходова',
                'pages' => 1200,
                'isbn' => '978-5-17-090666-2',
                'category_slug' => 'history',
                'is_featured' => false,
            ],
            [
                'title' => 'Стив Джобс',
                'author' => 'Уолтер Айзексон',
                'description' => 'Биография основателя Apple.',
                'publication_year' => 2011,
                'publisher' => 'Simon & Schuster',
                'pages' => 656,
                'isbn' => '978-1-4516-4853-9',
                'category_slug' => 'history',
                'is_featured' => true,
            ],

            // Техническая литература
            [
                'title' => 'Чистый код',
                'author' => 'Роберт Мартин',
                'description' => 'Руководство по написанию качественного кода.',
                'publication_year' => 2008,
                'publisher' => 'Prentice Hall',
                'pages' => 464,
                'isbn' => '978-0-13-235088-4',
                'category_slug' => 'technical',
                'is_featured' => true,
            ],
            [
                'title' => 'Искусство программирования',
                'author' => 'Дональд Кнут',
                'description' => 'Фундаментальный труд по алгоритмам и программированию.',
                'publication_year' => 1968,
                'publisher' => 'Addison-Wesley',
                'pages' => 650,
                'isbn' => '978-0-201-89683-1',
                'category_slug' => 'technical',
                'is_featured' => false,
            ],
        ];

        foreach ($books as $bookData) {
            $category = Category::where('slug', $bookData['category_slug'])->first();
            
            if ($category) {
                unset($bookData['category_slug']);
                $bookData['category_id'] = $category->id;
                $bookData['rating'] = rand(30, 50) / 10; // 3.0 - 5.0
                $bookData['reviews_count'] = rand(5, 50);
                
                Book::firstOrCreate(
                    ['title' => $bookData['title'], 'author' => $bookData['author']],
                    $bookData
                );
            }
        }
    }
}
