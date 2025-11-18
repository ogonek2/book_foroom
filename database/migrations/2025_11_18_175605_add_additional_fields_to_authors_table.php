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
        Schema::table('authors', function (Blueprint $table) {
            // Ukrainian names
            $table->string('first_name_ua')->nullable()->after('first_name');
            $table->string('middle_name_ua')->nullable()->after('middle_name');
            $table->string('last_name_ua')->nullable()->after('last_name');
            
            // English names
            $table->string('first_name_eng')->nullable()->after('first_name_ua');
            $table->string('middle_name_eng')->nullable()->after('middle_name_ua');
            $table->string('last_name_eng')->nullable()->after('last_name_ua');
            
            // Additional fields
            $table->string('pseudonym')->nullable()->after('last_name_eng');
            $table->json('synonyms')->nullable()->after('pseudonym');
            
            // Rename website to web_page for consistency with Excel
            // Keep website column, but add web_page as alias
            $table->string('web_page')->nullable()->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn([
                'first_name_ua',
                'middle_name_ua',
                'last_name_ua',
                'first_name_eng',
                'middle_name_eng',
                'last_name_eng',
                'pseudonym',
                'synonyms',
                'web_page',
            ]);
        });
    }
};
