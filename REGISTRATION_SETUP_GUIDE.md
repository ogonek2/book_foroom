# Гід по налаштуванню покращеної реєстрації

## Що було додано

### 1. Багатоетапна форма реєстрації
- **Етап 1**: Особисті дані (ім'я, прізвище, email, телефон, пароль)
- **Етап 2**: Додаткова інформація (дата народження, місто, біо, улюблені жанри)
- **Етап 3**: Підтвердження та згода з умовами

### 2. Підтвердження електронної пошти
- Автоматичне надсилання листа після реєстрації
- Красивий HTML шаблон листа
- Сторінка підтвердження з можливістю повторного надсилання

### 3. OAuth інтеграція (Google та Facebook)
- Готові функції для OAuth
- Маршрути для авторизації
- Кнопки в інтерфейсі

### 4. Додаткові поля користувача
- Ім'я та прізвище окремо
- Телефон
- Дата народження
- Місто
- Біографія
- Улюблені жанри книг
- Підписка на розсилку

## Налаштування SMTP

### 1. Додайте в .env файл:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bookforum.com
MAIL_FROM_NAME="Книжковий форум"
```

### 2. Для Gmail:
1. Увійдіть в Google Account
2. Перейдіть в Security → 2-Step Verification
3. Увімкніть 2-Step Verification
4. Перейдіть в Security → App passwords
5. Створіть новий пароль для додатку
6. Використовуйте цей пароль в MAIL_PASSWORD

## Налаштування OAuth

### Google OAuth:
1. Перейдіть в [Google Cloud Console](https://console.cloud.google.com/)
2. Створіть новий проект або оберіть існуючий
3. Увімкніть Google+ API
4. Створіть OAuth 2.0 credentials
5. Додайте в .env:
```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### Facebook OAuth:
1. Перейдіть в [Facebook Developers](https://developers.facebook.com/)
2. Створіть новий додаток
3. Додайте Facebook Login продукт
4. Налаштуйте Valid OAuth Redirect URIs
5. Додайте в .env:
```env
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

## Оновлення контролера

Після налаштування .env файлу, оновіть контроллер:

```php
// Замініть статичні значення на динамічні з .env
private $smtpConfig = [
    'host' => env('MAIL_HOST', 'smtp.gmail.com'),
    'port' => env('MAIL_PORT', 587),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'from_address' => env('MAIL_FROM_ADDRESS'),
    'from_name' => env('MAIL_FROM_NAME')
];

private $oauthConfig = [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI')
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect_uri' => env('FACEBOOK_REDIRECT_URI')
    ]
];
```

## Нові маршрути

Додані маршрути:
- `GET /email/verify/{token}` - підтвердження email
- `GET /verification-notice` - сторінка підтвердження
- `POST /verification/resend` - повторне надсилання листа
- `GET /auth/google` - авторизація через Google
- `GET /auth/google/callback` - callback для Google
- `GET /auth/facebook` - авторизація через Facebook
- `GET /auth/facebook/callback` - callback для Facebook

## Нові поля в базі даних

Додані поля в таблицю `users`:
- `first_name` - ім'я
- `last_name` - прізвище
- `phone` - телефон
- `birth_date` - дата народження
- `city` - місто
- `favorite_genres` - улюблені жанри (JSON)
- `newsletter_subscribed` - підписка на розсилку
- `email_verification_token` - токен підтвердження email

## Тестування

1. Перейдіть на `/register`
2. Заповніть форму поетапно
3. Перевірте надходження листа підтвердження
4. Перевірте роботу OAuth кнопок (після налаштування)

## Примітки

- Всі тексти на українській мові
- Стилі відповідають дизайну сайту
- Форма адаптивна для мобільних пристроїв
- Валідація на фронтенді та бекенді
- Безпечне зберігання паролів
- Логування помилок відправки email
