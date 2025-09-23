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
        // Обновляем существующие записи на статус 'active'
        DB::table('posts')->whereNull('status')->update(['status' => 'active']);
        DB::table('reviews')->whereNull('status')->update(['status' => 'active']);
        DB::table('quotes')->whereNull('status')->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем статус на 'pending' для существующих записей
        DB::table('posts')->where('status', 'active')->update(['status' => 'pending']);
        DB::table('reviews')->where('status', 'active')->update(['status' => 'pending']);
        DB::table('quotes')->where('status', 'active')->update(['status' => 'pending']);
    }
};
