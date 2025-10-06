@extends('layouts.app')

@section('title', 'Участники сообщества')

@section('main')
<div class="max-w-7xl mx-auto">
    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-2">
            
            <!-- Search Box -->
            <div class="bg-white backdrop-blur-xl rounded-xl p-4 border border-white/20 shadow-2xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">
                    Пошук користувачів
                </h3>
                <form method="GET" action="{{ route('users.index') }}" class="space-y-4">
                    <div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Ім'я або юзернейм..."
                               class="w-full px-4 py-3 bg-light-bg-secondary dark:bg-dark-bg-tertiary border border-light-border dark:border-dark-border rounded-lg text-light-text-primary dark:text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all duration-200">
                    </div>
                    <button type="submit" 
                            class="w-full bg-brand-500 hover:bg-brand-600 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>Шукати
                    </button>
                    @if(request('search'))
                        <a href="{{ route('users.index') }}" 
                           class="block w-full text-center text-light-text-tertiary dark:text-dark-text-tertiary hover:text-brand-500 transition-colors duration-200">
                            <i class="fas fa-times mr-1"></i>Очистити пошук
                        </a>
                    @endif
                </form>
            </div>

            <!-- Filters -->
            <div class="bg-white backdrop-blur-xl rounded-xl p-4 border border-white/20 shadow-2xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">
                    Фільтри
                </h3>
                <form method="GET" action="{{ route('users.index') }}" class="space-y-4">
                    
                    <!-- Rating Filter -->
                    <div>
                        <label class="block text-sm font-medium text-light-text-primary dark:text-dark-text-primary mb-2">
                            Рейтинг
                        </label>
                        <select name="rating_filter" 
                                class="w-full px-3 py-2 bg-light-bg-secondary dark:bg-dark-bg-tertiary border border-light-border dark:border-dark-border rounded-lg text-light-text-primary dark:text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="">Всі рейтинги</option>
                            <option value="9_stars" {{ request('rating_filter') == '9_stars' ? 'selected' : '' }}>9+ зірок</option>
                            <option value="7_stars" {{ request('rating_filter') == '7_stars' ? 'selected' : '' }}>7+ зірок</option>
                            <option value="5_stars" {{ request('rating_filter') == '5_stars' ? 'selected' : '' }}>5+ зірок</option>
                        </select>
                    </div>

                    <!-- Activity Filter -->
                    <div>
                        <label class="block text-sm font-medium text-light-text-primary dark:text-dark-text-primary mb-2">
                            Активність
                        </label>
                        <select name="activity_filter" 
                                class="w-full px-3 py-2 bg-light-bg-secondary dark:bg-dark-bg-tertiary border border-light-border dark:border-dark-border rounded-lg text-light-text-primary dark:text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="">Вся активність</option>
                            <option value="most_reviews" {{ request('activity_filter') == 'most_reviews' ? 'selected' : '' }}>Більше рецензій</option>
                            <option value="most_quotes" {{ request('activity_filter') == 'most_quotes' ? 'selected' : '' }}>Більше цитат</option>
                            <option value="most_discussions" {{ request('activity_filter') == 'most_discussions' ? 'selected' : '' }}>Більше обговорень</option>
                            <option value="most_books_read" {{ request('activity_filter') == 'most_books_read' ? 'selected' : '' }}>Більше прочитаних книг</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-medium text-light-text-primary dark:text-dark-text-primary mb-2">
                            Сортування
                        </label>
                        <select name="sort" 
                                class="w-full px-3 py-2 bg-light-bg-secondary dark:bg-dark-bg-tertiary border border-light-border dark:border-dark-border rounded-lg text-light-text-primary dark:text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>За рейтингом</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>За ім'ям</option>
                            <option value="username" {{ request('sort') == 'username' ? 'selected' : '' }}>За юзернеймом</option>
                            <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>За рецензіями</option>
                            <option value="quotes" {{ request('sort') == 'quotes' ? 'selected' : '' }}>За цитатами</option>
                        </select>
                    </div>

                    <!-- Hidden fields to preserve search -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Застосувати
                    </button>
                </form>
            </div>

            <!-- Stats Overview -->
            <div class="bg-white backdrop-blur-xl rounded-xl p-4 border border-white/20 shadow-2xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">
                    Статистика
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-light-text-secondary dark:text-dark-text-secondary">Всього користувачів</span>
                        <span class="font-semibold text-light-text-primary dark:text-dark-text-primary">{{ $stats['total_users'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-light-text-secondary dark:text-dark-text-secondary">Активних</span>
                        <span class="font-semibold text-brand-500">{{ $stats['active_users'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-light-text-secondary dark:text-dark-text-secondary">Знайдено</span>
                        <span class="font-semibold text-accent-500">{{ $users->total() }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Reviewers -->
            <div class="bg-white backdrop-blur-xl rounded-xl p-4 border border-white/20 shadow-2xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">
                    Топ рецензенти
                </h3>
                <div class="space-y-3">
                    @forelse($stats['top_reviewers'] as $index => $reviewer)
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-brand-500 to-accent-500 flex items-center justify-center text-white text-sm font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('users.public.profile', $reviewer->username) }}" 
                                   class="text-sm font-medium text-light-text-primary dark:text-dark-text-primary hover:text-brand-500 transition-colors duration-200 truncate block">
                                    {{ $reviewer->name }}
                                </a>
                                <p class="text-xs text-light-text-tertiary dark:text-dark-text-tertiary">
                                    {{ $reviewer->main_reviews_count }} рецензій
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">Немає даних</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white backdrop-blur-xl rounded-xl p-4 border border-white/20 shadow-2xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">
                    Остання активність
                </h3>
                <div class="space-y-3">
                    @foreach($stats['recent_activity']['reviews']->take(2) as $review)
                        <div class="flex items-start space-x-3">
                            <img src="{{ $review->user->avatar_display }}" 
                                 alt="{{ $review->user->name }}" 
                                 class="w-10 h-10 rounded-full object-cover border border-light-border dark:border-dark-border">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-light-text-primary dark:text-dark-text-primary">
                                    <a href="{{ route('users.public.profile', $review->user->username) }}" 
                                       class="font-medium hover:text-brand-500 transition-colors duration-200">
                                        {{ $review->user->name }}
                                    </a> написав рецензію
                                </p>
                                <p class="text-xs text-light-text-tertiary dark:text-dark-text-tertiary truncate">
                                    {{ $review->book->title }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                    
                    @foreach($stats['recent_activity']['quotes']->take(1) as $quote)
                        <div class="flex items-start space-x-3">
                            <img src="{{ $quote->user->avatar_display }}" 
                                 alt="{{ $quote->user->name }}" 
                                 class="w-10 h-10 rounded-full object-cover border border-light-border dark:border-dark-border">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-light-text-primary dark:text-dark-text-primary">
                                    <a href="{{ route('users.public.profile', $quote->user->username) }}" 
                                       class="font-medium hover:text-brand-500 transition-colors duration-200">
                                        {{ $quote->user->name }}
                                    </a> додав цитату
                                </p>
                                <p class="text-xs text-light-text-tertiary dark:text-dark-text-tertiary truncate">
                                    {{ $quote->book_title ?? 'Без назви' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            
            <!-- Results Info -->
            @if(request('search') || request('rating_filter') || request('activity_filter'))
                <div class="bg-white backdrop-blur-xl rounded-xl p-4 border border-white/20 shadow-2xl dark:bg-gray-900 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-filter text-brand-500"></i>
                            <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                Застосовані фільтри:
                            </span>
                        </div>
                        <a href="{{ route('users.index') }}" 
                           class="text-sm text-light-text-tertiary dark:text-dark-text-tertiary hover:text-brand-500 transition-colors duration-200">
                            <i class="fas fa-times mr-1"></i>Очистити всі
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @if(request('search'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-brand-100 dark:bg-brand-900 text-brand-800 dark:text-brand-200">
                                Пошук: "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('rating_filter'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-accent-100 dark:bg-accent-900 text-accent-800 dark:text-accent-200">
                                Рейтинг: {{ ucfirst(str_replace('_', ' ', request('rating_filter'))) }}
                            </span>
                        @endif
                        @if(request('activity_filter'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                Активність: {{ ucfirst(str_replace('_', ' ', request('activity_filter'))) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Users Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-2">
                @forelse($users as $index => $user)
                    <a href="{{ route('users.public.profile', $user->username) }}" 
                       class="group relative rounded-xl hover:shadow-lg transition-all duration-200 border border-light-border dark:border-dark-border overflow-hidden">
                        
                        <!-- Background Blurred Avatar -->
                        <div class="absolute inset-0  bg-gradient-to-t from-blue-500 to-cyan-500">
                            <img src="{{ $user->avatar_display }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-full h-full object-cover" style="filter: blur(1px); opacity: 40%;">
                        </div>

                        <!-- Dark Overlay for Better Text Readability -->
                        <div class="absolute inset-0 bg-black/40 dark:bg-black/60"></div>

                        <!-- User Info - Horizontal Layout -->
                        <div class="relative z-10 p-4">
                            <div class="flex items-center space-x-4">
                                <!-- Circular Avatar -->
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $user->avatar_display }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-16 h-16 rounded-full object-cover border-3 border-white dark:border-gray-800 shadow-lg">
                                </div>
                                
                                <!-- User Details -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-white truncate drop-shadow-lg">
                                        {{ $user->name }}
                                    </h3>
                                    <p class="text-sm text-gray-200 dark:text-white truncate drop-shadow-lg">
                                        {{ '@' . $user->username }}
                                    </p>
                                </div>
                                
                                <!-- Rating -->
                                <div class="flex items-center flex-shrink-0">
                                    <div class="flex text-yellow-400 dark:text-white mr-1 drop-shadow-lg">
                                        <i class="fas fa-star text-xs text-yellow-400"></i>
                                    </div>
                                    <span class="text-sm font-medium text-white drop-shadow-lg">
                                        {{ $user->rating_score }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-light-text-tertiary dark:text-dark-text-tertiary">
                            <i class="fas fa-search text-4xl mb-4"></i>
                            <h3 class="text-xl font-semibold mb-2">Користувачів не знайдено</h3>
                            <p class="text-sm">Спробуйте змінити параметри пошуку або фільтри</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-lg border border-light-border dark:border-dark-border">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection