<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Обновляем счетчики ответов для всех рецензий
        $reviews = DB::table('reviews')->whereNull('parent_id')->get();
        
        foreach ($reviews as $review) {
            $repliesCount = DB::table('reviews')
                ->where('parent_id', $review->id)
                ->count();
                
            DB::table('reviews')
                ->where('id', $review->id)
                ->update(['replies_count' => $repliesCount]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            //
        });
    }
};
