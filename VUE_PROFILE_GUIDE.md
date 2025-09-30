# Руководство: Профиль на Vue.js

## Обзор

Страница профиля пользователя переработана с использованием **Vue.js 3** через CDN (без Vite). Реализована система вкладок с реактивным переключением контента.

## Технологии

- **Vue.js 3.3.4** - через CDN (https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js)
- **Компонентный подход** - каждая вкладка = отдельный компонент
- **Реактивность** - данные обновляются без перезагрузки страницы
- **Анимации** - плавное появление контента

## Структура вкладок

### 1. **Профіль** (Profile Tab)
**Компонент**: `ProfileTab`

**Отображает**:
- Последние прочитанные книги (до 4 книг)
- Обложки книг или плейсхолдеры
- Рейтинги книг (звездочки)
- Кнопки для просмотра книг

**Данные**:
```javascript
readingBooks: [
    {
        id, title, author, cover, rating, slug
    }
]
```

### 2. **Бібліотека** (Library Tab)
**Компонент**: `LibraryTab`

**Функционал**:
- Фильтрация по статусам:
  - **Всі** - все книги
  - **Читаю** - книги в процессе чтения
  - **Прочитано** - завершенные книги
  - **Буду читати** - книги в списке желаний

**Отображает**:
- Цветные статусы (синий/зеленый/желтый)
- Обложки книг
- Название и автор

**Данные**:
```javascript
libraryBooks: [
    {
        id, title, author, cover, status, slug
    }
]
```

### 3. **Рецензії** (Reviews Tab)
**Компонент**: `ReviewsTab`

**Отображает**:
- Все рецензии пользователя
- Рейтинг звездочками
- Текст рецензии
- Количество лайков и ответов
- Дата публикации

**Данные**:
```javascript
reviews: [
    {
        id, book_title, book_author, book_slug,
        rating, content, likes, replies, date
    }
]
```

### 4. **Обговорення** (Discussions Tab)
**Компонент**: `DiscussionsTab`

**Отображает**:
- **Теми** (слева) - созданные темы форума
- **Пости** (справа) - комментарии в темах
- Количество ответов в каждой теме
- Превью контента постов

**Данные**:
```javascript
topics: [
    { id, title, posts_count, url }
],
posts: [
    { id, content, url }
]
```

### 5. **Цитати** (Quotes Tab)
**Компонент**: `QuotesTab`

**Отображает**:
- Цитаты из книг в стиле blockquote
- Оранжевая граница слева
- Название книги и автор
- Дата добавления

**Данные**:
```javascript
quotes: [
    {
        id, text, book_title, book_author, date
    }
]
```

### 6. **Добірки** (Collections Tab)
**Компонент**: `CollectionsTab`

**Статус**: Пустая страница с приглашением создать добірку

**Будущий функционал**:
- Создание коллекций книг
- Группировка по темам
- Публичные/приватные добірки

## Vue.js Архитектура

### Главное приложение

```javascript
createApp({
    components: {
        ProfileTab,
        LibraryTab,
        ReviewsTab,
        DiscussionsTab,
        QuotesTab,
        CollectionsTab
    },
    data() {
        return {
            activeTab: 'profile', // Активная вкладка
            user: @json($user),   // Данные пользователя
            tabs: [...],          // Массив вкладок
            // Данные для каждой вкладки...
        };
    }
}).mount('#profileApp');
```

### Переключение вкладок

```html
<button 
    @click="activeTab = 'profile'"
    :class="activeTab === 'profile' ? 'active' : 'inactive'">
    Профіль
</button>
```

### Условное отображение

```html
<div v-show="activeTab === 'profile'">
    <profile-tab :user="user" :reading-books="readingBooks"></profile-tab>
</div>
```

## Компоненты

Каждый компонент определен как объект с:
- `props` - входные данные
- `data()` - локальное состояние (для LibraryTab)
- `computed` - вычисляемые свойства (фильтрация)
- `template` - HTML шаблон

### Пример компонента:

```javascript
const ProfileTab = {
    props: ['user', 'readingBooks'],
    template: `
        <div>
            <h2>{{ user.name }}'s Profile</h2>
            <div v-for="book in readingBooks" :key="book.id">
                {{ book.title }}
            </div>
        </div>
    `
};
```

## Передача данных из Laravel

Данные передаются через `@json()` директиву:

```javascript
readingBooks: @json($user->readingStatuses()
    ->where('status', 'read')
    ->with('book')
    ->get()
    ->map(function($status) {
        return [
            'id' => $status->book->id,
            'title' => $status->book->title,
            // ...
        ];
    }))
```

## Стилизация

### Анимация появления

```css
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
```

### Цветовая схема

- **Активная вкладка**: градиент orange-500 → pink-500
- **Неактивная вкладка**: серый фон с hover эффектом
- **Карточки**: bg-gray-800 с hover:shadow-xl
- **Текст**: белый/серый на темном фоне

## Интерактивность

### Фильтрация (Library Tab)

```javascript
data() {
    return {
        filter: 'all'
    };
},
computed: {
    filteredBooks() {
        if (this.filter === 'all') return this.libraryBooks;
        return this.libraryBooks.filter(book => book.status === this.filter);
    }
}
```

### Динамические классы

```javascript
:class="[
    'base-class',
    activeTab === tab.id ? 'active-class' : 'inactive-class'
]"
```

### Условный рендеринг

```html
<div v-if="items.length > 0">
    <!-- Показать список -->
</div>
<div v-else>
    <!-- Пустое состояние -->
</div>
```

## Преимущества

1. **Реактивность** - мгновенное переключение вкладок
2. **Без перезагрузки** - весь контент загружается один раз
3. **Компонентность** - легко добавлять новые вкладки
4. **Производительность** - данные загружаются при первой загрузке страницы
5. **Простота** - Vue.js через CDN, без сборки

## Недостатки

1. **Первая загрузка** - все данные загружаются сразу
2. **SEO** - контент генерируется на клиенте
3. **Размер** - Vue.js CDN добавляет ~100KB

## Будущие улучшения

1. **Ленивая загрузка** - загружать данные вкладок по требованию
2. **API endpoints** - получать данные через AJAX
3. **Пагинация** - для больших списков
4. **Поиск** - фильтрация контента
5. **Сортировка** - по дате, рейтингу, названию

## Расширение функционала

### Добавление новой вкладки:

1. **Создать компонент**:
```javascript
const NewTab = {
    props: ['data'],
    template: `<div>New Content</div>`
};
```

2. **Зарегистрировать компонент**:
```javascript
components: {
    NewTab
}
```

3. **Добавить в массив вкладок**:
```javascript
tabs: [
    { id: 'new', name: 'Новая', icon: '...' }
]
```

4. **Добавить отображение**:
```html
<div v-show="activeTab === 'new'">
    <new-tab :data="newData"></new-tab>
</div>
```

## Отладка

### Vue DevTools
Установите расширение Vue DevTools для браузера для отладки.

### Console
```javascript
// Доступ к Vue приложению через консоль
const app = document.querySelector('#profileApp').__vueParentComponent;
```

## Поддержка браузеров

- ✅ Chrome 64+
- ✅ Firefox 67+
- ✅ Safari 12+
- ✅ Edge 79+
