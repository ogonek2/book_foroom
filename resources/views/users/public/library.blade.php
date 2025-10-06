@extends('users.public.main')

@section('title', $user->name . ' - Бібліотека')

@section('profile-content')
    <div class="flex-1">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Бібліотека користувача</h2>

            <!-- Library Stats -->
            @php
                $readingStats = [
                    'read_count' => $user->readingStatuses()->where('status', 'read')->count(),
                    'reading_count' => $user->readingStatuses()->where('status', 'reading')->count(),
                    'want_to_read_count' => $user->readingStatuses()->where('status', 'want_to_read')->count(),
                    'average_rating' => $user
                        ->readingStatuses()
                        ->where('status', 'read')
                        ->whereNotNull('rating')
                        ->avg('rating'),
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readingStats['read_count'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Прочитано</div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readingStats['reading_count'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Читає</div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readingStats['want_to_read_count'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Планує</div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $readingStats['average_rating'] ? number_format($readingStats['average_rating'], 1) : '0.0' }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Середня оцінка</div>
                </div>
            </div>

            <!-- Books Grid -->
            @php
                $allBooks = $user->readingStatuses()->with('book')->orderBy('created_at', 'desc')->paginate(20);
            @endphp

            @if ($allBooks->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach ($allBooks as $readingStatus)
                        <div class="group cursor-pointer">
                            <div
                                class="aspect-[3/4] bg-white/5 rounded-xl overflow-hidden mb-3 group-hover:bg-white/10 transition-all">
                                @if ($readingStatus->book->cover_image)
                                    <img src="{{ $readingStatus->book->cover_image }}"
                                        alt="{{ $readingStatus->book->title }}" class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Status Badge -->
                                <div class="absolute top-2 left-2">
                                    @if ($readingStatus->status === 'read')
                                        <span
                                            class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Прочитано</span>
                                    @elseif($readingStatus->status === 'reading')
                                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Читає</span>
                                    @elseif($readingStatus->status === 'want_to_read')
                                        <span class="bg-purple-500 text-white text-xs px-2 py-1 rounded-full">Планує</span>
                                    @endif
                                </div>

                                <!-- Rating -->
                                @if ($readingStatus->rating)
                                    <div
                                        class="absolute bottom-2 right-2 bg-black/50 backdrop-blur-sm rounded-full px-2 py-1">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                            <span class="text-gray-900 dark:text-white text-xs font-medium">{{ $readingStatus->rating }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <h3
                                class="text-gray-900 dark:text-white font-medium text-sm text-center group-hover:text-purple-600 dark:group-hover:text-purple-200 transition-colors">
                                {{ Str::limit($readingStatus->book->title, 25) }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs text-center mt-1">
                                {{ Str::limit($readingStatus->book->author, 20) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $allBooks->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Бібліотека порожня</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не додав книги до своєї бібліотеки</p>
                </div>
            @endif
        </div>
    </div>
@endsection
