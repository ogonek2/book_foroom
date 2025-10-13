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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->integer('page_number')->nullable();
            $table->boolean('is_public')->default(true);
            $table->enum('status', ['active', 'blocked', 'pending'])->default('active');
            $table->timestamp('moderated_at')->nullable();
            $table->unsignedBigInteger('moderated_by')->nullable();
            $table->text('moderation_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
