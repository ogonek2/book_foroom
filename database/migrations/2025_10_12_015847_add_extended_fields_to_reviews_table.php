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
            $table->string('review_type')->default('review')->after('content'); // review или feedback
            $table->string('opinion_type')->default('positive')->after('review_type'); // positive, neutral, negative
            $table->string('book_type')->nullable()->after('opinion_type'); // paper, electronic, audio
            $table->string('language')->default('uk')->after('book_type'); // uk, ru, en
            $table->boolean('contains_spoiler')->default(false)->after('language');
            $table->integer('replies_count')->default(0)->after('contains_spoiler');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'review_type',
                'opinion_type',
                'book_type',
                'language',
                'contains_spoiler',
                'replies_count'
            ]);
        });
    }
};
