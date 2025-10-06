@extends('layouts.app')

@section('title', 'Обговорення')

@section('main')
    <div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
        <!-- Header -->
        <div class="">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-light-text-primary dark:text-dark-text-primary mb-2">Обговорення
                        </h1>
                        <p class="text-light-text-secondary dark:text-dark-text-secondary">Обговорюйте книги, діліться
                            думками і знаходьте єдиномисленних</p>
                    </div>
                    @auth
                        <a href="{{ route('discussions.create') }}"
                            class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-bold hover:from-brand-600 hover:to-accent-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Стоврити обговорення
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="py-4">
            <!-- Discussions List -->
            @if ($discussions->count() > 0)
                <div class="space-y-4">
                    @foreach ($discussions as $discussion)
                        <div
                            class="rounded-2xl p-4 border border-light-border dark:border-dark-border hover:border-brand-300 dark:hover:border-brand-600">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Discussion Header -->
                                    <div class="flex items-center space-x-3 mb-3">
                                        @if ($discussion->is_pinned)
                                            <span
                                                class="bg-yellow-500 text-yellow-900 dark:bg-yellow-600 dark:text-yellow-100 px-2 py-1 rounded-full text-xs font-bold">
                                                <i class="fas fa-thumbtack mr-1"></i>
                                                Закреплено
                                            </span>
                                        @endif
                                        @if ($discussion->is_closed)
                                            <span
                                                class="bg-red-500 text-red-900 dark:bg-red-600 dark:text-red-100 px-2 py-1 rounded-full text-xs font-bold">
                                                <i class="fas fa-lock mr-1"></i>
                                                Закрыто
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-2 mb-3">
                                        <a href="{{ route('users.public.profile', $discussion->user->username) }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                                            <img src="{{ $discussion->user->avatar_display }}"
                                                alt="{{ $discussion->user->name }}" class="w-6 h-6 rounded-full">
                                            <span>{{ $discussion->user->name }}</span>
                                        </a>
                                        <div class="text-light-text-tertiary dark:text-dark-text-tertiary">
                                            {{ $discussion->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <!-- Discussion Title -->
                                    <h3
                                        class="text-md font-bold text-light-text-primary dark:text-dark-text-primary mb-2 hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                                        <a href="{{ route('discussions.show', $discussion) }}">
                                            {{ $discussion->title }}
                                        </a>
                                    </h3>

                                    <!-- Discussion Content Preview -->
                                    <p class="text-sm text-light-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">
                                        {{ Str::limit(strip_tags($discussion->content), 200) }}
                                    </p>

                                    <!-- Discussion Meta -->
                                    <div
                                        class="flex items-center justify-between text-sm text-light-text-tertiary dark:text-dark-text-tertiary">
                                        <div class="flex items-center space-x-6">
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-eye"></i>
                                                <span>{{ $discussion->views_count }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-comments"></i>
                                                <span>{{ $discussion->replies_count }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-heart"></i>
                                                <span>{{ $discussion->likes_count }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $discussions->appends(request()->query())->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl p-12 border border-light-border dark:border-dark-border shadow-xl">
                        <svg class="w-24 h-24 mx-auto mb-6 text-light-text-tertiary dark:text-dark-text-tertiary"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <h3 class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">Пока нет
                            обсуждений</h3>
                        <p class="text-light-text-secondary dark:text-dark-text-secondary mb-8">Станьте первым, кто создаст
                            обсуждение!</p>
                        @auth
                            <a href="{{ route('discussions.create') }}"
                                class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-8 py-4 rounded-xl font-bold hover:from-brand-600 hover:to-accent-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-plus mr-2"></i>
                                Создать обсуждение
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush
@endsection
