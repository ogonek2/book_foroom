<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🔍 Тестирование доступа пользователя ID 18...\n\n";

$user = User::with('roles', 'permissions')->find(18);

if (!$user) {
    echo "❌ Пользователь не найден!\n";
    exit(1);
}

echo "👤 Пользователь: {$user->name} ({$user->email})\n";

echo "\n📋 Роли:\n";
foreach ($user->roles as $role) {
    echo "- {$role->name} ({$role->slug})\n";
}

echo "\n✅ Проверки доступа:\n";
echo "- isAdmin(): " . ($user->isAdmin() ? '✅ Да' : '❌ Нет') . "\n";
echo "- isModerator(): " . ($user->isModerator() ? '✅ Да' : '❌ Нет') . "\n";
echo "- hasRole('super_admin'): " . ($user->hasRole('super_admin') ? '✅ Да' : '❌ Нет') . "\n";
echo "- hasRole('admin'): " . ($user->hasRole('admin') ? '✅ Да' : '❌ Нет') . "\n";
echo "- hasPermission('admin.access'): " . ($user->hasPermission('admin.access') ? '✅ Да' : '❌ Нет') . "\n";

if ($user->isAdmin()) {
    echo "\n🎉 Пользователь МОЖЕТ получить доступ к админ панели!\n";
} else {
    echo "\n❌ Пользователь НЕ МОЖЕТ получить доступ к админ панели!\n";
}

echo "\n🔧 Дополнительная диагностика:\n";
echo "- Количество ролей: " . $user->roles->count() . "\n";
echo "- Количество разрешений: " . $user->permissions->count() . "\n";

// Проверим, есть ли роль admin в системе
$adminRole = \App\Models\Role::where('slug', 'admin')->first();
echo "- Роль 'admin' существует: " . ($adminRole ? '✅ Да' : '❌ Нет') . "\n";

if ($adminRole) {
    echo "- Роль 'admin' активна: " . ($adminRole->is_active ? '✅ Да' : '❌ Нет') . "\n";
}
