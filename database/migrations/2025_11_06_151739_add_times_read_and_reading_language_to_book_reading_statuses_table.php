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
        Schema::table('book_reading_statuses', function (Blueprint $table) {
            $table->integer('times_read')->default(1)->after('status');
            $table->string('reading_language', 10)->nullable()->after('times_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_reading_statuses', function (Blueprint $table) {
            $table->dropColumn(['times_read', 'reading_language']);
        });
    }
};
