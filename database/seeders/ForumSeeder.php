<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Художественная литература',
                'slug' => 'fiction',
                'description' => 'Обсуждение художественных произведений, романов, рассказов и поэзии',
                'color' => '#3B82F6',
                'icon' => 'book-open',
                'sort_order' => 1,
            ],
            [
                'name' => 'Научная литература',
                'slug' => 'science',
                'description' => 'Обсуждение научных книг, исследований и образовательной литературы',
                'color' => '#10B981',
                'icon' => 'microscope',
                'sort_order' => 2,
            ],
            [
                'name' => 'Бизнес и саморазвитие',
                'slug' => 'business',
                'description' => 'Книги по бизнесу, личностному росту и профессиональному развитию',
                'color' => '#F59E0B',
                'icon' => 'trending-up',
                'sort_order' => 3,
            ],
            [
                'name' => 'История и биографии',
                'slug' => 'history',
                'description' => 'Исторические книги, мемуары и биографии известных людей',
                'color' => '#8B5CF6',
                'icon' => 'clock',
                'sort_order' => 4,
            ],
            [
                'name' => 'Техническая литература',
                'slug' => 'technical',
                'description' => 'Программирование, IT, технические руководства и документация',
                'color' => '#EF4444',
                'icon' => 'code',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Get users (create some if none exist)
        $users = User::all();
        if ($users->count() < 5) {
            $users = User::factory(10)->create();
        }

        // Create topics and posts for each category (only if no topics exist)
        $categories = Category::all();
        
        if (Topic::count() > 0) {
            return; // Skip if topics already exist
        }
        
        foreach ($categories as $category) {
            // Create 3-5 topics per category
            $topicsCount = rand(3, 5);
            
            for ($i = 0; $i < $topicsCount; $i++) {
                $user = $users->random();
                
                $title = $this->getRandomTopicTitle($category->name);
                $topic = Topic::create([
                    'title' => $title,
                    'slug' => \Illuminate\Support\Str::slug($title) . '-' . uniqid(),
                    'content' => $this->getRandomTopicContent(),
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'is_pinned' => rand(0, 10) === 0, // 10% chance of being pinned
                    'views_count' => rand(0, 100),
                    'replies_count' => 0,
                ]);

                // Create 2-8 posts per topic
                $postsCount = rand(2, 8);
                
                for ($j = 0; $j < $postsCount; $j++) {
                    $postUser = $users->random();
                    
                    Post::create([
                        'content' => $this->getRandomPostContent(),
                        'topic_id' => $topic->id,
                        'user_id' => $postUser->id,
                        'is_solution' => $j === $postsCount - 1 && rand(0, 3) === 0, // 25% chance for last post to be solution
                    ]);
                }

                // Update topic replies count
                $topic->update(['replies_count' => $postsCount - 1]); // -1 because first post is the topic content
            }
        }
    }

    private function getRandomTopicTitle(string $categoryName): string
    {
        $titles = [
            'Художественная литература' => [
                'Рекомендации классической литературы',
                'Современные авторы, которых стоит прочитать',
                'Лучшие фантастические романы',
                'Поэзия: от классики до современности',
                'Детективы и триллеры',
            ],
            'Научная литература' => [
                'Популярная наука для начинающих',
                'Квантовая физика простыми словами',
                'Эволюция и происхождение видов',
                'Космология и астрофизика',
                'Психология и поведение человека',
            ],
            'Бизнес и саморазвитие' => [
                'Эффективные методы управления временем',
                'Финансовая грамотность и инвестиции',
                'Лидерство и управление командой',
                'Мотивация и достижение целей',
                'Предпринимательство с нуля',
            ],
            'История и биографии' => [
                'Великие исторические личности',
                'Вторая мировая война: новые факты',
                'История Древнего мира',
                'Биографии успешных людей',
                'История науки и технологий',
            ],
            'Техническая литература' => [
                'Изучение программирования: с чего начать?',
                'Веб-разработка: современные технологии',
                'Искусственный интеллект и машинное обучение',
                'Кибербезопасность для разработчиков',
                'DevOps и автоматизация',
            ],
        ];

        $categoryTitles = $titles[$categoryName] ?? ['Обсуждение книг'];
        return $categoryTitles[array_rand($categoryTitles)];
    }

    private function getRandomTopicContent(): string
    {
        $contents = [
            'Привет всем! Хотелось бы обсудить эту тему и узнать ваше мнение. Что вы думаете по этому поводу?',
            'Недавно прочитал интересную книгу и хочу поделиться впечатлениями. Кто еще читал?',
            'Ищу рекомендации по этой теме. Какие книги вы бы посоветовали?',
            'Обнаружил противоречивую информацию в разных источниках. Как вы считаете, где правда?',
            'Хочу углубиться в изучение этой области. С чего лучше начать?',
        ];

        return $contents[array_rand($contents)];
    }

    private function getRandomPostContent(): string
    {
        $contents = [
            'Отличная тема! Полностью согласен с автором.',
            'Интересная точка зрения. А что вы думаете о...',
            'Рекомендую прочитать книгу "..." - там очень хорошо раскрыта эта тема.',
            'У меня есть другой опыт по этому вопросу. Хочу поделиться...',
            'Спасибо за информацию! Очень полезно.',
            'А как вы относитесь к мнению, что...',
            'Отличный вопрос! Я думаю, что...',
            'Могу поделиться личным опытом по этому поводу.',
        ];

        return $contents[array_rand($contents)];
    }
}
