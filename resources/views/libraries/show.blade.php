@extends('layouts.app')

@section('title', $library->name . ' - Добірка')

@section('main')
<div id="library-show-app" class="max-w-7xl mx-auto space-y-8">
    <!-- Breadcrumb Navigation -->
    <nav class="text-sm text-slate-500 dark:text-slate-400">
        <ol class="flex flex-wrap gap-2">
            <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Головна</a></li>
            <li>/</li>
            <li><a href="{{ route('libraries.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Добірки</a></li>
            <li>/</li>
            <li class="font-semibold text-slate-900 dark:text-white">{{ $library->name }}</li>
        </ol>
    </nav>

    <div class="w-full flex items-start gap-8">
         <!-- Sticky Author Profile Sidebar -->
         <aside v-pre class="w-full max-w-[280px] hidden lg:block lg:sticky top-4 space-y-6">
            <!-- Author Profile Card -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/30 p-6">
                <!-- Author Avatar and Info -->
                <div class="text-center mb-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-slate-200 dark:border-slate-700">
                        @if($library->user->avatar)
                            <img src="{{ $library->user->avatar }}" alt="{{ $library->user->name }}" class="w-20 h-20 rounded-full" loading="lazy" decoding="async" width="80" height="80">
                        @else
                            <span class="text-white font-bold text-2xl">{{ $library->user->name[0] }}</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                        {{ $library->user->name }}
                    </h3>
                    <a href="{{ route('users.public.profile', $library->user->username) }}"
                        class="text-slate-500 dark:text-slate-400 text-sm hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        {{ $library->user->username }}
                    </a>
                </div>

                <!-- Author Stats -->
                <div class="space-y-3 border-t border-slate-200 dark:border-slate-700 pt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Добірок</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $library->user->libraries()->where('is_private', false)->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Рецензій</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $library->user->reviews()->whereNull('parent_id')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Обговорень</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $library->user->discussions()->count() }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 mt-6">
                    <a href="{{ route('users.public.library', $library->user->username) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        <i class="fas fa-book mr-2"></i> Бібліотека {{ $library->user->name }}
                    </a>
                    <a href="{{ route('users.public.reviews', $library->user->username) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        <i class="fas fa-comments mr-2"></i> Рецензії {{ $library->user->name }}
                    </a>
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <div class="w-full flex-1 space-y-4">
            <!-- Library Header Card -->
            <div>
                <!-- Library Header -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('users.public.profile', $library->user->username) }}" class="flex items-center gap-2">
                                @if($library->user->avatar)
                                    <img src="{{ $library->user->avatar }}" alt="{{ $library->user->name }}" class="w-10 h-10 rounded-full" loading="lazy" decoding="async" width="40" height="40">
                                @else
                                    <span class="text-white font-bold text-2xl">{{ $library->user->name[0] }}</span>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-slate-900 dark:text-white">{{ $library->user->name }}</span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm">{{ $library->user->username }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Library Title -->
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">{{ $library->name }}</h1>
                    
                    <!-- Description -->
                    @if($library->description)
                        <p class="text-lg text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">{{ $library->description }}</p>
                    @endif
                    
                    <!-- Stats -->
                    <div class="flex items-center flex-wrap gap-4 mb-4">
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $library->likes()->count() }} лайків</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $books->total() }} книг</span>
                        </div>
                        @if($library->is_private)
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Приватна</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Library Actions -->
                <div class="px-6 py-4 border-t border-white/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button @click="toggleLike" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 text-sm font-medium"
                                :class="isLiked ? 'text-red-500' : 'text-slate-600 dark:text-slate-400'"
                                :style="isLiked ? '' : 'hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-slate-200'">
                            <svg class="w-4 h-4" :fill="isLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span v-text="likesCount"></span>
                        </button>
                        
                        @auth
                        <button @click="toggleSave" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 text-sm font-medium"
                                :class="isSaved ? 'text-blue-500' : 'text-slate-600 dark:text-slate-400'"
                                :style="isSaved ? '' : 'hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-slate-200'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span v-text="isSaved ? 'Збережено' : 'Зберегти'"></span>
                        </button>
                        @endauth
                        
                        <button @click="shareLibrary" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-slate-200"
                                title="Поділитися добіркою">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                            </svg>
                            <span class="hidden sm:inline">Поділитися</span>
                        </button>
                        
                        @if(auth()->check() && $library->user_id == auth()->id())
                            <div class="flex items-center gap-2">
                                <a href="{{ route('libraries.edit', $library) }}" class="flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-blue-500" title="Редагувати добірку">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Books Section -->
            <div class="lg:py-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Книги в добірці</h2>
                    <span class="text-slate-600 dark:text-slate-400">{{ $books->total() }} {{ $books->total() == 1 ? 'книга' : ($books->total() < 5 ? 'книги' : 'книг') }}</span>
                </div>

                @if($books->count() > 0)
                    <!-- Books Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                        @foreach($books as $book)
                            <book-card
                                :book='@json($book)'
                                :user-libraries='@json([])'
                                :is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                                @notification="handleNotification"
                            ></book-card>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $books->links('vendor.pagination.custom') }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/30">
                        <div class="text-slate-400 dark:text-slate-500 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Добірка порожня</h3>
                        <p class="text-slate-600 dark:text-slate-400">В цій добірці ще немає книг</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Vue приложение для страницы добірки
