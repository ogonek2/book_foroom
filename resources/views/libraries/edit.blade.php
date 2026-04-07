@extends('layouts.app')

@section('title', 'Редагувати добірку - ' . $library->name)

@section('main')
<div id="app">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('libraries.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-orange-500">Добірки</a></li>
                <li><span class="text-slate-400 dark:text-slate-500">/</span></li>
                <li><a href="{{ route('libraries.show.slug', ['username' => $library->user->username, 'library' => $library->slug]) }}" class="text-slate-600 dark:text-slate-400 hover:text-orange-500">{{ $library->name }}</a></li>
                <li><span class="text-slate-400 dark:text-slate-500">/</span></li>
                <li class="text-slate-900 dark:text-white font-medium">Редагувати</li>
            </ol>
        </nav>

        <div class="mb-6">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mb-1">Редагувати добірку</h1>
            <p class="text-slate-600 dark:text-slate-400">Оновіть назву, опис, видимість і вміст добірки.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[360px,minmax(0,1fr)] gap-6 lg:items-start">
            <section class="lib-glass rounded-3xl p-5 sm:p-6 lg:sticky lg:top-24 lg:self-start">
                <form action="{{ route('libraries.update', $library) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Назва добірки</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $library->name) }}"
                            class="lib-input"
                            placeholder="Введіть назву добірки"
                            required
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Опис добірки</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="lib-input resize-none"
                            placeholder="Опишіть вашу добірку..."
                        >{{ old('description', $library->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Видимість</label>
                        <div class="space-y-2">
                            <label class="lib-option group">
                                <input
                                    type="radio"
                                    name="is_private"
                                    value="0"
                                    class="sr-only peer"
                                    {{ old('is_private', $library->is_private ? '1' : '0') == '0' ? 'checked' : '' }}
                                >
                                <span class="lib-radio-indicator" aria-hidden="true"></span>
                                <span class="font-semibold text-slate-900 dark:text-white">Публічна</span>
                                <span class="text-xs text-slate-600 dark:text-slate-400">Доступна всім користувачам.</span>
                            </label>
                            <label class="lib-option group">
                                <input
                                    type="radio"
                                    name="is_private"
                                    value="1"
                                    class="sr-only peer"
                                    {{ old('is_private', $library->is_private ? '1' : '0') == '1' ? 'checked' : '' }}
                                >
                                <span class="lib-radio-indicator" aria-hidden="true"></span>
                                <span class="font-semibold text-slate-900 dark:text-white">Приватна</span>
                                <span class="text-xs text-slate-600 dark:text-slate-400">Доступна тільки вам.</span>
                            </label>
                        </div>
                        @error('is_private')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="flex justify-between w-full gap-2">
                                <a href="{{ route('libraries.show.slug', ['username' => $library->user->username, 'library' => $library->slug]) }}" class="acc-btn">Скасувати</a>
                                <button type="button" class="acc-btn !border-red-400/40 !text-red-700 dark:!text-red-300" @click="deleteLibrary">
                                    <i class="fas fa-trash-alt mr-2"></i>Видалити
                                </button>
                            </div>
                            <button type="submit" class="btn-primary mt-4">
                                <i class="fas fa-check mr-2"></i>Зберегти зміни
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="min-w-0">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Книги в добірці</h2>
                        <p class="text-slate-600 dark:text-slate-400">Керуйте вмістом добірки.</p>
                    </div>
                    <a href="{{ route('books.index') }}" class="acc-btn-primary !px-3 !py-1.5">
                        <i class="fas fa-plus mr-2"></i>Додати книги
                    </a>
                </div>

                @php
                    $books = $library->books()->with(['author', 'categories'])->paginate(12);
                @endphp

                @if($books->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($books as $book)
                            <article class="rounded-xl overflow-hidden relative bg-white/70 dark:bg-slate-800/70">
                                <a href="{{ route('books.show', $book->slug) }}" class="block">
                                    <img
                                        src="{{ $book->cover_image_display }}"
                                        data-fallback="bookCover"
                                        alt="{{ $book->title }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-56 w-full object-cover border border-slate-200/70 dark:border-slate-700/70"
                                    >
                                </a>
                                <div class="p-3">
                                    <a href="{{ route('books.show', $book->slug) }}" class="line-clamp-2 text-sm font-bold text-slate-900 dark:text-white hover:text-orange-500">
                                        {{ $book->title }}
                                    </a>
                                    <div class="mt-1 text-xs text-slate-600 dark:text-slate-400 truncate">
                                        {{ $book->author->first_name ?? $book->author ?? 'Автор не вказаний' }}
                                    </div>
                                </div>
                                <button
                                    @click="removeBook({{ (int) $book->id }})"
                                    class="mt-3 w-full rounded-lg bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-700 dark:text-red-300 hover:bg-red-500/20 transition"
                                >
                                    <i class="fas fa-trash-alt mr-1"></i>Видалити
                                </button>
                            </article>
                        @endforeach
                    </div>

                    @if($books->hasPages())
                        <div class="mt-6">
                            {{ $books->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-10">
                        <div class="text-slate-500 dark:text-slate-400 text-sm">Добірка поки порожня.</div>
                        <a href="{{ route('books.index') }}" class="inline-flex mt-3 acc-btn-primary">Додати книги</a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

@push('styles')
<style>
    .lib-glass {
        background: rgba(255, 255, 255, 0.84);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(148, 163, 184, 0.25);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
    }
    .dark .lib-glass {
        background: rgba(15, 23, 42, 0.78);
        border-color: rgba(255, 255, 255, 0.10);
        box-shadow: 0 16px 40px rgba(2, 6, 23, 0.45);
    }
    .lib-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.45);
        background: rgba(255, 255, 255, 0.95);
        color: rgb(15 23 42);
        font-size: 0.9rem;
        outline: none;
        transition: all .2s ease;
    }
    .lib-input:focus {
        border-color: rgba(99, 102, 241, 0.7);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .dark .lib-input {
        background: rgba(30, 41, 59, 0.85);
        color: rgb(241 245 249);
        border-color: rgba(100, 116, 139, 0.45);
    }
    .lib-option {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: .15rem .75rem;
        align-items: start;
        border: 1px solid rgba(148, 163, 184, 0.35);
        border-radius: .75rem;
        padding: .75rem;
        background: rgba(255, 255, 255, .65);
        cursor: pointer;
        transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
    }
    .dark .lib-option {
        background: rgba(30, 41, 59, .45);
        border-color: rgba(100, 116, 139, .35);
    }
    .lib-option:hover {
        border-color: rgba(99, 102, 241, 0.45);
        background: rgba(255, 255, 255, .82);
    }
    .dark .lib-option:hover {
        background: rgba(30, 41, 59, .65);
    }
    .lib-radio-indicator {
        width: 1rem;
        height: 1rem;
        border-radius: 9999px;
        border: 2px solid rgba(148, 163, 184, 0.85);
        box-shadow: inset 0 0 0 0 rgba(99, 102, 241, 0.95);
        transition: all .2s ease;
        margin-top: .22rem;
        grid-row: 1 / span 2;
        align-self: start;
    }
    .peer:checked + .lib-radio-indicator {
        border-color: rgba(99, 102, 241, 0.95);
        box-shadow: inset 0 0 0 3px rgba(99, 102, 241, 0.95);
    }
    .peer:focus-visible + .lib-radio-indicator {
        box-shadow: inset 0 0 0 3px rgba(99, 102, 241, 0.95), 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .lib-option:has(.peer:checked) {
        border-color: rgba(99, 102, 241, 0.75);
        background: rgba(99, 102, 241, 0.08);
    }
    .dark .lib-option:has(.peer:checked) {
        background: rgba(99, 102, 241, 0.18);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Vue({
        el: '#app',
        methods: {
            async removeBook(bookId) {
                const ok = await confirm('Видалити книгу з добірки?', 'Підтвердження', 'warning');
                if (!ok) return;
                try {
                    const res = await fetch(`/libraries/{{ $library->id }}/books/${bookId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const data = await res.json();
                    if (!res.ok || !data?.success) {
                        throw new Error(data?.message || 'Не вдалося видалити книгу.');
                    }
                    location.reload();
                } catch (e) {
                    alert(e?.message || 'Помилка при видаленні книги');
                }
            },
            async deleteLibrary() {
                const ok = await confirm('Видалити цю добірку? Дію неможливо скасувати.', 'Підтвердження', 'warning');
                if (!ok) return;
                try {
                    const res = await fetch(`/libraries/{{ $library->id }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    if (!res.ok) {
                        throw new Error('Не вдалося видалити добірку.');
                    }
                    window.location.href = '{{ route("libraries.index") }}';
                } catch (e) {
                    alert(e?.message || 'Помилка при видаленні добірки');
                }
            },
        },
    });
});
</script>
@endpush
@endsection