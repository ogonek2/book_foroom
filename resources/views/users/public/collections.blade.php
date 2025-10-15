@extends('users.public.main')

@section('title', $user->name . ' - Добірки')

@section('profile-content')
    <div class="flex-1" id="public-collections-app">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Добірки користувача</h2>

            

            @if ($libraries->count() > 0)
                <!-- Collections List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
                    @foreach ($libraries as $library)
                        <library-collection
                            :library='@json($library)'
                            :is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                            :is-liked="{{ auth()->check() && $library->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }}"
                            :is-saved="{{ auth()->check() && DB::table('user_saved_libraries')->where('user_id', auth()->id())->where('library_id', $library->id)->exists() ? 'true' : 'false' }}"
                            :likes-count="{{ $library->likes()->count() }}"
                        ></library-collection>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Ще немає добірок</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не створив жодної колекції книг</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Инициализация Vue для публичной страницы добірок
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Vue) {
                    new Vue({ el: '#public-collections-app' });
                } else {
                    // Повторяем попытку, если Vue ещё не загрузился
                    setTimeout(function initVue(){
                        if (window.Vue) new Vue({ el: '#public-collections-app' });
                    }, 100);
                }
            });

            function loadLibraryBooks(libraryId) {
                // Здесь позже можно добавить AJAX-подгрузку книг
                window.location.href = `/libraries/${libraryId}`;
            }
        </script>
    @endpush
@endsection
