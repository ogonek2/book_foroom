@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Discussions Header -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Мої обговорення</h2>
                    <p class="text-gray-600 dark:text-gray-400">Створені теми для обговорення</p>
                </div>
                <!-- Create Discussion Button -->
                <button onclick="createNewDiscussion()" 
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg font-medium transition-all duration-300 backdrop-blur-sm">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Створити тему
                </button>
            </div>

            <!-- Discussions Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $discussions = $user->discussions();
                    $totalDiscussions = $discussions->count();
                    $activeDiscussions = $discussions->where('status', 'active')->count();
                    $totalReplies = $discussions->withCount('replies')->get()->sum('replies_count');
                    $totalLikes = $discussions->withCount('likes')->get()->sum('likes_count');
                @endphp

                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-1">{{ $totalDiscussions }}</div>
                    <div class="text-sm text-gray-300">Всього тем</div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-green-400 mb-1">{{ $activeDiscussions }}</div>
                    <div class="text-sm text-gray-300">Активних</div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-1">{{ $totalReplies }}</div>
                    <div class="text-sm text-gray-300">Відповідей</div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-orange-400 mb-1">{{ $totalLikes }}</div>
                    <div class="text-sm text-gray-300">Лайків отримано</div>
                </div>
            </div>
        </div>

        <!-- Discussions Content -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Список обговорень</h3>
                <!-- Filter and Sort -->
                <div class="flex items-center space-x-4">
                    <select id="statusFilter" onchange="filterByStatus(this.value)" 
                            class="px-3 py-2 bg-white/20 text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Всі статуси</option>
                        <option value="active">Активні</option>
                        <option value="closed">Закриті</option>
                        <option value="pinned">Закріплені</option>
                    </select>
                    
                    <select id="sortBy" onchange="sortDiscussions(this.value)" 
                            class="px-3 py-2 bg-white/20 text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="created_at">Дата створення</option>
                        <option value="updated_at">Остання активність</option>
                        <option value="replies_count">Кількість відповідей</option>
                        <option value="likes_count">Кількість лайків</option>
                    </select>
                </div>
            </div>

            <!-- Discussions List -->
            <div id="discussionsList" class="space-y-4">
                @php
                    $discussions = $user->discussions()
                        ->withCount(['replies', 'likes'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                @endphp

                @if($discussions->count() > 0)
                    @foreach($discussions as $discussion)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all duration-200 group" 
                             data-status="{{ $discussion->status }}">
                            <div class="flex items-start space-x-4">
                                <!-- Discussion Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-pink-500 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Discussion Content -->
                                <div class="flex-1 min-w-0">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                <a href="{{ route('discussions.show', $discussion->slug) }}" 
                                                   class="hover:text-orange-500 transition-colors">
                                                    {{ $discussion->title }}
                                                </a>
                                            </h4>
                                            
                                            <!-- Status Badge -->
                                            <div class="flex items-center space-x-2 mb-2">
                                                @php
                                                    $statusColors = [
                                                        'active' => 'bg-green-500',
                                                        'closed' => 'bg-red-500',
                                                        'pinned' => 'bg-blue-500',
                                                    ];
                                                    $statusLabels = [
                                                        'active' => 'Активне',
                                                        'closed' => 'Закрите',
                                                        'pinned' => 'Закріплене',
                                                    ];
                                                @endphp
                                                <span class="px-2 py-1 text-xs font-medium text-white rounded-full {{ $statusColors[$discussion->status] }}">
                                                    {{ $statusLabels[$discussion->status] }}
                                                </span>
                                                
                                                @if($discussion->category)
                                                    <span class="px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-full">
                                                        {{ $discussion->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="editDiscussion({{ $discussion->id }})" 
                                                    class="p-2 text-gray-400 hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button onclick="deleteDiscussion({{ $discussion->id }})" 
                                                    class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Discussion Preview -->
                                    <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 mb-4">
                                        {!! Str::limit($discussion->content, 200) !!}
                                        @if(strlen($discussion->content) > 200)
                                            <a href="{{ route('discussions.show', $discussion->slug) }}" 
                                               class="text-orange-500 hover:text-orange-600 font-medium">
                                                Читати повністю →
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center space-x-4">
                                            <span>{{ $discussion->created_at->format('d.m.Y H:i') }}</span>
                                            @if($discussion->updated_at != $discussion->created_at)
                                                <span>(оновлено {{ $discussion->updated_at->format('d.m.Y H:i') }})</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                                <span>{{ $discussion->replies_count }}</span>
                                            </span>
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>{{ $discussion->likes_count }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Немає обговорень</h3>
                        <p class="text-gray-500 mb-6">Створіть свою першу тему для обговорення</p>
                        <a href="{{ route('discussions.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Створити тему
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($discussions->hasPages())
                <div class="mt-6">
                    {{ $discussions->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function createNewDiscussion() {
                window.location.href = '{{ route("discussions.create") }}';
            }

            function filterByStatus(status) {
                const discussions = document.querySelectorAll('#discussionsList > div[data-status]');
                discussions.forEach(discussion => {
                    if (!status || discussion.dataset.status === status) {
                        discussion.style.display = 'block';
                    } else {
                        discussion.style.display = 'none';
                    }
                });
            }

            function sortDiscussions(sortBy) {
                // TODO: Implement client-side sorting or AJAX request
                console.log('Sort discussions by:', sortBy);
            }

            function editDiscussion(discussionId) {
                // TODO: Implement edit discussion modal
                console.log('Edit discussion:', discussionId);
            }

            function deleteDiscussion(discussionId) {
                if (confirm('Ви впевнені, що хочете видалити це обговорення? Цю дію неможливо скасувати.')) {
                    // TODO: Implement delete discussion
                    console.log('Delete discussion:', discussionId);
                }
            }
        </script>
    @endpush
@endsection
