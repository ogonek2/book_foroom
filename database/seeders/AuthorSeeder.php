<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            // Українські автори
            [
                'first_name' => 'Тарас',
                'last_name' => 'Шевченко',
                'middle_name' => 'Григорович',
                'biography' => 'Великий український поет, прозаїк, художник, громадський діяч. Народився в селі Моринці на Черкащині. Автор поеми "Кобзар", яка стала символом української літератури.',
                'birth_date' => '1814-03-09',
                'death_date' => '1861-03-10',
                'nationality' => 'Україна',
                'photo' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Національний герой України',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Леся',
                'last_name' => 'Українка',
                'middle_name' => 'Петрівна',
                'biography' => 'Видатна українська поетеса, драматург, перекладач, громадська діячка. Одна з найвидатніших постатей української літератури кінця XIX — початку XX століття.',
                'birth_date' => '1871-02-25',
                'death_date' => '1913-08-01',
                'nationality' => 'Україна',
                'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Класик української літератури',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Іван',
                'last_name' => 'Франко',
                'middle_name' => 'Якович',
                'biography' => 'Видатний український письменник, поет, перекладач, громадський діяч, вчений. Автор поеми "Мойсей", роману "Захар Беркут" та багатьох інших творів.',
                'birth_date' => '1856-08-27',
                'death_date' => '1916-05-28',
                'nationality' => 'Україна',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Нобелівська премія з літератури (1926)',
                'is_featured' => true,
            ],
            
            // Російські автори
            [
                'first_name' => 'Олександр',
                'last_name' => 'Пушкін',
                'middle_name' => 'Сергійович',
                'biography' => 'Великий російський поет, драматург, прозаїк. Автор поеми "Євгеній Онєгін", роману "Капітанська дочка", багатьох віршів та оповідань.',
                'birth_date' => '1799-06-06',
                'death_date' => '1837-02-10',
                'nationality' => 'Росія',
                'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Засновник нової російської літератури',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Лев',
                'last_name' => 'Толстой',
                'middle_name' => 'Миколайович',
                'biography' => 'Великий російський письменник, мислитель. Автор романів "Війна і мир", "Анна Кареніна", повістей "Смерть Івана Ілліча", "Хаджи-Мурат" та інших.',
                'birth_date' => '1828-09-09',
                'death_date' => '1910-11-20',
                'nationality' => 'Росія',
                'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Нобелівська премія з літератури (1901)',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Федір',
                'last_name' => 'Достоєвський',
                'middle_name' => 'Михайлович',
                'biography' => 'Великий російський письменник, філософ. Автор романів "Злочин і кара", "Ідіот", "Брати Карамазови", "Підліток" та інших.',
                'birth_date' => '1821-11-11',
                'death_date' => '1881-02-09',
                'nationality' => 'Росія',
                'photo' => 'https://images.unsplash.com/photo-1566492031773-4f4e44671d66?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Класик світової літератури',
                'is_featured' => true,
            ],
            
            // Англійські автори
            [
                'first_name' => 'Вільям',
                'last_name' => 'Шекспір',
                'middle_name' => null,
                'biography' => 'Англійський поет, драматург, актор. Автор п\'єс "Ромео і Джульєтта", "Гамлет", "Макбет", "Король Лір", "Отелло" та інших.',
                'birth_date' => '1564-04-26',
                'death_date' => '1616-04-23',
                'nationality' => 'Англія',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Найвидатніший драматург світу',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Чарльз',
                'last_name' => 'Діккенс',
                'middle_name' => 'Джон',
                'biography' => 'Англійський письменник, романіст, журналіст. Автор романів "Олівер Твіст", "Девід Копперфілд", "Великі сподівання", "Повість про два міста" та інших.',
                'birth_date' => '1812-02-07',
                'death_date' => '1870-06-09',
                'nationality' => 'Англія',
                'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Класик англійської літератури',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Джейн',
                'last_name' => 'Остін',
                'middle_name' => null,
                'biography' => 'Англійська письменниця, автор романів "Гордість і упередження", "Розум і почуття", "Емма", "Менсфілд-парк" та інших.',
                'birth_date' => '1775-12-16',
                'death_date' => '1817-07-18',
                'nationality' => 'Англія',
                'photo' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Класик англійської літератури',
                'is_featured' => true,
            ],
            
            // Французькі автори
            [
                'first_name' => 'Віктор',
                'last_name' => 'Гюго',
                'middle_name' => null,
                'biography' => 'Французький письменник, поет, драматург. Автор романів "Собор Паризької Богоматері", "Знедолені", "Людина, що сміється" та інших.',
                'birth_date' => '1802-02-26',
                'death_date' => '1885-05-22',
                'nationality' => 'Франція',
                'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Класик французької літератури',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Олександр',
                'last_name' => 'Дюма',
                'middle_name' => null,
                'biography' => 'Французький письменник, автор романів "Три мушкетери", "Граф Монте-Крісто", "Королева Марго" та інших.',
                'birth_date' => '1802-07-24',
                'death_date' => '1870-12-05',
                'nationality' => 'Франція',
                'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Майстер пригодницького роману',
                'is_featured' => true,
            ],
            
            // Американські автори
            [
                'first_name' => 'Ернест',
                'last_name' => 'Хемінгуей',
                'middle_name' => 'Міллер',
                'biography' => 'Американський письменник, журналіст. Автор романів "Старий і море", "Прощавай, зброє!", "Сонце також сходить", "За кого дзвонить дзвін" та інших.',
                'birth_date' => '1899-07-21',
                'death_date' => '1961-07-02',
                'nationality' => 'США',
                'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Нобелівська премія з літератури (1954)',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Марк',
                'last_name' => 'Твен',
                'middle_name' => null,
                'biography' => 'Американський письменник, журналіст, лектор. Автор романів "Пригоди Тома Сойєра", "Пригоди Гекльберрі Фінна", "Янкі з Коннектикуту при дворі короля Артура" та інших.',
                'birth_date' => '1835-11-30',
                'death_date' => '1910-04-21',
                'nationality' => 'США',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Батько американської літератури',
                'is_featured' => true,
            ],
            
            // Німецькі автори
            [
                'first_name' => 'Франц',
                'last_name' => 'Кафка',
                'middle_name' => null,
                'biography' => 'Німецькомовний письменник, автор романів "Процес", "Замок", "Америка", оповідань "Перетворення", "У виправній колонії" та інших.',
                'birth_date' => '1883-07-03',
                'death_date' => '1924-06-03',
                'nationality' => 'Німеччина',
                'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Класик екзистенціалізму',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Йоганн',
                'last_name' => 'Гете',
                'middle_name' => 'Вольфганг',
                'biography' => 'Німецький поет, драматург, прозаїк. Автор трагедії "Фауст", роману "Страждання молодого Вертера", поеми "Герман і Доротея" та інших.',
                'birth_date' => '1749-08-28',
                'death_date' => '1832-03-22',
                'nationality' => 'Німеччина',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Великий німецький поет',
                'is_featured' => true,
            ],
            
            // Сучасні автори
            [
                'first_name' => 'Габріель',
                'last_name' => 'Гарсія Маркес',
                'middle_name' => 'Хосе',
                'biography' => 'Колумбійський письменник, журналіст. Автор романів "Сто років самотності", "Любов під час холери", "Хроніка оголошеної смерті" та інших.',
                'birth_date' => '1927-03-06',
                'death_date' => '2014-04-17',
                'nationality' => 'Колумбія',
                'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Нобелівська премія з літератури (1982)',
                'is_featured' => true,
            ],
            [
                'first_name' => 'Мілан',
                'last_name' => 'Кундера',
                'middle_name' => null,
                'biography' => 'Чесько-французький письменник. Автор романів "Невиносима легкість буття", "Жарт", "Вальс на прощання" та інших.',
                'birth_date' => '1929-04-01',
                'death_date' => '2023-07-11',
                'nationality' => 'Чехія',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop&crop=face',
                'awards' => 'Класик сучасної літератури',
                'is_featured' => true,
            ],
        ];

        foreach ($authors as $authorData) {
            Author::firstOrCreate(
                [
                    'first_name' => $authorData['first_name'],
                    'last_name' => $authorData['last_name']
                ],
                $authorData
            );
        }
    }
}
