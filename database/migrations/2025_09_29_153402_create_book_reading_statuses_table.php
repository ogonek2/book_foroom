<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_reading_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['read', 'reading', 'want_to_read'])->default('want_to_read');
            $table->integer('rating')->nullable(); // Оценка книги (1-10)
            $table->text('review')->nullable(); // Отзыв о книге
            $table->date('started_at')->nullable(); // Дата начала чтения
            $table->date('finished_at')->nullable(); // Дата завершения чтения
            $table->timestamps();
            
            // Уникальный индекс для комбинации пользователь-книга
            $table->unique(['user_id', 'book_id']);
            
            // Индексы для быстрого поиска
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reading_statuses');
    }
};