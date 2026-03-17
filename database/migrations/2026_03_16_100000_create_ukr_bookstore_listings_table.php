<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukr_bookstore_listings', function (Blueprint $table) {
            $table->id();
            $table->string('store_key', 64)->index();
            $table->string('external_id', 128)->nullable()->index();
            $table->string('title');
            $table->string('url', 1024);
            $table->string('price', 64)->nullable();
            $table->string('image_url', 1024)->nullable();
            $table->string('author', 512)->nullable();
            $table->string('search_query')->nullable()->index();
            $table->timestamps();

            $table->unique(['store_key', 'external_id'], 'ukr_listings_store_external_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukr_bookstore_listings');
    }
};
