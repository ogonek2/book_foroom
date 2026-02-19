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
        Schema::table('facts', function (Blueprint $table) {
            $table->enum('status', ['active', 'blocked', 'pending'])->default('active')->after('is_public');
            $table->timestamp('moderated_at')->nullable()->after('status');
            $table->unsignedBigInteger('moderated_by')->nullable()->after('moderated_at');
            $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
            $table->text('moderation_reason')->nullable()->after('moderated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facts', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn(['status', 'moderated_at', 'moderated_by', 'moderation_reason']);
        });
    }
};
