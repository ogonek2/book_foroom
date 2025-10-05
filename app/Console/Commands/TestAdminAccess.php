<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:test-access {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin access for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        $user = User::with('roles', 'permissions')->find($userId);
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден.");
            return 1;
        }

        $this->info("🔍 Тестирование доступа для пользователя: {$user->name} ({$user->email})");
        
        // Проверяем роли
        $this->line("\n📋 Роли:");
        foreach ($user->roles as $role) {
            $this->line("- {$role->name} ({$role->slug})");
        }

        // Проверяем разрешения
        $this->line("\n🔑 Разрешения:");
        foreach ($user->permissions as $permission) {
            $this->line("- {$permission->name} ({$permission->slug})");
        }

        // Проверяем методы доступа
        $this->line("\n✅ Проверки доступа:");
        $this->line("- isAdmin(): " . ($user->isAdmin() ? '✅ Да' : '❌ Нет'));
        $this->line("- isModerator(): " . ($user->isModerator() ? '✅ Да' : '❌ Нет'));
        $this->line("- hasRole('super_admin'): " . ($user->hasRole('super_admin') ? '✅ Да' : '❌ Нет'));
        $this->line("- hasRole('admin'): " . ($user->hasRole('admin') ? '✅ Да' : '❌ Нет'));
        $this->line("- hasPermission('admin.access'): " . ($user->hasPermission('admin.access') ? '✅ Да' : '❌ Нет'));

        // Итоговый результат
        if ($user->isAdmin()) {
            $this->info("\n🎉 Пользователь МОЖЕТ получить доступ к админ панели!");
        } else {
            $this->error("\n❌ Пользователь НЕ МОЖЕТ получить доступ к админ панели!");
        }

        return 0;
    }
}
