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
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->enum('status', ['pending', 'active', 'blocked'])->default('active');
            $table->timestamp('moderated_at')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('moderation_reason')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['is_pinned', 'last_activity_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};
