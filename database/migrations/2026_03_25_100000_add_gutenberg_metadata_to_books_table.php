<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'gutenberg_download_count')) {
                $table->unsignedInteger('gutenberg_download_count')->nullable()->after('gutenberg_book_id');
                $table->index('gutenberg_download_count');
            }

            if (! Schema::hasColumn('books', 'gutenberg_issued_at')) {
                $table->timestamp('gutenberg_issued_at')->nullable()->after('gutenberg_download_count');
                $table->index('gutenberg_issued_at');
            }

            if (! Schema::hasColumn('books', 'gutenberg_reading_ease_score')) {
                $table->decimal('gutenberg_reading_ease_score', 6, 2)->nullable()->after('gutenberg_issued_at');
            }

            if (! Schema::hasColumn('books', 'gutenberg_formats')) {
                $table->json('gutenberg_formats')->nullable()->after('gutenberg_reading_ease_score');
            }

            if (! Schema::hasColumn('books', 'gutenberg_media_type')) {
                $table->string('gutenberg_media_type')->nullable()->after('gutenberg_formats');
            }

            if (! Schema::hasColumn('books', 'gutenberg_summary_en')) {
                $table->text('gutenberg_summary_en')->nullable()->after('gutenberg_media_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            foreach ([
                'gutenberg_download_count',
                'gutenberg_issued_at',
                'gutenberg_reading_ease_score',
                'gutenberg_formats',
                'gutenberg_media_type',
                'gutenberg_summary_en',
            ] as $col) {
                if (Schema::hasColumn('books', $col)) {
                    if (in_array($col, ['gutenberg_download_count', 'gutenberg_issued_at'], true)) {
                        $table->dropIndex([$col]);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};

