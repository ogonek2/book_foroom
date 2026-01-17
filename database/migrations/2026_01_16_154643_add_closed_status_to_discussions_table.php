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
        // Додаємо 'closed' до enum статусів
        DB::statement("ALTER TABLE discussions MODIFY COLUMN status ENUM('pending', 'active', 'blocked', 'closed') DEFAULT 'active'");
        
        // Оновлюємо існуючі закриті обговорення
        DB::table('discussions')
            ->where('is_closed', true)
            ->where('status', '!=', 'closed')
            ->update(['status' => 'closed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Перед видаленням 'closed' з enum, повертаємо закриті обговорення до 'active'
        DB::table('discussions')
            ->where('status', 'closed')
            ->update(['status' => 'active']);
        
        // Видаляємо 'closed' з enum
        DB::statement("ALTER TABLE discussions MODIFY COLUMN status ENUM('pending', 'active', 'blocked') DEFAULT 'active'");
    }
};
