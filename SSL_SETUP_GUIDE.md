# Руководство по настройке SSL для BunnyCDN

## 🚨 Проблема

Ошибка: `cURL error 60: SSL certificate problem: unable to get local issuer certificate`

Эта ошибка возникает на Windows при подключении к BunnyCDN из-за проблем с SSL сертификатами.

## 🔧 Решения

### Решение 1: Отключение SSL проверки (для разработки)

**Добавьте в .env файл:**
```env
# SSL настройки для разработки
CDN_SSL_VERIFY=false
CDN_TIMEOUT=30
```

**ВНИМАНИЕ:** Это решение только для разработки! В продакшене SSL проверка должна быть включена.

### Решение 2: Установка SSL сертификатов (рекомендуется)

#### Для XAMPP:
1. **Скачайте cacert.pem:**
   - Перейдите на https://curl.haxx.se/ca/cacert.pem
   - Сохраните файл как `cacert.pem`

2. **Поместите файл в папку PHP:**
   - Обычно: `C:\xampp\php\cacert.pem`

3. **Обновите php.ini:**
   ```ini
   curl.cainfo = "C:\xampp\php\cacert.pem"
   openssl.cafile = "C:\xampp\php\cacert.pem"
   ```

4. **Перезапустите Apache**

#### Для OpenServer:
1. **Скачайте cacert.pem** (как выше)
2. **Поместите в папку PHP OpenServer:**
   - Обычно: `C:\OpenServer\modules\php\PHP-8.2-x64\cacert.pem`

3. **Обновите php.ini OpenServer:**
   ```ini
   curl.cainfo = "C:\OpenServer\modules\php\PHP-8.2-x64\cacert.pem"
   openssl.cafile = "C:\OpenServer\modules\php\PHP-8.2-x64\cacert.pem"
   ```

4. **Перезапустите OpenServer**

### Решение 3: Использование HTTP вместо HTTPS (не рекомендуется)

Если SSL проблемы критичны, можно временно использовать HTTP:

```env
# Временно использовать HTTP (НЕ рекомендуется для продакшена)
BUNNY_CDN_URL=http://foxy-forum.b-cdn.net
```

## 🎯 Рекомендуемая настройка для разработки

**Добавьте в .env:**
```env
# BunnyCDN настройки
BUNNY_STORAGE_NAME=foxy-forum
BUNNY_STORAGE_PASSWORD=your_storage_password_here
BUNNY_CDN_URL=https://foxy-forum.b-cdn.net

# SSL настройки для разработки
CDN_SSL_VERIFY=false
CDN_TIMEOUT=30
```

## 🚀 Проверка

После настройки запустите тест:
```bash
php artisan cdn:test-upload
```

## ⚠️ Важные замечания

1. **Для разработки:** `CDN_SSL_VERIFY=false` - безопасно
2. **Для продакшена:** `CDN_SSL_VERIFY=true` - обязательно
3. **Локальные сертификаты:** Устанавливайте только если уверены в безопасности
4. **HTTP вместо HTTPS:** Используйте только в крайнем случае

## 🔍 Отладка

Если проблемы продолжаются:

1. **Проверьте логи Laravel:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Проверьте настройки PHP:**
   ```bash
   php -m | grep -i ssl
   php -i | grep -i curl
   ```

3. **Тестируйте подключение:**
   ```bash
   curl -I https://storage.bunnycdn.com
   ```
