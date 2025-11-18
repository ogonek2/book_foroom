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
        Schema::table('books', function (Blueprint $table) {
            $table->string('book_name_ua')->nullable()->after('title');
            $table->text('annotation')->nullable()->after('book_name_ua');
            $table->string('annotation_source')->nullable()->after('annotation');
            $table->integer('first_publish_year')->nullable()->after('publication_year');
            $table->string('original_language', 10)->nullable()->after('language');
            $table->json('synonyms')->nullable()->after('original_language');
            $table->string('series')->nullable()->after('synonyms');
        });

        // Переносим существующие данные в новые поля
        DB::statement('UPDATE books SET book_name_ua = title WHERE book_name_ua IS NULL');
        DB::statement('UPDATE books SET annotation = description WHERE annotation IS NULL AND description IS NOT NULL');
        DB::statement('UPDATE books SET annotation_source = NULL WHERE annotation_source IS NULL');
        DB::statement('UPDATE books SET first_publish_year = publication_year WHERE first_publish_year IS NULL AND publication_year IS NOT NULL');
        DB::statement('UPDATE books SET original_language = language WHERE original_language IS NULL');

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
        });

        DB::statement('UPDATE books SET description = annotation WHERE annotation IS NOT NULL');

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'book_name_ua',
                'annotation',
                'annotation_source',
                'first_publish_year',
                'original_language',
                'synonyms',
                'series',
            ]);
        });
    }
};

