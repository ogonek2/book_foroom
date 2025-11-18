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
        // Изменяем ENUM колонку, добавляя новое значение 'abandoned'
        DB::statement("ALTER TABLE `book_reading_statuses` MODIFY COLUMN `status` ENUM('read', 'reading', 'want_to_read', 'abandoned') DEFAULT 'want_to_read'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем значение 'abandoned' из ENUM (если есть записи с этим статусом, их нужно будет обработать отдельно)
        DB::statement("ALTER TABLE `book_reading_statuses` MODIFY COLUMN `status` ENUM('read', 'reading', 'want_to_read') DEFAULT 'want_to_read'");
    }
};
