@extends('users.public.main')

@section('title', $user->name . ' - Обговорення')

@section('profile-content')
    <div class="flex-1">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Обговорення користувача</h2>

            @if ($discussions->count() > 0)
                <div class="space-y-6">
                    @foreach ($discussions as $discussion)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <a href="{{ route('discussions.show', $discussion->slug) }}"
                                        class="text-xl font-semibold text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-200 transition-colors break-words" 
                                        style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                        {{ $discussion->title }}
                                    </a>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">
                                        {{ $discussion->topic->name ?? 'Загальне обговорення' }}</p>
                                </div>
                            </div>

                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4 break-words" 
                                 style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                {{ Str::limit(strip_tags($discussion->content), 200) }}
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center space-x-4">
                                    <span class="text-gray-500 dark:text-gray-500">
                                        {{ $discussion->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if ($discussion->status === 'active')
                                        <span
                                            class="bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-xs">Активне</span>
                                    @elseif($discussion->status === 'closed')
                                        <span
                                            class="bg-red-500/20 text-red-400 px-2 py-1 rounded-full text-xs">Закрите</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 mt-2">
                                    <div class="flex items-center space-x-1 text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        <span class="text-sm">{{ $discussion->replies_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1 text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                        <span class="text-sm">{{ $discussion->likes_count ?? 0 }}</span>
                                    </div>
                                </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $discussions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Ще немає обговорень</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не створив жодного обговорення</p>
                </div>
            @endif
        </div>
    </div>
@endsection
