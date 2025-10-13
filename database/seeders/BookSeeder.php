<?php

namespace Database\Seeders;

use App\Models\Author;
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
            // Українські автори - Тарас Шевченко
            [
                'title' => 'Кобзар',
                'author_name' => 'Тарас Шевченко',
                'description' => 'Збірка поетичних творів великого українського поета, яка стала символом української літератури.',
                'publication_year' => 1840,
                'publisher' => 'Видавництво Шевченка',
                'pages' => 280,
                'isbn' => '978-966-03-1234-5',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Гайдамаки',
                'author_name' => 'Тарас Шевченко',
                'description' => 'Історична поема про повстання гайдамаків у XVIII столітті.',
                'publication_year' => 1841,
                'publisher' => 'Видавництво Шевченка',
                'pages' => 150,
                'isbn' => '978-966-03-1235-2',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Леся Українка
            [
                'title' => 'Лісова пісня',
                'author_name' => 'Леся Українка',
                'description' => 'Драматична поема про кохання та природу, один з найвідоміших творів автора.',
                'publication_year' => 1911,
                'publisher' => 'Видавництво Українка',
                'pages' => 120,
                'isbn' => '978-966-03-1236-9',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Бояриня',
                'author_name' => 'Леся Українка',
                'description' => 'Драматична поема про долю української жінки в історичному контексті.',
                'publication_year' => 1910,
                'publisher' => 'Видавництво Українка',
                'pages' => 95,
                'isbn' => '978-966-03-1237-6',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Іван Франко
            [
                'title' => 'Захар Беркут',
                'author_name' => 'Іван Франко',
                'description' => 'Історичний роман про боротьбу українського народу за незалежність.',
                'publication_year' => 1883,
                'publisher' => 'Видавництво Франка',
                'pages' => 320,
                'isbn' => '978-966-03-1238-3',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Олександр Пушкін
            [
                'title' => 'Євгеній Онєгін',
                'author_name' => 'Олександр Пушкін',
                'description' => 'Роман у віршах, який вважається одним з найвидатніших творів російської літератури.',
                'publication_year' => 1833,
                'publisher' => 'Російський вісник',
                'pages' => 400,
                'isbn' => '978-966-03-1239-0',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Капітанська дочка',
                'author_name' => 'Олександр Пушкін',
                'description' => 'Історичний роман про повстання Пугачова.',
                'publication_year' => 1836,
                'publisher' => 'Російський вісник',
                'pages' => 250,
                'isbn' => '978-966-03-1240-6',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Лев Толстой
            [
                'title' => 'Війна і мир',
                'author_name' => 'Лев Толстой',
                'description' => 'Епічний роман про російське суспільство епохи війн проти Наполеона.',
                'publication_year' => 1869,
                'publisher' => 'Російський вісник',
                'pages' => 1225,
                'isbn' => '978-966-03-1241-3',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Анна Кареніна',
                'author_name' => 'Лев Толстой',
                'description' => 'Роман про любов, зраду та суспільні умовності.',
                'publication_year' => 1877,
                'publisher' => 'Російський вісник',
                'pages' => 864,
                'isbn' => '978-966-03-1242-0',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Федір Достоєвський
            [
                'title' => 'Злочин і кара',
                'author_name' => 'Федір Достоєвський',
                'description' => 'Психологічний роман про студента, який вбиває лихварку.',
                'publication_year' => 1866,
                'publisher' => 'Російський вісник',
                'pages' => 671,
                'isbn' => '978-966-03-1243-7',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Ідіот',
                'author_name' => 'Федір Достоєвський',
                'description' => 'Роман про князя Мишкіна, якого називають ідіотом через його доброту.',
                'publication_year' => 1869,
                'publisher' => 'Російський вісник',
                'pages' => 667,
                'isbn' => '978-966-03-1244-4',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Вільям Шекспір
            [
                'title' => 'Ромео і Джульєтта',
                'author_name' => 'Вільям Шекспір',
                'description' => 'Трагедія про кохання двох молодих людей з ворогуючих родин.',
                'publication_year' => 1597,
                'publisher' => 'First Folio',
                'pages' => 120,
                'isbn' => '978-966-03-1245-1',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Гамлет',
                'author_name' => 'Вільям Шекспір',
                'description' => 'Трагедія про принца Данії, який мстить за смерть батька.',
                'publication_year' => 1603,
                'publisher' => 'First Folio',
                'pages' => 200,
                'isbn' => '978-966-03-1246-8',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Чарльз Діккенс
            [
                'title' => 'Олівер Твіст',
                'author_name' => 'Чарльз Діккенс',
                'description' => 'Роман про сироту Олівера та його пригоди в Лондоні.',
                'publication_year' => 1838,
                'publisher' => 'Chapman & Hall',
                'pages' => 480,
                'isbn' => '978-966-03-1247-5',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Великі сподівання',
                'author_name' => 'Чарльз Діккенс',
                'description' => 'Роман про хлопчика Піпа та його життєвий шлях.',
                'publication_year' => 1861,
                'publisher' => 'Chapman & Hall',
                'pages' => 544,
                'isbn' => '978-966-03-1248-2',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Джейн Остін
            [
                'title' => 'Гордість і упередження',
                'author_name' => 'Джейн Остін',
                'description' => 'Роман про Елізабет Беннет та містера Дарсі.',
                'publication_year' => 1813,
                'publisher' => 'T. Egerton',
                'pages' => 432,
                'isbn' => '978-966-03-1249-9',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Віктор Гюго
            [
                'title' => 'Знедолені',
                'author_name' => 'Віктор Гюго',
                'description' => 'Епічний роман про Жана Вальжана та його життєвий шлях.',
                'publication_year' => 1862,
                'publisher' => 'A. Lacroix',
                'pages' => 1463,
                'isbn' => '978-966-03-1250-5',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Собор Паризької Богоматері',
                'author_name' => 'Віктор Гюго',
                'description' => 'Роман про Квазімодо, Есмеральду та Фролло.',
                'publication_year' => 1831,
                'publisher' => 'Gosselin',
                'pages' => 940,
                'isbn' => '978-966-03-1251-2',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Олександр Дюма
            [
                'title' => 'Три мушкетери',
                'author_name' => 'Олександр Дюма',
                'description' => 'Пригодницький роман про д\'Артаньяна та трьох мушкетерів.',
                'publication_year' => 1844,
                'publisher' => 'Le Siècle',
                'pages' => 625,
                'isbn' => '978-966-03-1252-9',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Ернест Хемінгуей
            [
                'title' => 'Старий і море',
                'author_name' => 'Ернест Хемінгуей',
                'description' => 'Повість про старого рибалку Сантьяго та його боротьбу з великою рибою.',
                'publication_year' => 1952,
                'publisher' => 'Charles Scribner\'s Sons',
                'pages' => 127,
                'isbn' => '978-966-03-1253-6',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Прощавай, зброє!',
                'author_name' => 'Ернест Хемінгуей',
                'description' => 'Роман про кохання під час Першої світової війни.',
                'publication_year' => 1929,
                'publisher' => 'Charles Scribner\'s Sons',
                'pages' => 355,
                'isbn' => '978-966-03-1254-3',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Марк Твен
            [
                'title' => 'Пригоди Тома Сойєра',
                'author_name' => 'Марк Твен',
                'description' => 'Пригодницький роман про хлопчика Тома та його друзів.',
                'publication_year' => 1876,
                'publisher' => 'American Publishing Company',
                'pages' => 274,
                'isbn' => '978-966-03-1255-0',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Франц Кафка
            [
                'title' => 'Перетворення',
                'author_name' => 'Франц Кафка',
                'description' => 'Оповідання про Грегора Замзу, який прокидається у вигляді великого комаха.',
                'publication_year' => 1915,
                'publisher' => 'Kurt Wolff Verlag',
                'pages' => 55,
                'isbn' => '978-966-03-1256-7',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Процес',
                'author_name' => 'Франц Кафка',
                'description' => 'Роман про Йозефа К., який раптово опиняється втягнутим у незрозумілий судовий процес.',
                'publication_year' => 1925,
                'publisher' => 'Verlag Die Schmiede',
                'pages' => 316,
                'isbn' => '978-966-03-1257-4',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Йоганн Гете
            [
                'title' => 'Фауст',
                'author_name' => 'Йоганн Гете',
                'description' => 'Трагедія про доктора Фауста та його угоду з Мефістофелем.',
                'publication_year' => 1808,
                'publisher' => 'Cotta',
                'pages' => 461,
                'isbn' => '978-966-03-1258-1',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Габріель Гарсія Маркес
            [
                'title' => 'Сто років самотності',
                'author_name' => 'Габріель Гарсія Маркес',
                'description' => 'Роман про сім\'ю Буендіа та історію міста Макондо.',
                'publication_year' => 1967,
                'publisher' => 'Editorial Sudamericana',
                'pages' => 417,
                'isbn' => '978-966-03-1259-8',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Мілан Кундера
            [
                'title' => 'Невиносима легкість буття',
                'author_name' => 'Мілан Кундера',
                'description' => 'Філософський роман про кохання, свободу та сенс життя.',
                'publication_year' => 1984,
                'publisher' => 'Gallimard',
                'pages' => 314,
                'isbn' => '978-966-03-1260-4',
                'category_slug' => 'fiction',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Наукова література
            [
                'title' => 'Коротка історія часу',
                'author_name' => 'Стівен Хокінг',
                'description' => 'Популярне виклад космології та фізики.',
                'publication_year' => 1988,
                'publisher' => 'Bantam Books',
                'pages' => 256,
                'isbn' => '978-966-03-1261-1',
                'category_slug' => 'science',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Походження видів',
                'author_name' => 'Чарльз Дарвін',
                'description' => 'Фундаментальна праця з теорії еволюції.',
                'publication_year' => 1859,
                'publisher' => 'John Murray',
                'pages' => 502,
                'isbn' => '978-966-03-1262-8',
                'category_slug' => 'science',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],

            // Бізнес та саморозвиток
            [
                'title' => 'Багатий тато, бідний тато',
                'author_name' => 'Роберт Кійосакі',
                'description' => 'Книга про фінансову грамотність та інвестиції.',
                'publication_year' => 1997,
                'publisher' => 'Warner Books',
                'pages' => 207,
                'isbn' => '978-966-03-1263-5',
                'category_slug' => 'business',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => '7 навичок високоефективних людей',
                'author_name' => 'Стівен Кові',
                'description' => 'Класика особистісного розвитку та лідерства.',
                'publication_year' => 1989,
                'publisher' => 'Free Press',
                'pages' => 432,
                'isbn' => '978-966-03-1264-2',
                'category_slug' => 'business',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Історія та біографії
            [
                'title' => 'Історія Росії',
                'author_name' => 'Сергій Соловйов',
                'description' => 'Багатотомна праця з історії Росії.',
                'publication_year' => 1851,
                'publisher' => 'Друк. І. Н. Скороходова',
                'pages' => 1200,
                'isbn' => '978-966-03-1265-9',
                'category_slug' => 'history',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],
            [
                'title' => 'Стів Джобс',
                'author_name' => 'Волтер Айзексон',
                'description' => 'Біографія засновника Apple.',
                'publication_year' => 2011,
                'publisher' => 'Simon & Schuster',
                'pages' => 656,
                'isbn' => '978-966-03-1266-6',
                'category_slug' => 'history',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],

            // Технічна література
            [
                'title' => 'Чистий код',
                'author_name' => 'Роберт Мартін',
                'description' => 'Керівництво з написання якісного коду.',
                'publication_year' => 2008,
                'publisher' => 'Prentice Hall',
                'pages' => 464,
                'isbn' => '978-966-03-1267-3',
                'category_slug' => 'technical',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => true,
            ],
            [
                'title' => 'Мистецтво програмування',
                'author_name' => 'Дональд Кнут',
                'description' => 'Фундаментальна праця з алгоритмів та програмування.',
                'publication_year' => 1968,
                'publisher' => 'Addison-Wesley',
                'pages' => 650,
                'isbn' => '978-966-03-1268-0',
                'category_slug' => 'technical',
                'cover_image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop',
                'is_featured' => false,
            ],
        ];

        foreach ($books as $bookData) {
            $category = Category::where('slug', $bookData['category_slug'])->first();
            
            // Найти автора по имени и фамилии
            $authorNameParts = explode(' ', $bookData['author_name']);
            $author = Author::where('first_name', $authorNameParts[0])
                          ->where('last_name', $authorNameParts[1])
                          ->first();
            
            if ($category) {
                $categoryId = $category->id;
                unset($bookData['category_slug'], $bookData['author_name']);
                $bookData['author_id'] = $author ? $author->id : null;
                $bookData['author'] = $author ? $author->full_name : 'Невідомий автор';
                $bookData['rating'] = rand(30, 50) / 10; // 3.0 - 5.0
                $bookData['reviews_count'] = rand(5, 50);
                $bookData['language'] = 'uk';
                
                $book = Book::firstOrCreate(
                    ['title' => $bookData['title'], 'author' => $bookData['author']],
                    $bookData
                );
                
                // Прикрепляем категорию через many-to-many связь
                $book->categories()->syncWithoutDetaching([$categoryId]);
            }
        }
    }
}
