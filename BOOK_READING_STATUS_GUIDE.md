# Руководство по функционалу статусов чтения книг

## Обзор

Функционал статусов чтения книг позволяет пользователям отмечать книги как:
- **Прочитано** (`read`) - книга полностью прочитана
- **Читаю** (`reading`) - книга читается в данный момент
- **Буду читать** (`want_to_read`) - книга добавлена в список для чтения

## База данных

### Таблица `book_reading_statuses`

```sql
CREATE TABLE book_reading_statuses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    book_id BIGINT NOT NULL,
    status ENUM('read', 'reading', 'want_to_read') DEFAULT 'want_to_read',
    rating INT NULL, -- Оценка книги (1-10)
    review TEXT NULL, -- Отзыв о книге
    started_at DATE NULL, -- Дата начала чтения
    finished_at DATE NULL, -- Дата завершения чтения
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY unique_user_book (user_id, book_id),
    INDEX idx_user_status (user_id, status),
    INDEX idx_book_status (book_id, status),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);
```

## Модели

### BookReadingStatus

Основная модель для работы со статусами чтения.

**Константы статусов:**
- `STATUS_READ = 'read'`
- `STATUS_READING = 'reading'`
- `STATUS_WANT_TO_READ = 'want_to_read'`

**Основные методы:**
- `markAsRead($rating, $review)` - отметить как прочитанную
- `markAsReading()` - отметить как читаемую
- `markAsWantToRead()` - отметить как планируемую к чтению

### User (обновленная)

Добавлены отношения для работы со статусами чтения:

```php
// Все статусы чтения пользователя
$user->readingStatuses()

// Прочитанные книги
$user->readBooks()

// Читаемые книги
$user->readingBooks()

// Планируемые к чтению книги
$user->wantToReadBooks()
```

### Book (обновленная)

Добавлены отношения для работы со статусами чтения:

```php
// Все статусы чтения книги
$book->readingStatuses()

// Пользователи, прочитавшие книгу
$book->readByUsers()

// Пользователи, читающие книгу
$book->readingByUsers()

// Пользователи, планирующие читать книгу
$book->wantToReadByUsers()

// Статус чтения для конкретного пользователя
$book->getReadingStatusForUser($userId)
```

## API Endpoints

### Получение статуса чтения книги
```
GET /api/reading-status/book/{book}
```

### Установка/обновление статуса чтения
```
POST /api/reading-status/book/{book}
Content-Type: application/json

{
    "status": "read|reading|want_to_read",
    "rating": 8, // опционально, только для статуса "read"
    "review": "Отличная книга!" // опционально, только для статуса "read"
}
```

### Удаление статуса чтения
```
DELETE /api/reading-status/book/{book}
```

### Получение книг по статусу
```
GET /api/reading-status/books/{status}
```

### Получение статистики чтения
```
GET /api/reading-status/stats
```

### Обновление рейтинга и отзыва
```
PUT /api/reading-status/book/{book}/review
Content-Type: application/json

{
    "rating": 9,
    "review": "Потрясающая книга!"
}
```

## Использование в профиле

### Отображение последних прочитанных книг

В профиле пользователя отображаются:
- Последние 4 прочитанные книги
- Обложки книг или заглушки
- Рейтинги пользователя
- Кнопки для изменения статуса

### Статистика в правой панели

- Количество прочитанных книг
- Количество читаемых книг
- Количество планируемых к чтению книг
- Средний рейтинг пользователя
- Список текущих читаемых книг

## JavaScript функции

### updateReadingStatus(bookId, status)

Обновляет статус чтения книги через API.

```javascript
// Отметить книгу как читаемую
updateReadingStatus(123, 'reading');

// Отметить книгу как прочитанную
updateReadingStatus(123, 'read');

// Добавить в список для чтения
updateReadingStatus(123, 'want_to_read');
```

### showNotification(message, type)

Показывает уведомление пользователю.

```javascript
showNotification('Статус обновлен!', 'success');
showNotification('Ошибка!', 'error');
```

## Примеры использования

### В контроллере

```php
// Получить все прочитанные книги пользователя
$readBooks = $user->readBooks()->get();

// Получить статистику чтения
$stats = [
    'read_count' => $user->readingStatuses()->where('status', 'read')->count(),
    'reading_count' => $user->readingStatuses()->where('status', 'reading')->count(),
    'average_rating' => $user->readingStatuses()
        ->where('status', 'read')
        ->whereNotNull('rating')
        ->avg('rating'),
];
```

### В Blade шаблоне

```blade
@foreach($user->readingBooks() as $book)
    <div class="book-card">
        <h3>{{ $book->title }}</h3>
        <p>Начато: {{ $book->pivot->started_at->format('d.m.Y') }}</p>
    </div>
@endforeach
```

## Сидеры

### BookReadingStatusSeeder

Создает тестовые данные для статусов чтения:
- Случайно назначает 5-15 книг каждому пользователю
- Генерирует случайные статусы, рейтинги и отзывы
- Устанавливает реалистичные даты начала и завершения чтения

## Безопасность

- Все API endpoints защищены middleware `auth`
- Валидация входных данных
- CSRF защита для веб-запросов
- Уникальные ограничения на комбинацию пользователь-книга

## Производительность

- Индексы на часто используемые поля
- Eager loading для предотвращения N+1 запросов
- Пагинация для больших списков книг
- Кэширование статистики (можно добавить в будущем)
