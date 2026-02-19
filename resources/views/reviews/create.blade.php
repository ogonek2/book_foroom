@extends('layouts.app')

@section('title', (isset($review) ? 'Редагувати рецензію на ' : 'Написати рецензію на ') . $book->title . ' - Книжковий форум')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
    <style>
        /* Quill editor styles */
        #quill-editor-review {
            min-height: 400px;
        }

        .ql-container {
            min-height: 400px;
            font-size: 1rem;
            line-height: 1.7;
            border: none;
            border-radius: 0.75rem;
        }

        .ql-editor {
            min-height: 400px;
            padding: 1.5rem;
            color: rgb(15 23 42);
        }

        .dark .ql-editor {
            color: rgb(241 245 249);
        }

        .ql-toolbar {
            border: none;
            border-bottom: 1px solid rgb(226 232 240);
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 0.75rem;
            background: rgb(248 250 252);
        }

        .dark .ql-toolbar {
            border-bottom-color: rgb(51 65 85);
            background: rgb(30 41 59);
        }

        .ql-snow .ql-stroke {
            stroke: rgb(100 116 139);
        }

        .dark .ql-snow .ql-stroke {
            stroke: rgb(148 163 184);
        }

        .ql-snow .ql-fill {
            fill: rgb(100 116 139);
        }

        .dark .ql-snow .ql-fill {
            fill: rgb(148 163 184);
        }

        .ql-snow .ql-picker-label {
            color: rgb(100 116 139);
        }

        .dark .ql-snow .ql-picker-label {
            color: rgb(148 163 184);
        }

        .ql-snow .ql-picker-options {
            background: white;
            border: 1px solid rgb(226 232 240);
        }

        .dark .ql-snow .ql-picker-options {
            background: rgb(30 41 59);
            border-color: rgb(51 65 85);
        }
    </style>
@endpush

