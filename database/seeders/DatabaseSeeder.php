<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user if not exists
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'test_user',
                'password' => bcrypt('password'),
            ]
        );

        // Seed forum data
        $this->call([
            ForumSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
            UserSeeder::class,
            QuoteSeeder::class,
            PublicationSeeder::class,
            ReviewSeeder::class,
            UserLibrarySeeder::class,
            BookReadingStatusSeeder::class,
            BookstoreSeeder::class,
        ]);
    }
}
