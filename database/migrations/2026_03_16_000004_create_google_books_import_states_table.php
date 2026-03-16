<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_books_import_states', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->unsignedInteger('last_start_index')->default(0);
            $table->unsignedInteger('last_total_items')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->unique('query');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_books_import_states');
    }
};

