<x-filament-panels::page>
    @php
        $totalHashtags = \App\Models\Hashtag::count();
        $totalReviewsWithHashtags = \App\Models\Review::whereHas('hashtags')->count();
        $totalDiscussionsWithHashtags = \App\Models\Discussion::whereHas('hashtags')->count();
        $totalUsage = \App\Models\Hashtag::sum('usage_count');
        
        $topHashtags = \App\Models\Hashtag::orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();
    @endphp

    <div class="space-y-6">
        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Всього хештегів</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalHashtags }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Рецензій з хештегами</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalReviewsWithHashtags }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Обговорень з хештегами</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalDiscussionsWithHashtags }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Всього використань</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalUsage }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Топ хештегів -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Топ-10 найпопулярніших хештегів</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($topHashtags as $index => $hashtag)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">#{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">#{{ $hashtag->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $hashtag->slug }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Рецензії</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $hashtag->reviews()->count() }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Обговорення</p>
                                    <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $hashtag->discussions()->count() }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Всього</p>
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $hashtag->usage_count }}</p>
                                </div>
                                <a href="{{ \App\Filament\Resources\HashtagResource::getUrl('view', ['record' => $hashtag]) }}" 
                                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                                    Деталі
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Хештегів поки немає</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
