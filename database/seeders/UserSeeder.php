<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Александр Петров',
                'username' => 'alex_petrov',
                'email' => 'alex@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Александр+Петров&background=random',
                'bio' => 'Любитель классической литературы и философии. Читаю книги с детства.',
                'rating' => 4.8,
            ],
            [
                'name' => 'Мария Сидорова',
                'username' => 'maria_sidorova',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Мария+Сидорова&background=random',
                'bio' => 'Книжный блогер и критик. Специализируюсь на современной прозе.',
                'rating' => 4.9,
            ],
            [
                'name' => 'Дмитрий Козлов',
                'username' => 'dmitry_kozlov',
                'email' => 'dmitry@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Дмитрий+Козлов&background=random',
                'bio' => 'Фантастика и фэнтези - моя страсть. Пишу собственные рассказы.',
                'rating' => 4.5,
            ],
            [
                'name' => 'Елена Волкова',
                'username' => 'elena_volkova',
                'email' => 'elena@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Елена+Волкова&background=random',
                'bio' => 'Историк по образованию, читаю биографии и мемуары.',
                'rating' => 4.7,
            ],
            [
                'name' => 'Игорь Морозов',
                'username' => 'igor_morozov',
                'email' => 'igor@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Игорь+Морозов&background=random',
                'bio' => 'Программист, увлекаюсь научной фантастикой и технической литературой.',
                'rating' => 4.6,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
