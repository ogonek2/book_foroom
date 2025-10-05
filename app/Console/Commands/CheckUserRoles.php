<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check-roles {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        // Найти пользователя
        $user = User::with('roles', 'permissions')->find($userId);
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден.");
            return 1;
        }

        $this->info("Пользователь: {$user->name} ({$user->email})");
        $this->info("ID: {$user->id}");
        
        $this->line("\nРоли:");
        if ($user->roles->count() > 0) {
            foreach ($user->roles as $role) {
                $this->line("- {$role->name} ({$role->slug})");
            }
        } else {
            $this->line("- Нет ролей");
        }

        $this->line("\nРазрешения:");
        if ($user->permissions->count() > 0) {
            foreach ($user->permissions as $permission) {
                $this->line("- {$permission->name} ({$permission->slug})");
            }
        } else {
            $this->line("- Нет разрешений");
        }

        $this->line("\nПроверки:");
        $this->line("- isAdmin(): " . ($user->isAdmin() ? 'Да' : 'Нет'));
        $this->line("- isModerator(): " . ($user->isModerator() ? 'Да' : 'Нет'));
        $this->line("- hasRole('super_admin'): " . ($user->hasRole('super_admin') ? 'Да' : 'Нет'));

        return 0;
    }
}
