<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_reading_status_id')->constrained('book_reading_statuses')->onDelete('cascade');
            $table->date('read_at');
            $table->string('language', 10)->nullable();
            $table->timestamps();

            $table->index(['book_reading_status_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_sessions');
    }
};
