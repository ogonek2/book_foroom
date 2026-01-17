@extends('profile.private.main')

@push('styles')
<style>
    /* Прибираємо стандартну стрілочку з Tailwind для наших select */
    #statusFilter,
    #sortBy {
        background-image: none !important;
        background-position: initial !important;
        background-repeat: initial !important;
        background-size: initial !important;
        -webkit-print-color-adjust: unset !important;
        print-color-adjust: unset !important;
    }
</style>
@endpush

@section('profile-content')
    <div class="flex-1">
        <!-- Discussions Header -->
        <div class="mb-8">
            <div class="flex flex-col mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Мої обговорення</h2>
                    <p class="text-gray-600 dark:text-gray-400">Створені теми для обговорення</p>
                </div>
                <!-- Create Discussion Button -->
                <button onclick="createNewDiscussion()"
                    class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 mt-4">
                    <i class="fa-solid fa-plus"></i>
                    Створити тему
                </button>
            </div>

            <!-- Discussions Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 lg:gap-4">
                @php
                    $discussions = $user->discussions();
                    $totalDiscussions = $discussions->count();
                    $activeDiscussions = $discussions->where('status', 'active')->count();
                    $totalReplies = $discussions->withCount('replies')->get()->sum('replies_count');
                    $totalLikes = $discussions->withCount('likes')->get()->sum('likes_count');
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-1">{{ $totalDiscussions }}</div>
                    <div class="text-sm text-gray-900 dark:text-white">Всього тем</div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-green-400 mb-1" id="activeDiscussionsCount">{{ $activeDiscussions }}</div>
                    <div class="text-sm text-gray-900 dark:text-white">Активних</div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-1">{{ $totalReplies }}</div>
                    <div class="text-sm text-gray-900 dark:text-white">Відповідей</div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-orange-400 mb-1">{{ $totalLikes }}</div>
                    <div class="text-sm text-gray-900 dark:text-white">Лайків отримано</div>
                </div>
            </div>
        </div>

        <!-- Discussions Content -->
        <div>
            <div class="flex flex-col mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Список обговорень</h3>
                <!-- Filter and Sort -->
                <div class="flex items-center space-x-2 mt-2">
                    <div class="relative">
                        <select id="statusFilter" onchange="filterByStatus(this.value)" 
                                class="px-3 py-2 pr-8 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none cursor-pointer">
                            <option value="" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Всі статуси</option>
                            <option value="active" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Активні</option>
                            <option value="closed" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Закриті</option>
                            <option value="pinned" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Закріплені</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <select id="sortBy" onchange="sortDiscussions(this.value)" 
                                class="px-3 py-2 pr-8 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none cursor-pointer">
                            <option value="created_at" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Дата створення</option>
                            <option value="updated_at" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Остання активність</option>
                            <option value="replies_count" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Кількість відповідей</option>
                            <option value="likes_count" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Кількість лайків</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
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
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 transition-all duration-200 group" 
                             data-status="{{ $discussion->status }}"
                             data-discussion-id="{{ $discussion->id }}"
                             data-discussion-slug="{{ $discussion->slug }}">
                            <div class="flex items-start space-x-4">
                                <!-- Discussion Content -->
                                <div class="flex-1 min-w-0">
                                    <!-- Actions -->
                                        <div class="flex items-center space-x-2 mb-2">
                                            <button onclick="deleteDiscussion({{ $discussion->id }})" 
                                                    class="w-10 h-10 bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-colors">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                            <a href="{{ route('discussions.edit', $discussion->slug) }}"
                                                    class="p-2 text-yellow-500 underline transition-colors">
                                                Редагувати
                                            </a>
                                            <!-- Close/Open Toggle -->
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       onchange="toggleDiscussionStatus({{ $discussion->id }}, this.checked, event)"
                                                       {{ $discussion->is_closed ? 'checked' : '' }}
                                                       class="sr-only peer">
                                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                                <span class="ml-2 text-xs text-gray-400 dark:text-gray-500">
                                                    {{ $discussion->is_closed ? 'Закрите' : 'Відкрите' }}
                                                </span>
                                            </label>
                                        </div>
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 break-words" 
                                                style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                                <a href="{{ route('discussions.show', $discussion->slug) }}" 
                                                   class="hover:text-orange-500 transition-colors">
                                                    {{ $discussion->title }}
                                                </a>
                                            </h4>
                                            
                                            <!-- Status Badge -->
                                            <div class="flex items-center space-x-2 mb-2">
                                                @if($discussion->category)
                                                    <span class="px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-full">
                                                        {{ $discussion->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center space-x-4">
                                            <span>{{ $discussion->created_at->format('d.m.Y H:i') }}</span>
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
                                    @if($discussion->updated_at != $discussion->created_at)
                                        <span class="text-sm text-gray-400"><i class="fa-solid fa-pen"></i> оновлено {{ $discussion->updated_at->format('d.m.Y H:i') }}</span>
                                    @endif
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
                    {{ $discussions->appends(array_merge(request()->query(), ['tab' => 'discussions']))->links() }}
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

            async function deleteDiscussion(discussionId) {
                const confirmed = await confirm('Ви впевнені, що хочете видалити це обговорення? Цю дію неможливо скасувати.', 'Підтвердження', 'warning');
                if (confirmed) {
                    // TODO: Implement delete discussion
                    console.log('Delete discussion:', discussionId);
                }
            }

            async function toggleDiscussionStatus(discussionId, isClosed, event) {
                const checkbox = event ? event.target : null;
                try {
                    // Find discussion element to get slug
                    const discussionElement = checkbox ? checkbox.closest('[data-discussion-slug]') : null;
                    const discussionSlug = discussionElement ? discussionElement.dataset.discussionSlug : null;
                    
                    if (!discussionSlug) {
                        console.error('Discussion slug not found');
                        if (checkbox) checkbox.checked = !isClosed;
                        alert('Помилка: не вдалося знайти обговорення', 'Помилка', 'error');
                        return;
                    }
                    
                    const url = isClosed 
                        ? `/discussions/${discussionSlug}/close`
                        : `/discussions/${discussionSlug}/reopen`;
                    
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Update the label text
                        const label = checkbox ? checkbox.closest('label').querySelector('span') : null;
                        if (label) {
                            label.textContent = isClosed ? 'Закрите' : 'Відкрите';
                        }
                        
                        // Update active discussions count
                        const activeCountElement = document.getElementById('activeDiscussionsCount');
                        if (activeCountElement) {
                            let currentCount = parseInt(activeCountElement.textContent) || 0;
                            if (isClosed) {
                                // Closing discussion - decrease count
                                currentCount = Math.max(0, currentCount - 1);
                            } else {
                                // Opening discussion - increase count
                                currentCount = currentCount + 1;
                            }
                            activeCountElement.textContent = currentCount;
                        }
                    } else {
                        // Revert checkbox on error
                        if (checkbox) checkbox.checked = !isClosed;
                        alert('Помилка при зміні статусу обговорення', 'Помилка', 'error');
                    }
                } catch (error) {
                    console.error('Error toggling discussion status:', error);
                    // Revert checkbox on error
                    if (checkbox) checkbox.checked = !isClosed;
                    alert('Помилка при зміні статусу обговорення', 'Помилка', 'error');
                }
            }
        </script>
    @endpush
@endsection