@section('main')
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Breadcrumb Navigation -->
        <nav class="text-sm text-slate-500 dark:text-slate-400">
            <ol class="flex flex-wrap gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Головна</a></li>
                <li>/</li>
                <li><a href="{{ route('books.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Книги</a>
                </li>
                <li>/</li>
                <li><a href="{{ route('books.show', $book) }}"
                        class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ $book->title }}</a></li>
                <li>/</li>
                <li class="font-semibold text-slate-900 dark:text-white">
                    {{ isset($review) ? 'Редагувати рецензію' : 'Написати рецензію' }}</li>
            </ol>
        </nav>

        <div class="w-full flex flex-col lg:flex-row gap-4 items-start">
            <!-- Sticky Book Sidebar -->
            <aside class="w-full lg:max-w-[220px] lg:block lg:sticky top-4 space-y-6">
                <div class="flex flex-row items-center lg:flex-col lg:items-start gap-x-4">
                    <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}"
                        alt="{{ $book->title }}" class="w-full max-w-[120px] lg:max-w-full object-cover rounded-lg mb-4" style="aspect-ratio: 2 / 3;">
                    <div>
                        <p class="text-xs uppercase text-slate-500 dark:text-slate-400 mb-1">Книга</p>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $book->title }}</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                            @if($authorModel)
                                <a href="{{ route('authors.show', $authorModel->slug) }}"
                                    class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $authorModel->full_name ?? $authorModel->short_name ?? $book->author }}
                                </a>
                            @else
                                {{ $book->author }}
                            @endif
                        </p>
                        <!-- Book Rating -->
                        <div class="flex items-center gap-2">
                            <i class="fas fa-star text-yellow-400"></i>
                            <span
                                class="text-lg font-bold text-slate-900 dark:text-white">{{ $book->display_rating }}/10</span>
                            @if($book->reviews_count > 0)
                                <span class="text-sm text-slate-500 dark:text-slate-400">({{ $book->reviews_count }})</span>
                            @endif
                        </div>
                    </div>
                </div>
            </aside>
            <!-- Review Form Section -->
            <div class="w-full flex-1">
                <div class=" overflow-hidden">
                    <!-- Form Header -->
                    <h2 class="text-2xl py-4 font-bold text-slate-900 dark:text-white">
                            {{ isset($review) ? 'Редагувати рецензію' : 'Написати рецензію' }}</h2>
                    <div>

                        @if(isset($lastReviewInfo) && $lastReviewInfo && isset($lastReviewInfo['remaining_seconds']) && $lastReviewInfo['remaining_seconds'] > 0)
                            <!-- Cooldown Timer -->
                            <div id="cooldown-timer-app" class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-8 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <i class="fas fa-clock text-orange-500 dark:text-orange-400 text-5xl"></i>
                                    <h3 class="text-xl font-bold text-orange-700 dark:text-orange-300">
                                        Ви вже написали рецензію на цю книгу
                                    </h3>
                                    <p class="text-sm text-orange-600 dark:text-orange-400">
                                        Ви можете написати наступну рецензію через:
                                    </p>
                                    <div class="text-3xl font-bold text-orange-700 dark:text-orange-300" id="cooldown-display">
                                        <span id="cooldown-hours">0</span> год. 
                                        <span id="cooldown-minutes">0</span> хв. 
                                        <span id="cooldown-seconds">0</span> сек.
                                    </div>
                                    <a href="{{ route('books.show', $book) }}" 
                                       class="mt-4 inline-flex items-center gap-2 px-6 py-3 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl font-semibold transition-colors">
                                        <i class="fas fa-arrow-left"></i>
                                        Повернутися до книги
                                    </a>
                                </div>
                            </div>
                            
                            @push('scripts')
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const lastReviewInfo = @json($lastReviewInfo);
                                        let remainingSeconds = parseInt(lastReviewInfo.remaining_seconds) || 0;
                                        
                                        function updateTimer() {
                                            if (remainingSeconds <= 0) {
                                                // Cooldown истек, перезагружаем страницу
                                                window.location.reload();
                                                return;
                                            }
                                            
                                            const hours = Math.floor(remainingSeconds / 3600);
                                            const minutes = Math.floor((remainingSeconds % 3600) / 60);
                                            const seconds = remainingSeconds % 60;
                                            
                                            const hoursEl = document.getElementById('cooldown-hours');
                                            const minutesEl = document.getElementById('cooldown-minutes');
                                            const secondsEl = document.getElementById('cooldown-seconds');
                                            
                                            if (hoursEl) hoursEl.textContent = hours;
                                            if (minutesEl) minutesEl.textContent = minutes;
                                            if (secondsEl) secondsEl.textContent = seconds;
                                            
                                            remainingSeconds--;
                                        }
                                        
                                        // Обновляем таймер каждую секунду
                                        updateTimer();
                                        const timerInterval = setInterval(updateTimer, 1000);
                                        
                                        // Очищаем интервал при размонтировании
                                        window.addEventListener('beforeunload', function() {
                                            clearInterval(timerInterval);
                                        });
                                    });
                                </script>
                            @endpush
                        @else
                            <!-- Review Form -->
                            <div id="review-form-app">
                            <form id="review-form" method="POST"
                                action="{{ isset($review) ? route('books.reviews.update', [$book, $review]) : route('books.reviews.store', $book) }}">
                                @csrf
                                @if(isset($review))
                                    @method('PUT')
                                    <input type="hidden" name="_method" value="PUT">
                                @endif

                                <!-- Hidden inputs for Vue-managed values -->
                                <input type="hidden" name="review_type" v-model="reviewType">
                                <input type="hidden" name="opinion_type" v-model="opinionType">
                                <input type="hidden" name="book_type" v-model="bookType">
                                <input type="hidden" name="language" :value="language === 'other' ? otherLanguage : language">
                                <input type="hidden" name="contains_spoiler" :value="containsSpoiler ? '1' : '0'">
                                <input type="hidden" name="rating" v-model="rating">
                                <!-- Rating -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-3">Оцінка
                                        <span class="text-red-500" v-if="actionMode === 'publish'">*</span></label>
                                    <div class="flex gap-2 flex-wrap">
                                        <button type="button" v-for="star in 10" :key="star" @@click="rating = star"
                                            :class="['text-3xl bg-transparent border-none cursor-pointer p-0 transition-all duration-200 hover:scale-110', star <= rating ? 'text-yellow-400 drop-shadow-lg' : 'text-slate-300 dark:text-slate-600']">
                                            ★
                                        </button>
                                    </div>
                                    <div v-if="rating > 0" class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                        Вибрано: <span class="font-semibold text-slate-900 dark:text-white">@{{ rating
                                            }}/10</span>
                                    </div>
                                </div>
                                <!-- Review Type -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Тип
                                        відгуку</label>
                                    <select id="review_type"
                                        class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        v-model="reviewType">
                                        <option value="review">Рецензія</option>
                                        <option value="opinion">Відгук</option>
                                    </select>
                                </div>

                                <!-- Content -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                                        Ваша думка <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="flex justify-between items-center my-3 text-xs text-slate-500 dark:text-slate-400">
                                        <span>
                                            Мінімум: <strong class="text-slate-700 dark:text-slate-300">@{{ minContentLength }}</strong> символів
                                        </span>
                                        <span class="font-semibold"
                                            :class="getContentLengthClass() === 'error' ? 'text-red-500 dark:text-red-400' : getContentLengthClass() === 'warning' ? 'text-amber-500 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300'">
                                            @{{ contentLength }} / @{{ maxContentLength }} символів
                                        </span>
                                    </div>
                                    <div id="quill-editor-review"
                                        class="min-h-[400px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                                    </div>
                                    <input type="hidden" name="content" id="review-content" required>
                                </div>

                                <!-- Spoiler Checkbox -->
                                <div class="mb-6">
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" id="contains_spoiler" v-model="containsSpoiler"
                                            @if(isset($review) && $review->contains_spoiler) checked @endif
                                            class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 rounded focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all cursor-pointer">
                                        <span
                                            class="ml-3 text-sm text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Містить
                                            спойлер</span>
                                    </label>
                                </div>

                                <!-- Opinion Type (тільки для відгуків) -->
                                <div class="mb-6" v-if="reviewType === 'opinion'">
                                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-3">Тип
                                        думки</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <button type="button" @@click="opinionType = 'positive'"
                                            :class="['relative overflow-hidden px-3 py-2.5 rounded-xl border-2 text-center transition-all duration-200', 
                                            opinionType === 'positive' 
                                                ? 'border-green-400/60 bg-green-50/80 dark:bg-green-900/30 dark:border-green-600/60 shadow-md' 
                                                : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-green-300 dark:hover:border-green-600/50']">
                                            <div class="text-2xl mb-1 transition-transform duration-200"
                                                :class="opinionType === 'positive' ? 'scale-110' : ''">😊</div>
                                            <div class="text-xs font-medium"
                                                :class="opinionType === 'positive' ? 'text-green-700 dark:text-green-300' : 'text-slate-600 dark:text-slate-400'">
                                                Позитивна</div>
                                        </button>
                                        <button type="button" @@click="opinionType = 'neutral'"
                                            :class="['relative overflow-hidden px-3 py-2.5 rounded-xl border-2 text-center transition-all duration-200', 
                                            opinionType === 'neutral' 
                                                ? 'border-yellow-400/60 bg-yellow-50/80 dark:bg-yellow-900/30 dark:border-yellow-600/60 shadow-md' 
                                                : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-yellow-300 dark:hover:border-yellow-600/50']">
                                            <div class="text-2xl mb-1 transition-transform duration-200"
                                                :class="opinionType === 'neutral' ? 'scale-110' : ''">😐</div>
                                            <div class="text-xs font-medium"
                                                :class="opinionType === 'neutral' ? 'text-yellow-700 dark:text-yellow-300' : 'text-slate-600 dark:text-slate-400'">
                                                Нейтральна</div>
                                        </button>
                                        <button type="button" @@click="opinionType = 'negative'"
                                            :class="['relative overflow-hidden px-3 py-2.5 rounded-xl border-2 text-center transition-all duration-200', 
                                            opinionType === 'negative' 
                                                ? 'border-red-400/60 bg-red-50/80 dark:bg-red-900/30 dark:border-red-600/60 shadow-md' 
                                                : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-red-300 dark:hover:border-red-600/50']">
                                            <div class="text-2xl mb-1 transition-transform duration-200"
                                                :class="opinionType === 'negative' ? 'scale-110' : ''">😞</div>
                                            <div class="text-xs font-medium"
                                                :class="opinionType === 'negative' ? 'text-red-700 dark:text-red-300' : 'text-slate-600 dark:text-slate-400'">
                                                Негативна</div>
                                        </button>
                                    </div>
                                </div>

                                <!-- Book Type -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Тип
                                        книги</label>
                                    <select id="book_type"
                                        class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        v-model="bookType">
                                        <option value="paper">Паперова</option>
                                        <option value="electronic">Електронна</option>
                                        <option value="audio">Аудіо</option>
                                    </select>
                                </div>

                                <!-- Language -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Мова читання</label>
                                    <select id="language"
                                        class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        v-model="language">
                                        <option value="uk">Українська</option>
                                        <option value="en">Англійська</option>
                                        <option value="pl">Польська</option>
                                        <option value="de">Німецька</option>
                                        <option value="fr">Французька</option>
                                        <option value="es">Іспанська</option>
                                        <option value="it">Італійська</option>
                                        <option value="ru">російська</option>
                                        <option value="other">Інша</option>
                                    </select>
                                    <select v-if="language === 'other'" id="other-language"
                                        class="w-full mt-2 px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        v-model="otherLanguage">
                                        <option value="cs">Чеська</option>
                                        <option value="sk">Словацька</option>
                                        <option value="hu">Угорська</option>
                                        <option value="ro">Румунська</option>
                                        <option value="bg">Болгарська</option>
                                        <option value="lt">Литовська</option>
                                        <option value="pt">Португальська</option>
                                        <option value="nl">Нідерландська</option>
                                        <option value="sv">Шведська</option>
                                        <option value="no">Норвезька</option>
                                        <option value="da">Данська</option>
                                        <option value="fi">Фінська</option>
                                        <option value="ja">Японська</option>
                                        <option value="ko">Корейська</option>
                                        <option value="zh">Китайська</option>
                                    </select>
                                </div>

                                <!-- Form Actions -->
                                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <!-- Tab Switch -->
                                    <div class="inline-flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 gap-1 mb-4">
                                        <button type="button" @@click="actionMode = 'draft'"
                                            :disabled="isSubmitting"
                                            :class="['flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed',
                                                actionMode === 'draft' 
                                                    ? 'bg-white dark:bg-slate-700 text-orange-500 dark:text-orange-400 shadow-sm' 
                                                    : 'text-slate-600 dark:text-slate-400 hover:text-orange-500 dark:hover:text-orange-400']">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                            </svg>
                                            <span>Чернетка</span>
                                        </button>
                                        <button type="button" @@click="actionMode = 'publish'"
                                            :disabled="isSubmitting"
                                            :class="['flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed',
                                                actionMode === 'publish' 
                                                    ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg' 
                                                    : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400']">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Опублікувати</span>
                                        </button>
                                    </div>
                                    
                                    <!-- Submit Button -->
                                    <button type="button" @@click="submitReview(actionMode === 'draft')"
                                        :disabled="isSubmitting || !content.trim() || (actionMode === 'publish' && rating === 0)"
                                        :class="['w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed',
                                            actionMode === 'draft'
                                                ? 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-orange-500 dark:text-orange-400 shadow-md'
                                                : 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg hover:shadow-xl']">
                                        <svg v-if="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                :d="actionMode === 'draft' ? 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4' : 'M5 13l4 4L19 7'" />
                                        </svg>
                                        <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>@{{ actionMode === 'draft' ? 'Зберегти чернетку' : 'Опублікувати рецензію' }}</span>
                                    </button>
                                </div>
                            </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if(!isset($lastReviewInfo) || !$lastReviewInfo || !isset($lastReviewInfo['remaining_seconds']) || $lastReviewInfo['remaining_seconds'] <= 0)
        @push('scripts')
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (typeof window.Vue === 'undefined') {
                        console.error('Vue is not loaded');
                        return;
                    }

                    const reviewApp = new Vue({
                        el: '#review-form-app',
                        data: {
                            reviewType: '{{ isset($review) ? ($review->review_type ?? 'review') : 'review' }}',
                            rating: {{ isset($review) ? ($review->rating ?? 0) : ($userRating ?? 0) }},
                            opinionType: '{{ isset($review) ? ($review->opinion_type ?? 'positive') : 'positive' }}',
                            bookType: '{{ isset($review) ? ($review->book_type ?? 'paper') : 'paper' }}',
                            @php
                                $reviewLanguage = isset($review) ? ($review->language ?? 'uk') : 'uk';
                                $otherLanguages = ['cs', 'sk', 'hu', 'ro', 'bg', 'lt', 'pt', 'nl', 'sv', 'no', 'da', 'fi', 'ja', 'ko', 'zh'];
                                if (in_array($reviewLanguage, $otherLanguages)) {
                                    $language = 'other';
                                    $otherLanguage = $reviewLanguage;
                                } else {
                                    $language = $reviewLanguage;
                                    $otherLanguage = 'cs';
                                }
                            @endphp
                            language: '{{ $language }}',
                            otherLanguage: '{{ $otherLanguage }}',
                            content: '',
                            containsSpoiler: {{ isset($review) ? ($review->contains_spoiler ? 'true' : 'false') : 'false' }},
                            isSubmitting: false,
                            quillInstance: null,
                            actionMode: '{{ isset($review) && $review->is_draft ? 'draft' : 'publish' }}' // 'draft' or 'publish'
                        },
                        computed: {
                            contentLength() {
                                if (this.quillInstance) {
                                    return this.quillInstance.getText().length;
                                }
                                return this.content ? this.content.length : 0;
                            },
                            minContentLength() {
                                return this.reviewType === 'opinion' ? 25 : 800;
                            },
                            maxContentLength() {
                                return this.reviewType === 'opinion' ? 1000 : 15000;
                            }
                        },
                        watch: {
                            reviewType(newVal) {
                                // При зміні типу відгуку оновлюємо інтерфейс
                                this.$nextTick(() => {
                                    // Opinion type field вже має v-if="reviewType === 'opinion'", тому автоматично показується/ховається
                                });
                            }
                        },
                        mounted() {
                            this.initQuill();
                        },
                        methods: {
                            getContentLengthClass() {
                                const length = this.contentLength;
                                const min = this.minContentLength;
                                const max = this.maxContentLength;

                                if (length < min || length > max) {
                                    return 'error';
                                }
                                if (length > max * 0.9) {
                                    return 'warning';
                                }
                                return '';
                            },
                            initQuill() {
                                this.$nextTick(() => {
                                    if (typeof Quill === 'undefined') {
                                        console.error('Quill is not loaded');
                                        return;
                                    }

                                    const toolbarOptions = [
                                        ['bold', 'italic', 'underline'],
                                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                        ['link'],
                                        ['clean']
                                    ];

                                    this.quillInstance = new Quill('#quill-editor-review', {
                                        theme: 'snow',
                                        modules: {
                                            toolbar: toolbarOptions
                                        },
                                    });

                                    @if(isset($review))
                                        // Загружаем контент рецензии при редактировании
                                        const reviewContent = {!! json_encode($review->content) !!};
                                        this.quillInstance.root.innerHTML = reviewContent;
                                        // Обновляем this.content после загрузки
                                        this.$nextTick(() => {
                                            this.content = this.quillInstance.root.innerHTML;
                                        });
                                    @endif

                                    this.quillInstance.on('text-change', () => {
                                        // Обновляем контент при каждом изменении
                                        this.content = this.quillInstance.root.innerHTML;
                                        this.$forceUpdate();
                                    });
                                });
                            },
                            sanitizeHTML(html) {
                                if (!html || html.trim() === '') {
                                    return '';
                                }

                                // Упрощенная санитизация - только удаляем опасные элементы
                                // Не трогаем структуру HTML, чтобы не потерять контент
                                const temp = document.createElement('div');
                                temp.innerHTML = html;

                                // Удаляем только опасные элементы
                                const scripts = temp.querySelectorAll('script');
                                scripts.forEach(script => script.remove());

                                const styles = temp.querySelectorAll('style');
                                styles.forEach(style => style.remove());

                                // Удаляем только опасные атрибуты событий
                                const allElements = temp.querySelectorAll('*');
                                allElements.forEach(el => {
                                    // Удаляем только обработчики событий
                                    const dangerousAttrs = ['onclick', 'onerror', 'onload', 'onmouseover', 'onmouseout', 'onfocus', 'onblur'];
                                    dangerousAttrs.forEach(attr => {
                                        if (el.hasAttribute(attr)) {
                                            el.removeAttribute(attr);
                                        }
                                    });
                                });

                                return temp.innerHTML;
                            },
                            async saveAsDraft() {
                                await this.submitReview(true);
                            },
                            async submitReview(isDraft) {
                                if (typeof isDraft !== 'boolean') {
                                    isDraft = false;
                                }

                                // Получаем контент напрямую из Quill Editor
                                let contentToSend = '';

                                if (this.quillInstance) {
                                    // Получаем HTML напрямую из редактора (самый надежный способ)
                                    const quillHTML = this.quillInstance.root.innerHTML;

                                    // Проверяем текстовое содержимое
                                    const textContent = this.quillInstance.getText().trim();
                                    console.log('Quill content check:', {
                                        htmlLength: quillHTML.length,
                                        textLength: textContent.length,
                                        textPreview: textContent.substring(0, 50)
                                    });

                                    if (!textContent || textContent.length === 0) {
                                        alert('Будь ласка, введіть текст рецензії', 'Помилка валідації', 'error');
                                        return;
                                    }

                                    // Используем HTML напрямую из Quill, санитизация будет на сервере
                                    // Но все равно удаляем только опасные элементы
                                    contentToSend = this.sanitizeHTML(quillHTML);

                                    // Если после санитизации контент стал пустым или содержит только пустые теги,
                                    // но текстовое содержимое есть, используем оригинальный HTML
                                    const sanitizedText = contentToSend.replace(/<[^>]*>/g, '').trim();
                                    if ((!contentToSend || contentToSend.trim() === '' || contentToSend === '<p><br></p>' || contentToSend === '<p></p>') && textContent.length > 0) {
                                        console.warn('Sanitized content is empty but text exists, using original HTML');
                                        contentToSend = quillHTML;
                                    }

                                    // Обновляем this.content для отображения
                                    this.content = contentToSend;
                                } else {
                                    // Если Quill не инициализирован, используем текущий контент
                                    contentToSend = this.content || '';
                                    if (!contentToSend || !contentToSend.trim()) {
                                        alert('Будь ласка, введіть текст рецензії', 'Помилка валідації', 'error');
                                        return;
                                    }
                                }

                                // Проверяем длину текста
                                const textLength = this.quillInstance ? this.quillInstance.getText().length : (contentToSend ? contentToSend.replace(/<[^>]*>/g, '').length : 0);
                                if (textLength === 0) {
                                    alert('Будь ласка, введіть текст рецензії');
                                    return;
                                }

                                if (!isDraft) {
                                    if (!this.rating || this.rating === 0) {
                                        alert('Будь ласка, поставте оцінку перед публікацією', 'Помилка валідації', 'error');
                                        return;
                                    }
                                }

                                this.isSubmitting = true;

                                const form = document.getElementById('review-form');
                                const formData = new FormData(form);

                                // Убеждаемся, что контент не пустой
                                if (!contentToSend || contentToSend.trim() === '' || contentToSend === '<p><br></p>' || contentToSend === '<p></p>') {
                                    console.error('Content is empty after processing:', contentToSend);
                                    alert('Помилка: контент не може бути порожнім', 'Помилка', 'error');
                                    this.isSubmitting = false;
                                    return;
                                }

                                // Устанавливаем контент в FormData
                                // ВАЖНО: устанавливаем контент ПЕРЕД другими полями
                                formData.set('content', contentToSend);

                                // Проверяем, что контент действительно установлен
                                const checkContent = formData.get('content');
                                if (!checkContent || checkContent.trim() === '') {
                                    console.error('Content was not set in FormData!', {
                                        contentToSend: contentToSend,
                                        checkContent: checkContent
                                    });
                                    alert('Помилка: не вдалося встановити контент у форму', 'Помилка', 'error');
                                    this.isSubmitting = false;
                                    return;
                                }

                                // Для чернеток rating може бути порожнім
                                if (isDraft) {
                                    // Якщо оцінка не встановлена, відправляємо порожній рядок
                                    formData.set('rating', this.rating && this.rating > 0 ? this.rating : '');
                                } else {
                                    formData.set('rating', this.rating);
                                }
                                formData.set('review_type', this.reviewType);
                                // Opinion type тільки для відгуків
                                if (this.reviewType === 'opinion') {
                                    formData.set('opinion_type', this.opinionType);
                                }
                                // Для рецензій не відправляємо opinion_type
                                formData.set('book_type', this.bookType);
                                // Використовуємо otherLanguage якщо вибрано "other", інакше використовуємо language
                                formData.set('language', this.language === 'other' ? this.otherLanguage : this.language);
                                formData.set('contains_spoiler', this.containsSpoiler ? '1' : '0');
                                formData.set('is_draft', isDraft ? '1' : '0');

                                // Финальная проверка перед отправкой
                                const finalContent = formData.get('content');
                                const finalText = finalContent ? finalContent.replace(/<[^>]*>/g, '').trim() : '';
                                console.log('Final check before send:', {
                                    hasContent: !!finalContent,
                                    contentLength: finalContent ? finalContent.length : 0,
                                    textLength: finalText.length,
                                    textPreview: finalText.substring(0, 100)
                                });

                                if (!finalContent || !finalText || finalText.length === 0) {
                                    console.error('Content is empty in final check!', {
                                        finalContent: finalContent,
                                        finalText: finalText
                                    });
                                    alert('Помилка: контент порожній перед відправкою', 'Помилка', 'error');
                                    this.isSubmitting = false;
                                    return;
                                }

                                try {
                                    // Проверяем, что контент действительно есть в FormData
                                    const contentValue = formData.get('content');

                                    // Проверяем текстовое содержимое (без HTML тегов)
                                    const textOnly = contentValue ? contentValue.replace(/<[^>]*>/g, '').trim() : '';

                                    if (!contentValue || !textOnly || textOnly.length === 0) {
                                        console.error('Content is empty in FormData:', {
                                            contentValue: contentValue,
                                            textOnly: textOnly,
                                            textLength: textOnly.length,
                                            quillText: this.quillInstance ? this.quillInstance.getText() : 'no quill',
                                            quillHTML: this.quillInstance ? this.quillInstance.root.innerHTML : 'no quill'
                                        });
                                        alert('Помилка: контент не може бути порожнім. Перевірте, що ви ввели текст рецензії.', 'Помилка', 'error');
                                        this.isSubmitting = false;
                                        return;
                                    }

                                    console.log('Sending content:', {
                                        htmlLength: contentValue.length,
                                        textLength: textOnly.length,
                                        preview: textOnly.substring(0, 100) + '...'
                                    });

                                    // Для PUT запросов нужно добавить _method в FormData
                                    if ('{{ isset($review) ? 'true' : 'false' }}' === 'true') {
                                        formData.set('_method', 'PUT');
                                    }

                                    const response = await fetch(form.action, {
                                        method: 'POST', // Всегда POST, Laravel обработает _method
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        body: formData
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        if (isDraft) {
                                            window.location.href = '/profile?tab=drafts';
                                        } else {
                                            @if(isset($review))
                                                window.location.href = '{{ route("books.reviews.show", [$book, $review]) }}';
                                            @else
                                                window.location.href = '{{ route("books.show", $book) }}';
                                            @endif
                                            }
                                    } else {
                                        alert(data.message || 'Помилка при збереженні рецензії', 'Помилка', 'error');
                                        this.isSubmitting = false;
                                    }
                                } catch (error) {
                                    console.error('Error submitting review:', error);
                                    alert('Помилка при додаванні рецензії', 'Помилка', 'error');
                                    this.isSubmitting = false;
                                }
                            }
                        }
                    });

                    // Handle form submit (for compatibility, but now we use button with type="button")
                    const form = document.getElementById('review-form');
                    if (form) {
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            // Use current action mode
                            reviewApp.submitReview(reviewApp.actionMode === 'draft');
                        });
                    }
                });
            </script>
        @endpush
        @endif
@endsection