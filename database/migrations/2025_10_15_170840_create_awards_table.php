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
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название награды
            $table->text('description')->nullable(); // Описание награды
            $table->string('image')->nullable(); // Изображение награды (путь к файлу на CDN)
            $table->string('color')->default('#f59e0b'); // Цвет награды
            $table->integer('points')->default(0); // Очки за получение награды
            $table->boolean('is_active')->default(true); // Активна ли награда
            $table->integer('sort_order')->default(0); // Порядок сортировки
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
