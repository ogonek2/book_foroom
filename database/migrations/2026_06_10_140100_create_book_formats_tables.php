<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_formats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('book_format', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_format_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['book_id', 'book_format_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_format');
        Schema::dropIfExists('book_formats');
    }
};
