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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('author');
            $table->foreignId('author_id')->nullable()->constrained()->onDelete('set null');
            $table->string('isbn')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('publisher')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('language', 5)->default('ru');
            $table->integer('pages')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('interesting_facts')->nullable();
            $table->timestamps();
            
            $table->index(['rating', 'reviews_count']);
            $table->index(['is_featured', 'created_at']);
            $table->index('author_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
