<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user {--email=} {--name=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user with super admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Создание пользователя администратора...');

        // Получаем данные от пользователя или используем опции
        $email = $this->option('email') ?: $this->ask('Email адрес');
        $name = $this->option('name') ?: $this->ask('Имя пользователя');
        $password = $this->option('password') ?: $this->secret('Пароль');

        // Валидация
        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ], [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Создаем пользователя
        $user = User::create([
            'name' => $name,
            'username' => strtolower(str_replace(' ', '_', $name)),
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Назначаем роль супер администратора
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        
        if (!$superAdminRole) {
            $this->error('Роль "super_admin" не найдена. Запустите сначала сидер: php artisan db:seed --class=RolePermissionSeeder');
            return 1;
        }

        $user->assignRole($superAdminRole);

        $this->info("Пользователь {$name} ({$email}) успешно создан с ролью Super Admin!");
        $this->info('Теперь вы можете войти в админ панель по адресу: /admin11');

        return 0;
    }
}
