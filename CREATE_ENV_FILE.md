# Создание файла .env

## 🚨 Проблема

Файл `.env` отсутствует в проекте, поэтому переменные окружения для BunnyCDN не загружаются.

## 🔧 Решение

### 1. Создайте файл .env

Создайте файл `.env` в корне проекта со следующим содержимым:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# BunnyCDN настройки
BUNNY_STORAGE_NAME=foxy-forum
BUNNY_STORAGE_PASSWORD=your_storage_password_here
BUNNY_CDN_URL=https://foxy-forum.b-cdn.net

# SSL настройки для разработки
CDN_SSL_VERIFY=false
CDN_TIMEOUT=30
```

### 2. Обновите настройки

Замените следующие значения на ваши реальные данные:

- `your_storage_password_here` - ваш пароль для BunnyCDN Storage
- `foxy-forum` - название вашего Storage Zone
- `https://foxy-forum.b-cdn.net` - URL вашего CDN

### 3. Сгенерируйте APP_KEY

После создания файла .env выполните:

```bash
php artisan key:generate
```

### 4. Проверьте настройки

Запустите команду проверки:

```bash
php artisan cdn:test-upload
```

## 📋 Минимальные настройки

Если у вас уже есть .env файл, просто добавьте эти строки:

```env
# BunnyCDN настройки
BUNNY_STORAGE_NAME=foxy-forum
BUNNY_STORAGE_PASSWORD=your_storage_password_here
BUNNY_CDN_URL=https://foxy-forum.b-cdn.net

# SSL настройки для разработки
CDN_SSL_VERIFY=false
CDN_TIMEOUT=30
```

## 🎯 Результат

После создания .env файла:
- ✅ Переменные окружения будут загружены
- ✅ CDN загрузка будет работать
- ✅ Аватары будут сохраняться на BunnyCDN
