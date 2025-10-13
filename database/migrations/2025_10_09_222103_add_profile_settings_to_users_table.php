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
        Schema::table('users', function (Blueprint $table) {
            // Notification settings
            $table->boolean('email_notifications')->default(true);
            $table->boolean('new_books_notifications')->default(true);
            $table->boolean('comments_notifications')->default(true);
            
            // Privacy settings
            $table->boolean('public_profile')->default(true);
            $table->boolean('show_reading_stats')->default(true);
            $table->boolean('show_ratings')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_notifications',
                'new_books_notifications', 
                'comments_notifications',
                'public_profile',
                'show_reading_stats',
                'show_ratings'
            ]);
        });
    }
};
