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
        Schema::create('user_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('award_id')->constrained()->onDelete('cascade');
            $table->timestamp('awarded_at')->useCurrent(); // Когда была получена награда
            $table->text('note')->nullable(); // Дополнительная заметка
            $table->timestamps();
            
            // Уникальная комбинация пользователя и награды
            $table->unique(['user_id', 'award_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_awards');
    }
};
