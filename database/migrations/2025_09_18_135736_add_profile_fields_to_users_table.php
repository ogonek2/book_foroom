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
            $table->string('username')->nullable()->after('name');
            $table->string('avatar')->nullable()->after('username');
            $table->text('bio')->nullable()->after('avatar');
            $table->decimal('rating', 3, 1)->default(0.0)->after('bio');
        });

        // Заполняем username для существующих пользователей
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $user->username = 'user_' . $user->id;
            $user->save();
        }

        // Теперь делаем username уникальным
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'avatar', 'bio', 'rating']);
        });
    }
};
