<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class CheckDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check database for roles, permissions and users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Проверка базы данных...');
        
        // Проверяем роли
        $this->line("\n📋 Роли:");
        $roles = Role::all();
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $this->line("- ID: {$role->id}, Название: {$role->name}, Slug: {$role->slug}, Активна: " . ($role->is_active ? 'Да' : 'Нет'));
            }
        } else {
            $this->error('❌ Роли не найдены!');
        }

        // Проверяем разрешения
        $this->line("\n🔑 Разрешения:");
        $permissions = Permission::all();
        if ($permissions->count() > 0) {
            $this->line("Всего разрешений: {$permissions->count()}");
            foreach ($permissions->groupBy('group') as $group => $groupPermissions) {
                $this->line("  Группа '{$group}': {$groupPermissions->count()} разрешений");
            }
        } else {
            $this->error('❌ Разрешения не найдены!');
        }

        // Проверяем пользователей
        $this->line("\n👥 Пользователи:");
        $users = User::with('roles')->get();
        if ($users->count() > 0) {
            foreach ($users as $user) {
                $rolesList = $user->roles->count() > 0 
                    ? $user->roles->pluck('name')->join(', ') 
                    : 'Нет ролей';
                $this->line("- ID: {$user->id}, Имя: {$user->name}, Email: {$user->email}, Роли: {$rolesList}");
            }
        } else {
            $this->error('❌ Пользователи не найдены!');
        }

        // Проверяем связи user_roles
        $this->line("\n🔗 Связи пользователь-роль:");
        $userRoles = \DB::table('user_roles')->get();
        if ($userRoles->count() > 0) {
            foreach ($userRoles as $userRole) {
                $user = User::find($userRole->user_id);
                $role = Role::find($userRole->role_id);
                $this->line("- Пользователь: {$user->name} → Роль: {$role->name}");
            }
        } else {
            $this->error('❌ Связи пользователь-роль не найдены!');
        }

        // Проверяем связи role_permissions
        $this->line("\n🔗 Связи роль-разрешение:");
        $rolePermissions = \DB::table('role_permissions')->get();
        if ($rolePermissions->count() > 0) {
            $rolePermCounts = $rolePermissions->groupBy('role_id')->map->count();
            foreach ($rolePermCounts as $roleId => $count) {
                $role = Role::find($roleId);
                $this->line("- Роль: {$role->name} → {$count} разрешений");
            }
        } else {
            $this->error('❌ Связи роль-разрешение не найдены!');
        }

        return 0;
    }
}
