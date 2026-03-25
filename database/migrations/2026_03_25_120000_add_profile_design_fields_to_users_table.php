<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Header / profile design (experimental)
            $table->string('profile_header_title')->nullable()->after('bio');
            $table->string('profile_header_subtitle')->nullable()->after('profile_header_title');
            $table->string('profile_header_image')->nullable()->after('profile_header_subtitle');

            // Theme tokens (colors / frames)
            $table->string('profile_accent_color', 32)->nullable()->after('profile_header_image');
            $table->string('profile_secondary_color', 32)->nullable()->after('profile_accent_color');
            $table->string('profile_frame_style', 32)->nullable()->after('profile_secondary_color');
            $table->string('profile_card_style', 32)->nullable()->after('profile_frame_style');

            // Future-proof bucket for custom settings
            $table->json('profile_design')->nullable()->after('profile_card_style');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_header_title',
                'profile_header_subtitle',
                'profile_header_image',
                'profile_accent_color',
                'profile_secondary_color',
                'profile_frame_style',
                'profile_card_style',
                'profile_design',
            ]);
        });
    }
};

