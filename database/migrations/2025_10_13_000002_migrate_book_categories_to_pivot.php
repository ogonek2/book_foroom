<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Переносим существующие связи книг с категориями в pivot таблицу
        DB::statement('
            INSERT INTO book_category (book_id, category_id, created_at, updated_at)
            SELECT id, category_id, created_at, updated_at
            FROM books
            WHERE category_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Откат не требуется, так как мы не удаляем старые данные
    }
};

