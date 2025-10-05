<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class CreateMissingRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-missing-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing roles (admin, moderator, editor, user)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Создание недостающих ролей...');

        // Создаем роль Admin
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Администратор',
                'description' => 'Администратор системы',
                'is_active' => true
            ]
        );

        // Назначаем административные разрешения
        $adminPermissions = Permission::whereIn('group', ['admin', 'users', 'books', 'authors', 'categories', 'reviews', 'forum'])->pluck('id')->toArray();
        $adminRole->syncPermissions($adminPermissions);
        $this->info("✅ Создана роль 'Администратор' с {$adminPermissions} разрешениями");

        // Создаем роль Moderator
        $moderatorRole = Role::firstOrCreate(
            ['slug' => 'moderator'],
            [
                'name' => 'Модератор',
                'description' => 'Модератор контента',
                'is_active' => true
            ]
        );

        // Назначаем разрешения модератора
        $moderatorPermissions = Permission::whereIn('slug', [
            'books.view', 'authors.view', 'categories.view',
            'reviews.view', 'reviews.moderate', 'reviews.delete',
            'forum.view', 'forum.moderate', 'forum.delete'
        ])->pluck('id')->toArray();
        $moderatorRole->syncPermissions($moderatorPermissions);
        $this->info("✅ Создана роль 'Модератор' с {$moderatorPermissions} разрешениями");

        // Создаем роль Editor
        $editorRole = Role::firstOrCreate(
            ['slug' => 'editor'],
            [
                'name' => 'Редактор',
                'description' => 'Редактор контента',
                'is_active' => true
            ]
        );

        // Назначаем разрешения редактора
        $editorPermissions = Permission::whereIn('slug', [
            'books.view', 'books.create', 'books.edit',
            'authors.view', 'authors.create', 'authors.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'reviews.view'
        ])->pluck('id')->toArray();
        $editorRole->syncPermissions($editorPermissions);
        $this->info("✅ Создана роль 'Редактор' с {$editorPermissions} разрешениями");

        // Создаем роль User
        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'Пользователь',
                'description' => 'Обычный пользователь',
                'is_active' => true
            ]
        );

        // У роли User нет специальных разрешений
        $userRole->syncPermissions([]);
        $this->info("✅ Создана роль 'Пользователь' без специальных разрешений");

        $this->info("\n📊 Итоговая информация:");
        $this->line("- Всего ролей: " . Role::count());
        $this->line("- Роль 'super_admin': " . (Role::where('slug', 'super_admin')->exists() ? '✅' : '❌'));
        $this->line("- Роль 'admin': " . (Role::where('slug', 'admin')->exists() ? '✅' : '❌'));
        $this->line("- Роль 'moderator': " . (Role::where('slug', 'moderator')->exists() ? '✅' : '❌'));
        $this->line("- Роль 'editor': " . (Role::where('slug', 'editor')->exists() ? '✅' : '❌'));
        $this->line("- Роль 'user': " . (Role::where('slug', 'user')->exists() ? '✅' : '❌'));

        return 0;
    }
}
