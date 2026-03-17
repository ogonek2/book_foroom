<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('books', 'ukr_store_url')) {
            Schema::table('books', function (Blueprint $table) {
                $table->string('ukr_store_url', 1024)->nullable()->after('open_library_work_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('books', 'ukr_store_url')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('ukr_store_url');
            });
        }
    }
};
