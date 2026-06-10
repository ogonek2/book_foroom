<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE `books` MODIFY `series_number` VARCHAR(50) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE books ALTER COLUMN series_number TYPE VARCHAR(50) USING series_number::text');
        } elseif ($driver === 'sqlite') {
            // SQLite: тип колонки не жёсткий, строковые значения уже допустимы.
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE `books` MODIFY `series_number` INT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE books ALTER COLUMN series_number TYPE INTEGER USING NULLIF(series_number, \'\')::integer');
        }
    }
};
