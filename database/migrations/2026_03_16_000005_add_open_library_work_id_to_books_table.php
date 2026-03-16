<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('open_library_work_id', 32)->nullable()->after('google_volume_id');
            $table->unique('open_library_work_id');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['open_library_work_id']);
            $table->dropColumn('open_library_work_id');
        });
    }
};
