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
        // Создаем обычный уникальный индекс для основных рецензий
        // В MySQL нет поддержки условных индексов, поэтому создаем обычный
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['book_id', 'user_id'], 'unique_user_book_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем уникальный индекс
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('unique_user_book_review');
        });
    }
};
