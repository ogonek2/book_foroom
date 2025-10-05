<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignRoleToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:assign-role {user_id} {role_slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $roleSlug = $this->argument('role_slug');

        // Найти пользователя
        $user = User::find($userId);
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден.");
            return 1;
        }

        // Найти роль
        $role = Role::where('slug', $roleSlug)->first();
        if (!$role) {
            $this->error("Роль с slug '{$roleSlug}' не найдена.");
            return 1;
        }

        // Назначить роль
        $user->assignRole($role);

        $this->info("Роль '{$role->name}' успешно назначена пользователю {$user->name} ({$user->email}).");

        return 0;
    }
}
