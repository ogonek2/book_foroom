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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('city')->nullable();
            $table->json('favorite_genres')->nullable();
            $table->boolean('newsletter_subscribed')->default(false);
            $table->string('email_verification_token')->nullable();
            
            // Notification settings
            $table->boolean('email_notifications')->default(true);
            $table->boolean('new_books_notifications')->default(true);
            $table->boolean('comments_notifications')->default(true);
            
            // Privacy settings
            $table->boolean('public_profile')->default(true);
            $table->boolean('show_reading_stats')->default(true);
            $table->boolean('show_ratings')->default(true);
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
