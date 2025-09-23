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
        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('status', ['active', 'blocked', 'pending'])->default('active')->after('is_approved');
            $table->timestamp('moderated_at')->nullable()->after('status');
            $table->unsignedBigInteger('moderated_by')->nullable()->after('moderated_at');
            $table->text('moderation_reason')->nullable()->after('moderated_by');
            
            $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn(['status', 'moderated_at', 'moderated_by', 'moderation_reason']);
        });
    }
};
