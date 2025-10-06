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
        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('discussion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('discussion_replies')->onDelete('cascade');
            $table->integer('replies_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->enum('status', ['pending', 'active', 'blocked'])->default('active');
            $table->timestamp('moderated_at')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('moderation_reason')->nullable();
            $table->timestamps();
            
            $table->index(['discussion_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
    }
};
