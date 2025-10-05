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
        // Удаляем дублирующиеся записи, оставляя только самую новую
        DB::statement("
            DELETE r1 FROM reviews r1
            INNER JOIN reviews r2 
            WHERE r1.id < r2.id 
            AND r1.book_id = r2.book_id 
            AND r1.user_id = r2.user_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
