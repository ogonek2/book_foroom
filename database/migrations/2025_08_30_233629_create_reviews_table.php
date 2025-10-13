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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->integer('rating')->nullable(); // 1-5 звезд
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('reviews')->onDelete('cascade');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('dislikes_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->string('review_type')->default('review'); // review или feedback
            $table->string('opinion_type')->default('positive'); // positive, neutral, negative
            $table->string('book_type')->nullable(); // paper, electronic, audio
            $table->string('language')->default('uk'); // uk, ru, en
            $table->boolean('contains_spoiler')->default(false);
            $table->integer('replies_count')->default(0);
            $table->enum('status', ['active', 'blocked', 'pending'])->default('active');
            $table->timestamp('moderated_at')->nullable();
            $table->unsignedBigInteger('moderated_by')->nullable();
            $table->text('moderation_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['book_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['parent_id']);
            $table->index(['is_approved', 'created_at']);
            $table->index(['book_id', 'user_id', 'parent_id'], 'idx_book_user_parent');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
