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
        Schema::table('reviews', function (Blueprint $table) {
            // Убираем ограничение foreign key для user_id
            $table->dropForeign(['user_id']);
            
            // Изменяем поле user_id, чтобы оно могло быть nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Добавляем обратно foreign key, но с возможностью NULL
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Убираем foreign key
            $table->dropForeign(['user_id']);
            
            // Возвращаем поле user_id как обязательное
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            
            // Добавляем обратно обязательный foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
