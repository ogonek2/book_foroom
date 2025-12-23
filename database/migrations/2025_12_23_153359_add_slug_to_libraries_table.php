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
        Schema::table('libraries', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->index('slug');
        });
        
        // Генерируем slug для существующих библиотек
        $libraries = \App\Models\Library::whereNull('slug')->get();
        foreach ($libraries as $library) {
            $baseSlug = \Illuminate\Support\Str::slug($library->name);
            $slug = $baseSlug;
            $counter = 1;
            
            while (\App\Models\Library::where('slug', $slug)->where('id', '!=', $library->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $library->slug = $slug;
            $library->save();
        }
        
        // Делаем slug обязательным после заполнения
        Schema::table('libraries', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('libraries', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};
