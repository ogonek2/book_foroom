<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'gutenberg_book_id')) {
                $table->unsignedBigInteger('gutenberg_book_id')->nullable()->after('open_library_work_id');
                $table->unique('gutenberg_book_id');
            }
        });

        Schema::table('authors', function (Blueprint $table) {
            if (! Schema::hasColumn('authors', 'gutenberg_author_id')) {
                $table->unsignedBigInteger('gutenberg_author_id')->nullable()->after('id');
                $table->unique('gutenberg_author_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'gutenberg_book_id')) {
                $table->dropUnique(['gutenberg_book_id']);
                $table->dropColumn('gutenberg_book_id');
            }
        });

        Schema::table('authors', function (Blueprint $table) {
            if (Schema::hasColumn('authors', 'gutenberg_author_id')) {
                $table->dropUnique(['gutenberg_author_id']);
                $table->dropColumn('gutenberg_author_id');
            }
        });
    }
};

