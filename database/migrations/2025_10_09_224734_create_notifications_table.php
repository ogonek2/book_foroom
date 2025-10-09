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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Получатель уведомления
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade'); // Отправитель (кто создал действие)
            $table->string('type'); // Тип: 'review_reply', 'discussion_reply', 'like', 'follow', etc.
            $table->text('message'); // Текст уведомления
            $table->morphs('notifiable'); // Polymorphic relation к связанной модели (Review, Discussion, etc.)
            $table->json('data')->nullable(); // Дополнительные данные
            $table->boolean('is_read')->default(false); // Прочитано ли
            $table->timestamp('read_at')->nullable(); // Время прочтения
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
