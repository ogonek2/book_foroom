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
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('city')->nullable()->after('birth_date');
            $table->json('favorite_genres')->nullable()->after('city');
            $table->boolean('newsletter_subscribed')->default(false)->after('favorite_genres');
            $table->string('email_verification_token')->nullable()->after('newsletter_subscribed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name', 
                'phone',
                'birth_date',
                'city',
                'favorite_genres',
                'newsletter_subscribed',
                'email_verification_token'
            ]);
        });
    }
};
