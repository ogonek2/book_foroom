<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: Wikimedia/Wikipedia URLs can exceed 255 chars.
        if (DB::getDriverName() === 'mysql') {
            if (Schema::hasColumn('authors', 'photo')) {
                DB::statement('ALTER TABLE `authors` MODIFY `photo` TEXT NULL');
            }
            if (Schema::hasColumn('authors', 'website')) {
                DB::statement('ALTER TABLE `authors` MODIFY `website` TEXT NULL');
            }
            if (Schema::hasColumn('authors', 'web_page')) {
                DB::statement('ALTER TABLE `authors` MODIFY `web_page` TEXT NULL');
            }
        }
    }

    public function down(): void
    {
        // Best-effort revert for MySQL. (Might truncate data if URLs were long.)
        if (DB::getDriverName() === 'mysql') {
            if (Schema::hasColumn('authors', 'photo')) {
                DB::statement('ALTER TABLE `authors` MODIFY `photo` VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('authors', 'website')) {
                DB::statement('ALTER TABLE `authors` MODIFY `website` VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('authors', 'web_page')) {
                DB::statement('ALTER TABLE `authors` MODIFY `web_page` VARCHAR(255) NULL');
            }
        }
    }
};

