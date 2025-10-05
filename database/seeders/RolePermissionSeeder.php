<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем разрешения
        $permissions = [
            // Админ панель
            ['name' => 'Доступ к админ панели', 'slug' => 'admin.access', 'group' => 'admin', 'description' => 'Полный доступ к админ панели'],
            
            // Управление пользователями
            ['name' => 'Просмотр пользователей', 'slug' => 'users.view', 'group' => 'users', 'description' => 'Просмотр списка пользователей'],
            ['name' => 'Создание пользователей', 'slug' => 'users.create', 'group' => 'users', 'description' => 'Создание новых пользователей'],
            ['name' => 'Редактирование пользователей', 'slug' => 'users.edit', 'group' => 'users', 'description' => 'Редактирование данных пользователей'],
            ['name' => 'Удаление пользователей', 'slug' => 'users.delete', 'group' => 'users', 'description' => 'Удаление пользователей'],
            ['name' => 'Управление ролями пользователей', 'slug' => 'users.roles', 'group' => 'users', 'description' => 'Назначение и изменение ролей пользователей'],
            
            // Управление книгами
            ['name' => 'Просмотр книг', 'slug' => 'books.view', 'group' => 'books', 'description' => 'Просмотр списка книг'],
            ['name' => 'Создание книг', 'slug' => 'books.create', 'group' => 'books', 'description' => 'Создание новых книг'],
            ['name' => 'Редактирование книг', 'slug' => 'books.edit', 'group' => 'books', 'description' => 'Редактирование данных книг'],
            ['name' => 'Удаление книг', 'slug' => 'books.delete', 'group' => 'books', 'description' => 'Удаление книг'],
            
            // Управление авторами
            ['name' => 'Просмотр авторов', 'slug' => 'authors.view', 'group' => 'authors', 'description' => 'Просмотр списка авторов'],
            ['name' => 'Создание авторов', 'slug' => 'authors.create', 'group' => 'authors', 'description' => 'Создание новых авторов'],
            ['name' => 'Редактирование авторов', 'slug' => 'authors.edit', 'group' => 'authors', 'description' => 'Редактирование данных авторов'],
            ['name' => 'Удаление авторов', 'slug' => 'authors.delete', 'group' => 'authors', 'description' => 'Удаление авторов'],
            
            // Управление категориями
            ['name' => 'Просмотр категорий', 'slug' => 'categories.view', 'group' => 'categories', 'description' => 'Просмотр списка категорий'],
            ['name' => 'Создание категорий', 'slug' => 'categories.create', 'group' => 'categories', 'description' => 'Создание новых категорий'],
            ['name' => 'Редактирование категорий', 'slug' => 'categories.edit', 'group' => 'categories', 'description' => 'Редактирование данных категорий'],
            ['name' => 'Удаление категорий', 'slug' => 'categories.delete', 'group' => 'categories', 'description' => 'Удаление категорий'],
            
            // Управление рецензиями
            ['name' => 'Просмотр рецензий', 'slug' => 'reviews.view', 'group' => 'reviews', 'description' => 'Просмотр всех рецензий'],
            ['name' => 'Модерация рецензий', 'slug' => 'reviews.moderate', 'group' => 'reviews', 'description' => 'Модерация и управление рецензиями'],
            ['name' => 'Удаление рецензий', 'slug' => 'reviews.delete', 'group' => 'reviews', 'description' => 'Удаление рецензий'],
            
            // Управление форумом
            ['name' => 'Просмотр форума', 'slug' => 'forum.view', 'group' => 'forum', 'description' => 'Просмотр всех тем и постов форума'],
            ['name' => 'Модерация форума', 'slug' => 'forum.moderate', 'group' => 'forum', 'description' => 'Модерация тем и постов форума'],
            ['name' => 'Удаление постов', 'slug' => 'forum.delete', 'group' => 'forum', 'description' => 'Удаление тем и постов форума'],
            
            // Управление ролями и разрешениями
            ['name' => 'Просмотр ролей', 'slug' => 'roles.view', 'group' => 'roles', 'description' => 'Просмотр списка ролей'],
            ['name' => 'Создание ролей', 'slug' => 'roles.create', 'group' => 'roles', 'description' => 'Создание новых ролей'],
            ['name' => 'Редактирование ролей', 'slug' => 'roles.edit', 'group' => 'roles', 'description' => 'Редактирование ролей и разрешений'],
            ['name' => 'Удаление ролей', 'slug' => 'roles.delete', 'group' => 'roles', 'description' => 'Удаление ролей'],
            
            // Системные настройки
            ['name' => 'Просмотр настроек', 'slug' => 'settings.view', 'group' => 'settings', 'description' => 'Просмотр системных настроек'],
            ['name' => 'Редактирование настроек', 'slug' => 'settings.edit', 'group' => 'settings', 'description' => 'Редактирование системных настроек'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Создаем роли
        $roles = [
            [
                'name' => 'Супер Администратор',
                'slug' => 'super_admin',
                'description' => 'Полный доступ ко всем функциям системы',
                'permissions' => Permission::all()->pluck('id')->toArray()
            ],
            [
                'name' => 'Администратор',
                'slug' => 'admin',
                'description' => 'Администратор системы',
                'permissions' => Permission::whereIn('group', ['admin', 'users', 'books', 'authors', 'categories', 'reviews', 'forum'])->pluck('id')->toArray()
            ],
            [
                'name' => 'Модератор',
                'slug' => 'moderator',
                'description' => 'Модератор контента',
                'permissions' => Permission::whereIn('slug', [
                    'books.view', 'authors.view', 'categories.view',
                    'reviews.view', 'reviews.moderate', 'reviews.delete',
                    'forum.view', 'forum.moderate', 'forum.delete'
                ])->pluck('id')->toArray()
            ],
            [
                'name' => 'Редактор',
                'slug' => 'editor',
                'description' => 'Редактор контента',
                'permissions' => Permission::whereIn('slug', [
                    'books.view', 'books.create', 'books.edit',
                    'authors.view', 'authors.create', 'authors.edit',
                    'categories.view', 'categories.create', 'categories.edit',
                    'reviews.view'
                ])->pluck('id')->toArray()
            ],
            [
                'name' => 'Пользователь',
                'slug' => 'user',
                'description' => 'Обычный пользователь',
                'permissions' => []
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
            
            $role->syncPermissions($permissions);
        }

        // Создаем супер администратора
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'username' => 'super_admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Назначаем роль супер администратора
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        if ($superAdminRole && !$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
