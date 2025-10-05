<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix database issues with roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Исправление проблем с базой данных...');

        // Проверяем, есть ли роли
        if (Role::count() === 0) {
            $this->warn('❌ Роли не найдены. Запускаем сидер...');
            $this->call('db:seed', ['--class' => 'RolePermissionSeeder']);
        }

        // Проверяем, есть ли супер администратор
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        if (!$superAdminRole) {
            $this->error('❌ Роль super_admin не найдена!');
            return 1;
        }

        // Проверяем, есть ли пользователи с ролью супер администратора
        $superAdmins = User::whereHas('roles', function($query) {
            $query->where('slug', 'super_admin');
        })->get();

        if ($superAdmins->count() === 0) {
            $this->warn('❌ Нет пользователей с ролью Super Admin. Создаем...');
            
            // Создаем супер администратора
            $superAdmin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Super Admin',
                    'username' => 'super_admin',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            // Назначаем роль
            $superAdmin->assignRole($superAdminRole);
            
            $this->info("✅ Создан супер администратор: {$superAdmin->email}");
        }

        // Проверяем всех пользователей и назначаем роль user по умолчанию
        $userRole = Role::where('slug', 'user')->first();
        if ($userRole) {
            $usersWithoutRoles = User::whereDoesntHave('roles')->get();
            foreach ($usersWithoutRoles as $user) {
                $user->assignRole($userRole);
                $this->info("✅ Назначена роль 'user' пользователю: {$user->name}");
            }
        }

        // Проверяем связи роль-разрешение
        $rolesWithoutPermissions = Role::whereDoesntHave('permissions')->get();
        foreach ($rolesWithoutPermissions as $role) {
            if ($role->slug === 'super_admin') {
                // Назначаем все разрешения супер администратору
                $role->syncPermissions(Permission::all()->pluck('id')->toArray());
                $this->info("✅ Назначены все разрешения роли: {$role->name}");
            } elseif ($role->slug === 'admin') {
                // Назначаем административные разрешения
                $adminPermissions = Permission::whereIn('group', ['admin', 'users', 'books', 'authors', 'categories', 'reviews', 'forum'])->pluck('id')->toArray();
                $role->syncPermissions($adminPermissions);
                $this->info("✅ Назначены административные разрешения роли: {$role->name}");
            }
        }

        $this->info('✅ Исправление завершено!');
        
        // Показываем итоговую информацию
        $this->line("\n📊 Итоговая информация:");
        $this->line("- Ролей: " . Role::count());
        $this->line("- Разрешений: " . Permission::count());
        $this->line("- Пользователей: " . User::count());
        $this->line("- Супер администраторов: " . User::whereHas('roles', function($query) {
            $query->where('slug', 'super_admin');
        })->count());

        return 0;
    }
}
