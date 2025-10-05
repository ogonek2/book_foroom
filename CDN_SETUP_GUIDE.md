# Руководство по настройке CDN для аватаров

## 🎯 Цель

Аватары пользователей должны сохраняться **только** на BunnyCDN, без fallback на локальное хранилище.

## ⚙️ Настройка

### 1. Добавьте настройки в .env файл

```env
# BunnyCDN Storage Settings
BUNNY_STORAGE_NAME=foxy-forum
BUNNY_STORAGE_PASSWORD=your_storage_password_here

# BunnyCDN CDN URL
BUNNY_CDN_URL=https://foxy-forum.b-cdn.net

# SSL настройки для разработки (отключить SSL проверку)
CDN_SSL_VERIFY=false
CDN_TIMEOUT=30
```

### 2. Получите данные для подключения

1. **Войдите в панель управления BunnyCDN**
2. **Перейдите в "Storage" → выберите ваш Storage Zone**
3. **Скопируйте:**
   - **Storage Name** (название вашего Storage Zone)
   - **Storage Password** (пароль для подключения)
4. **Перейдите в "Pull Zones" → выберите ваш Pull Zone**
5. **Скопируйте CDN URL**

### 3. Проверьте настройки

Убедитесь, что:
- ✅ Storage Zone создан в BunnyCDN
- ✅ CDN Pull Zone настроен и активен
- ✅ Storage Password настроен и имеет права на запись
- ✅ Storage Name соответствует вашему Storage Zone

## 🚀 Тестирование

### Проверка конфигурации и загрузки

```bash
php artisan cdn:test-upload
```

Эта команда:
- Проверит конфигурацию
- Создаст тестовый файл
- Загрузит его на CDN
- Покажет результат

## ⚠️ Решение проблем с SSL

Если возникает ошибка `cURL error 60: SSL certificate problem`, см. файл `SSL_SETUP_GUIDE.md` для подробных инструкций.

**Быстрое решение для разработки:**
Добавьте в `.env`:
```env
CDN_SSL_VERIFY=false
```

## 📁 Архитектура

### CDNUploader Helper

**Файл:** `app/Helpers/CDNUploader.php`

**Методы:**
- `uploadFile(UploadedFile $file, string $folder)` - универсальная загрузка (CDN + fallback)
- `uploadToBunnyCDN(UploadedFile $file, string $folder)` - загрузка на CDN
- `uploadLocally(UploadedFile $file, string $folder)` - локальная загрузка
- `deleteFromBunnyCDN(string $url)` - удаление файла с CDN

**Использование:**
```php
use App\Helpers\CDNUploader;

// Загрузка аватара (сначала CDN, потом локально)
$avatarUrl = CDNUploader::uploadFile($request->file('avatar'), 'avatars');

// Удаление файла
CDNUploader::deleteFromBunnyCDN($user->avatar);
```

### Контроллер

**Файл:** `app/Http/Controllers/ProfileController.php`

**Логика:**
1. При загрузке аватара вызывается `CDNUploader::uploadFile()`
2. Сначала пытается загрузить на CDN
3. Если CDN недоступен, загружает локально
4. Возвращается полный URL (CDN или локальный)
5. URL сохраняется в поле `avatar` пользователя
6. При удалении вызывается `CDNUploader::deleteFromBunnyCDN()`

### Модель User

**Файл:** `app/Models/User.php`

**Методы:**
- `getAvatarUrlAttribute()` - возвращает полный URL аватара
- `getAvatarDisplayAttribute()` - возвращает URL с fallback на UI Avatars

## 🔄 Процесс загрузки

1. **Пользователь выбирает файл**
2. **CDNUploader::upload() загружает файл на BunnyCDN**
3. **Возвращается полный URL CDN**
4. **URL сохраняется в базе данных**
5. **При отображении используется полный URL**

## ❌ Что НЕ используется

- ❌ Локальное хранилище Laravel
- ❌ FTP расширение PHP
- ❌ Fallback на локальные файлы
- ❌ Сложные трейты и сервисы

## 🎉 Результат

- ✅ Аватары сохраняются только на BunnyCDN
- ✅ Быстрая загрузка через CDN
- ✅ Простая и чистая архитектура
- ✅ Легко тестировать и отлаживать
- ✅ Автоматическое удаление старых аватаров