document.addEventListener('DOMContentLoaded', function() {
    // Wait for axios and shareContent to be available
    const waitForDependencies = () => {
        return new Promise((resolve) => {
            const checkInterval = setInterval(() => {
                if (window.axios && window.shareContent) {
                    clearInterval(checkInterval);
                    resolve({ axios: window.axios, shareContent: window.shareContent });
                }
            }, 100);
            
            // Timeout after 5 seconds
            setTimeout(() => {
                clearInterval(checkInterval);
                resolve({ axios: window.axios || null, shareContent: window.shareContent || null });
            }, 5000);
        });
    };

    waitForDependencies().then((deps) => {
        if (!deps.axios) {
            console.error('Axios is not available');
            return;
        }

        const appElement = document.getElementById('library-show-app');
        if (!appElement) {
            console.error('Element #library-show-app not found');
            return;
        }

        const libraryShowApp = new Vue({
            el: '#library-show-app',
            data: {
                isLiked: {{ $library->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
                isSaved: {{ $isSaved ? 'true' : 'false' }},
                likesCount: {{ $library->likes()->count() }},
                libraryId: {{ $library->id }},
                librarySlug: '{{ $library->slug }}',
                @auth
                user: {
                    username: '{{ auth()->user()->username }}'
                }
                @endauth
            },
            methods: {
                async toggleLike() {
                    if (!{{ auth()->check() ? 'true' : 'false' }}) {
                        return;
                    }

                    try {
                        const response = await deps.axios.post(`/libraries/${this.librarySlug}/like`);
                        if (response.data.success) {
                            this.isLiked = response.data.is_liked;
                            this.likesCount = response.data.likes_count;
                        }
                    } catch (error) {
                        console.error('Error toggling like:', error);
                    }
                },
                async toggleSave() {
                    if (!{{ auth()->check() ? 'true' : 'false' }}) {
                        return;
                    }

                    try {
                        const response = await deps.axios.post(`/libraries/${this.librarySlug}/save`, {}, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.data.success) {
                            this.isSaved = response.data.is_saved;
                        }
                    } catch (error) {
                        console.error('Error toggling save:', error);
                    }
                },
                async shareLibrary() {
                    try {
                        // Use shareHelper from app.js if available
                        if (deps.shareContent) {
                            await deps.shareContent({
                                title: '{{ $library->name }}',
                                text: '{{ $library->description ?: "Добірка від " . $library->user->name }}',
                                url: window.location.href
                            });
                            return;
                        }

                        // Fallback to native share or clipboard
                        if (navigator.share) {
                            await navigator.share({
                                title: '{{ $library->name }}',
                                text: '{{ $library->description ?: "Добірка від " . $library->user->name }}',
                                url: window.location.href
                            });
                        } else {
                            await navigator.clipboard.writeText(window.location.href);
                        }
                    } catch (error) {
                        console.error('Error sharing:', error);
                        // Silent fail - no notification
                    }
                },
                handleNotification(notification) {
                    // Уведомления отключены
                }
            }
        });
    });
});
</script>
@endpush
@endsection
