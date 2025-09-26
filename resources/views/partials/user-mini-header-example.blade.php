{{-- 
Пример использования инклуда user-mini-header

Параметры:
- user: объект пользователя (null для гостей)
- timestamp: строка с временем (например, "3 дня назад")
- showGuest: boolean, показывать ли бейдж "Гість"

Примеры использования:
--}}

{{-- Для авторизованного пользователя --}}
@include('partials.user-mini-header', [
    'user' => $user,
    'timestamp' => '3 дня назад'
])

{{-- Для гостя --}}
@include('partials.user-mini-header', [
    'user' => null,
    'timestamp' => '1 час назад',
    'showGuest' => true
])

{{-- Для рецензии (автоматически определяет гостя) --}}
@include('partials.user-mini-header', [
    'user' => $review->isGuest() ? null : $review->user,
    'timestamp' => $review->created_at->diffForHumans(),
    'showGuest' => $review->isGuest()
])

{{-- Только аватар без времени --}}
@include('partials.user-mini-header', [
    'user' => $user
])

{{--
Особенности:
1. Если у пользователя есть аватар, он загружается из Storage
2. Если аватар не загружается, показывается fallback с первой буквой имени
3. Для гостей показывается иконка пользователя
4. Поддерживает светлую и темную темы
5. Адаптивный дизайн
--}}
