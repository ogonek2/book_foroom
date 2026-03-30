<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('goal')->nullable();
            $table->date('target_date')->nullable();
            $table->timestamps();
        });

        Schema::create('reading_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->boolean('is_done')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['reading_plan_id', 'book_id']);
            $table->index(['reading_plan_id', 'is_done']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_plan_items');
        Schema::dropIfExists('reading_plans');
    }
};
