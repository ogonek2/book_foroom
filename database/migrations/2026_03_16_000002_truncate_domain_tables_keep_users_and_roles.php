<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tablesToTruncate = [
            'books',
            'book_category',
            'authors',
            'reviews',
            'ratings',
            'quotes',
            'facts',
            'likes',
            'notifications',
            'discussions',
            'discussion_replies',
            'discussion_mentions',
            'tags',
            'discussion_tag',
            'hashtags',
            'hashtag_review',
            'libraries',
            'library_book',
            'user_libraries',
            'user_saved_libraries',
            'user_liked_libraries',
            'favorite_quotes',
            'favorite_reviews',
            'reports',
            'book_reading_statuses',
            'bookstores',
            'book_prices',
            'publications',
            'user_awards',
            'saved_libraries',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Откат не восстанавливает данные
    }
};

