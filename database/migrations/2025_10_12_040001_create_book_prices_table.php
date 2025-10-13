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
        Schema::create('book_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('bookstore_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('UAH');
            $table->string('product_url');
            $table->boolean('is_available')->default(true);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
            
            // Уникальный индекс для книги и магазина
            $table->unique(['book_id', 'bookstore_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_prices');
    }
};
