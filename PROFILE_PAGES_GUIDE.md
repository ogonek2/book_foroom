# Руководство: Структура страниц профиля

## Обзор

Профиль пользователя разделен на отдельные страницы с общим layout. Каждая страница имеет свой маршрут и контент.

## Структура файлов

```
resources/views/profile/
├── layout.blade.php          # Основной layout (шапка, меню, панель статистики)
├── show.blade.php            # Старый файл (можно удалить)
├── edit.blade.php            # Редактирование профиля
└── pages/
    ├── overview.blade.php    # Профіль (прочитанные книги + рецензии)
    ├── library.blade.php     # Бібліотека (все книги с фильтрацией)
    ├── reviews.blade.php     # Рецензії (все рецензии)
    ├── discussions.blade.php # Обговорення (темы и посты)
    ├── quotes.blade.php      # Цитати (сохраненные цитаты)
    └── collections.blade.php # Добірки (коллекции книг)
```

## Маршруты

### Основные маршруты:
```
GET  /profile                          - Мой профиль (обзор)
GET  /profile/edit                     - Редактирование профиля
PUT  /profile                          - Обновление профиля
DELETE /profile/avatar                 - Удаление аватарки
```

### Публичные маршруты (просмотр чужих профилей):
```
GET  /profile/{username}               - Профиль пользователя (обзор)
GET  /profile/{username}/library       - Библиотека пользователя
GET  /profile/{username}/reviews       - Рецензии пользователя
GET  /profile/{username}/discussions   - Обсуждения пользователя
GET  /profile/{username}/quotes        - Цитаты пользователя
GET  /profile/{username}/collections   - Добірки пользователя
```

## Layout (profile/layout.blade.php)

Основной layout содержит:

### 1. Шапка профиля:
- Размытый фон (аватарка или градиент)
- Аватарка пользователя (200x200px)
- Имя и username
- Биография

### 2. Левое меню (навигация):
- **Профіль** - обзор с книгами и рецензиями
- **Бібліотека** - все книги с фильтрацией
- **Рецензії** - все рецензии
- **Обговорення** - темы форума и посты
- **Цитати** - сохраненные цитаты
- **Добірки** - коллекции книг

Активная страница подсвечивается градиентом orange-pink.

### 3. Правая панель (статистика):
- Статус "Бібліоман" и online индикатор
- Список текущих читаемых книг (до 3)
- Статистика:
  - Прочитано
  - Читає
  - Планує
  - Середня оцінка

### 4. Центральный контент:
```blade
@yield('profile-content')
```

## Страницы

### 1. Overview (Профіль)
**Файл**: `pages/overview.blade.php`  
**Маршрут**: `profile.show` или `profile.user.show`

**Содержит**:
- Последние 4 прочитанные книги (grid 2x2)
- Последние 3 рецензии
- Ссылка "Всі рецензії" если их больше 3

### 2. Library (Бібліотека)
**Файл**: `pages/library.blade.php`  
**Маршрут**: `profile.library`

**Функционал**:
- Фильтрация по статусам:
  - Всі
  - Читаю (синий badge)
  - Прочитано (зеленый badge)
  - Буду читати (желтый badge)
- Grid 4 колонки
- Пагинация (12 книг на странице)

**Параметры URL**:
```
/profile/{username}/library?filter=reading
/profile/{username}/library?filter=read
/profile/{username}/library?filter=want_to_read
```

### 3. Reviews (Рецензії)
**Файл**: `pages/reviews.blade.php`  
**Маршрут**: `profile.reviews`

**Содержит**:
- Все рецензии пользователя
- Полный текст рецензий
- Рейтинг, лайки, комментарии
- Пагинация (10 рецензий на странице)

### 4. Discussions (Обговорення)
**Файл**: `pages/discussions.blade.php`  
**Маршрут**: `profile.discussions`

**Содержит**:
- Grid 2 колонки:
  - **Левая**: Созданные темы
  - **Правая**: Посты в темах
- Пагинация для тем
- Лимит 10 последних постов

### 5. Quotes (Цитати)
**Файл**: `pages/quotes.blade.php`  
**Маршрут**: `profile.quotes`

**Содержит**:
- Сохраненные цитаты из книг
- Blockquote стиль с оранжевой границей
- Grid 2 колонки
- Пагинация (12 цитат на странице)

### 6. Collections (Добірки)
**Файл**: `pages/collections.blade.php`  
**Маршрут**: `profile.collections`

**Статус**: Заглушка с кнопкой "Створити добірку"

## ProfileController

### Методы:

```php
// Обзор профиля
public function show($username = null)
{
    $user = $username ? User::where('username', $username)->firstOrFail() : Auth::user();
    return view('profile.pages.overview', compact('user'));
}

// Библиотека
public function library($username)
{
    $user = User::where('username', $username)->firstOrFail();
    return view('profile.pages.library', compact('user'));
}

// Рецензии
public function reviews($username)
{
    $user = User::where('username', $username)->firstOrFail();
    return view('profile.pages.reviews', compact('user'));
}

// И т.д. для остальных страниц
```

## Использование

### Свой профиль:
```blade
<a href="{{ route('profile.show') }}">Мой профиль</a>
<a href="{{ route('profile.library', auth()->user()->username) }}">Моя библиотека</a>
```

### Чужой профиль:
```blade
<a href="{{ route('profile.show', $user->username) }}">Профиль {{ $user->name }}</a>
<a href="{{ route('profile.library', $user->username) }}">Библиотека</a>
```

## Навигация

Меню автоматически подсвечивает активную страницу через `request()->routeIs()`:

```blade
:class="request()->routeIs('profile.library') ? 'bg-gradient-to-r from-orange-500 to-pink-500 text-white' : 'text-gray-300 hover:bg-gray-700'"
```

## Расширение

### Добавление новой страницы:

1. **Создать view**: `resources/views/profile/pages/new-page.blade.php`
```blade
@extends('profile.layout')

@section('profile-content')
    <div>
        <!-- Ваш контент -->
    </div>
@endsection
```

2. **Добавить метод в ProfileController**:
```php
public function newPage($username)
{
    $user = User::where('username', $username)->firstOrFail();
    return view('profile.pages.new-page', compact('user'));
}
```

3. **Добавить маршрут**:
```php
Route::get('/profile/{username}/new-page', [ProfileController::class, 'newPage'])->name('profile.new-page');
```

4. **Добавить в меню** в `layout.blade.php`:
```blade
<a href="{{ route('profile.new-page', $user->username) }}" ...>
    Нова сторінка
</a>
```

## Преимущества

1. **Модульность** - каждая страница в отдельном файле
2. **Переиспользование** - общий layout для всех страниц
3. **SEO-friendly** - отдельные URL для каждой страницы
4. **Чистый код** - легко поддерживать и расширять
5. **Пагинация** - поддержка больших списков
6. **Публичные профили** - можно просматривать профили других пользователей

## Особенности

- **Авторизация**: Middleware `auth` защищает все маршруты
- **Поиск по username**: Используется `username` вместо `id`
- **404 обработка**: `firstOrFail()` выбрасывает 404, если пользователь не найден
- **Статистика**: Рассчитывается динамически на каждой странице
