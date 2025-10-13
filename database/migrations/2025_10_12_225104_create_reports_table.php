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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            
            // Пользователь, который подает жалобу
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            
            // Пользователь, на которого жалуются
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Полиморфные отношения для различных типов контента
            $table->morphs('reportable'); // reportable_type и reportable_id
            
            // Тип жалобы
            $table->enum('type', [
                'spam',           // Спам
                'harassment',     // Харассмент/травля
                'inappropriate',  // Неподходящий контент
                'copyright',      // Нарушение авторских прав
                'fake',          // Фейковая информация
                'hate_speech',   // Разжигание ненависти
                'violence',      // Насилие
                'adult_content', // Взрослый контент
                'other'          // Другое
            ]);
            
            // Причина жалобы (текстовое описание)
            $table->text('reason')->nullable();
            
            // Статус жалобы
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            
            // Модератор, который обработал жалобу
            $table->foreignId('moderator_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Комментарий модератора
            $table->text('moderator_comment')->nullable();
            
            // Дата обработки
            $table->timestamp('processed_at')->nullable();
            
            // URL контента для быстрого доступа
            $table->string('content_url')->nullable();
            
            // Дополнительные данные (JSON)
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index(['reporter_id', 'status']);
            $table->index(['reported_user_id', 'status']);
            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
