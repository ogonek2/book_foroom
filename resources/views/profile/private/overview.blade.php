@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Rating Statistics -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Статистика оцінок</h2>
            </div>

            @if ($stats['total_rated_books'] > 0)
                <div class="flex items-center justify-center">
                    <!-- Vertical Rating Bars -->
                    <div class="flex items-end space-x-1">
                        @for ($i = 1; $i <= 10; $i++)
                            @php
                                $percentage =
                                    $stats['total_rated_books'] > 0
                                        ? ($ratingStats[$i] / $stats['total_rated_books']) * 100
                                        : 0;

                                // Определяем цвет в зависимости от оценки
                                $barColor = match (true) {
                                    $i >= 9 => 'from-green-500 to-emerald-500', // 9-10: зеленый
                                    $i >= 7 => 'from-blue-500 to-cyan-500', // 7-8: синий
                                    $i >= 5 => 'from-yellow-500 to-orange-500', // 5-6: желтый
                                    $i >= 3 => 'from-orange-500 to-red-500', // 3-4: оранжевый
                                    default => 'from-red-500 to-pink-500', // 1-2: красный
                                };
                            @endphp
                            <div class="flex flex-col items-center space-y-1">
                                <!-- Vertical Progress Bar -->
                                <div class="relative w-6 h-32 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="absolute bottom-0 w-full bg-gradient-to-t {{ $barColor }} rounded-full transition-all duration-300"
                                        style="height: {{ $percentage }}%">
                                    </div>
                                </div>

                                <!-- Rating Label -->
                                <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $i }}</div>

                                <!-- Count Label -->
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $ratingStats[$i] }}</div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="text-center mt-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Усього оцінено: {{ $stats['total_rated_books'] }}
                        книг</span>
                    @if ($stats['average_rating'])
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-4">Середня оцінка:
                            {{ number_format($stats['average_rating'], 1) }} / 10</span>
                    @endif
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-300 mb-2">Ще немає оцінок</h3>
                    <p class="text-gray-500 mb-6">Оцініть книги, щоб побачити статистику</p>
                    <a href="{{ route('books.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Оцінити книги
                    </a>
                </div>
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Остання активність</h2>
            </div>

            @if (isset($recentActivity) && $recentActivity->count() > 0)
                <div class="space-y-4">
                     @foreach ($recentActivity as $activity)
                         @php
                             $activityUrl = match($activity->type) {
                                 'rating' => '/books',
                                 'review' => '/books',
                                 'discussion' => '/discussions',
                                 default => '#'
                             };
                         @endphp
                         <a href="{{ $activityUrl }}"
                            class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-200 cursor-pointer group">
                             <div class="flex-shrink-0">
                                 @if ($activity->type === 'rating')
                                     <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                                         <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                             <path
                                                 d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                         </svg>
                                     </div>
                                 @elseif($activity->type === 'review')
                                     <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                                         <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                             <path fill-rule="evenodd"
                                                 d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"
                                                 clip-rule="evenodd" />
                                         </svg>
                                     </div>
                                 @else
                                     <div class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                                         <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                             <path fill-rule="evenodd"
                                                 d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                                 clip-rule="evenodd" />
                                         </svg>
                                     </div>
                                 @endif
                             </div>

                             <div class="flex-1 min-w-0">
                                 <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                     {{ $activity->description }}
                                 </p>
                                 <p class="text-xs text-gray-500 dark:text-gray-400">
                                     {{ $activity->created_at->diffForHumans() }}
                                 </p>
                             </div>

                             <!-- Arrow icon -->
                             <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                 <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                 </svg>
                             </div>
                         </a>
                     @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-300 mb-2">Немає активності</h3>
                    <p class="text-gray-500 mb-6">Ваша активність з'явиться тут</p>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Швидкі дії</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('books.index') }}"
                    class="flex items-center space-x-3 p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-orange-500/20 rounded-full flex items-center justify-center group-hover:bg-orange-500/30 transition-colors">
                            <i class="fa-solid fa-star text-orange-400"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Оцінити книги</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Додати оцінки</p>
                    </div>
                </a>

                <a href="{{ route('libraries.create') }}"
                    class="flex items-center space-x-3 p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Створити добірку</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Нова колекція</p>
                    </div>
                </a>

                <a href="{{ route('discussions.index') }}"
                    class="flex items-center space-x-3 p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Почати обговорення</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Створити тему</p>
                    </div>
                </a>

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center space-x-3 p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Налаштування</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Редагувати профіль</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openStatsModal() {
                // TODO: Implement stats configuration modal
                console.log('Open stats modal');
            }

            function viewAllActivity() {
                // TODO: Implement view all activity page
                console.log('View all activity');
            }

            function editActivity(activityId) {
                // TODO: Implement edit activity functionality
                console.log('Edit activity:', activityId);
            }
        </script>
    @endpush
@endsection
