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
        // Удаляем старый уникальный индекс, который блокирует ответы
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('unique_user_book_review');
        });

        // Создаем частичный уникальный индекс только для основных рецензий (parent_id IS NULL)
        // MySQL не поддерживает условные индексы напрямую, поэтому используем raw SQL
        // Создаем обычный составной индекс и будем контролировать уникальность через Laravel
        
        // Для MySQL 8.0+ можем использовать функциональный индекс с IFNULL
        // Но для совместимости будем контролировать на уровне приложения
        
        // Добавляем обычный индекс для оптимизации запросов
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['book_id', 'user_id', 'parent_id'], 'idx_book_user_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем новый индекс
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_book_user_parent');
        });

        // Восстанавливаем старый уникальный индекс
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['book_id', 'user_id'], 'unique_user_book_review');
        });
    }
};
