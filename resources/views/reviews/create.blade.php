@extends('layouts.app')

@section('title', 'Написати рецензію на ' . $book->title . ' - Книжковий форум')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
    <style>
        /* Quill editor styles */
        #quill-editor-review {
            min-height: 500px;
        }

        .ql-container {
            min-height: 500px;
            font-size: 1rem;
            line-height: 1.7;
        }

        .ql-editor {
            min-height: 500px;
        }
    </style>
@endpush

@section('main')
    <div class="min-h-screen bg-background">
        <!-- Navigation Button -->
        <a href="{{ route('books.show', $book) }}"
            class="w-full bg-primary text-primary-foreground py-2 rounded-lg hover:bg-primary/90 transition-colors text-left block text-sm font-medium mb-6">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Назад до книги
        </a>
        <div class="mx-auto flex flex-col-reverse lg:flex-row gap-8">
            <!-- Review Form Section -->
            <div class="bg-card w-full">
                <h2 class="text-2xl font-bold mb-6 text-foreground">Написати рецензію</h2>

                <div id="review-form-app">
                    <form id="review-form" method="POST" action="{{ route('books.reviews.store', $book) }}">
                        @csrf

                        <!-- Hidden inputs for Vue-managed values -->
                        <input type="hidden" name="review_type" v-model="reviewType">
                        <input type="hidden" name="opinion_type" v-model="opinionType">
                        <input type="hidden" name="book_type" v-model="bookType">
                        <input type="hidden" name="language" v-model="language">
                        <input type="hidden" name="contains_spoiler" :value="containsSpoiler ? '1' : '0'">
                        <input type="hidden" name="rating" v-model="rating" required>
                        <!-- Rating -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-foreground mb-2">Оцінка <span
                                    class="text-red-500">*</span></label>
                            <div class="flex gap-2 flex-wrap">
                                <button type="button" v-for="star in 10" :key="star" @@click="rating = star"
                                    :class="['text-3xl bg-transparent border-none cursor-pointer p-0 transition-transform hover:scale-110', star <= rating ? 'text-yellow-400' : 'text-gray-300']">
                                    ★
                                </button>
                            </div>
                        </div>
                        <!-- Review Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-foreground mb-2">Тип відгуку</label>
                            <select id="review_type"
                                class="w-full px-3 py-3 border border-border rounded-lg bg-background dark:bg-dark-bg text-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                v-model="reviewType">
                                <option value="review">Рецензія</option>
                                <option value="opinion">Відгук</option>
                            </select>
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-foreground mb-2">
                                Ваша думка <span class="text-red-500">*</span>
                            </label>
                            <div class="flex justify-between items-center my-2 text-xs text-muted-foreground">
                                <span>
                                    Мінімум: <strong>@{{ minContentLength }}</strong> символів
                                </span>
                                <span class="font-semibold"
                                    :class="getContentLengthClass() === 'error' ? 'text-red-500' : getContentLengthClass() === 'warning' ? 'text-amber-500' : ''">
                                    @{{ contentLength }} / @{{ maxContentLength }} символів
                                </span>
                            </div>
                            <div id="quill-editor-review" class="min-h-[200px] bg-background border border-border"></div>
                            <input type="hidden" name="content" id="review-content" required>
                        </div>

                        <!-- Spoiler Checkbox -->
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="contains_spoiler" v-model="containsSpoiler"
                                    class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Містить спойлер</span>
                            </label>
                        </div>

                        <!-- Opinion Type -->
                        <div class="mb-6" v-show="reviewType === 'opinion'">
                            <label class="block text-sm font-semibold text-foreground mb-2">Тип думки</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @@click="opinionType = 'positive'"
                                    :class="['relative overflow-hidden px-3 py-2.5 rounded-lg border text-center transition-all duration-200', 
                                        opinionType === 'positive' 
                                            ? 'border-green-400/50 bg-green-50/50 dark:bg-green-900/20 dark:border-green-600/50' 
                                            : 'border-border bg-background hover:border-green-300/30 dark:hover:border-green-600/30']">
                                    <div class="text-xl mb-1 transition-transform duration-200"
                                        :class="opinionType === 'positive' ? 'scale-110' : ''">😊</div>
                                    <div class="text-xs font-medium"
                                        :class="opinionType === 'positive' ? 'text-green-600 dark:text-green-400' : 'text-muted-foreground'">
                                        Позитивна</div>
                                </button>
                                <button type="button" @@click="opinionType = 'neutral'"
                                    :class="['relative overflow-hidden px-3 py-2.5 rounded-lg border text-center transition-all duration-200', 
                                        opinionType === 'neutral' 
                                            ? 'border-yellow-400/50 bg-yellow-50/50 dark:bg-yellow-900/20 dark:border-yellow-600/50' 
                                            : 'border-border bg-background hover:border-yellow-300/30 dark:hover:border-yellow-600/30']">
                                    <div class="text-xl mb-1 transition-transform duration-200"
                                        :class="opinionType === 'neutral' ? 'scale-110' : ''">😐</div>
                                    <div class="text-xs font-medium"
                                        :class="opinionType === 'neutral' ? 'text-yellow-600 dark:text-yellow-400' : 'text-muted-foreground'">
                                        Нейтральна</div>
                                </button>
                                <button type="button" @@click="opinionType = 'negative'"
                                    :class="['relative overflow-hidden px-3 py-2.5 rounded-lg border text-center transition-all duration-200', 
                                        opinionType === 'negative' 
                                            ? 'border-red-400/50 bg-red-50/50 dark:bg-red-900/20 dark:border-red-600/50' 
                                            : 'border-border bg-background hover:border-red-300/30 dark:hover:border-red-600/30']">
                                    <div class="text-xl mb-1 transition-transform duration-200"
                                        :class="opinionType === 'negative' ? 'scale-110' : ''">😞</div>
                                    <div class="text-xs font-medium"
                                        :class="opinionType === 'negative' ? 'text-red-600 dark:text-red-400' : 'text-muted-foreground'">
                                        Негативна</div>
                                </button>
                            </div>
                        </div>

                        <!-- Book Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-foreground mb-2">Тип книги</label>
                            <select id="book_type"
                                class="w-full px-3 py-3 border border-border rounded-lg bg-background dark:bg-dark-bg text-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                v-model="bookType">
                                <option value="paper">Паперова</option>
                                <option value="electronic">Електронна</option>
                                <option value="audio">Аудіо</option>
                            </select>
                        </div>

                        <!-- Language -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-foreground mb-2">Мова рецензії</label>
                            <select id="language"
                                class="w-full px-3 py-3 border border-border rounded-lg bg-background dark:bg-dark-bg  text-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                v-model="language">
                                <option value="uk">Українська</option>
                                <option value="en">English</option>
                                <option value="de">Німецька</option>
                                <option value="other">Інша</option>
                            </select>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button type="button" @@click="saveAsDraft" :disabled="isSubmitting || !content.trim()"
                                class="flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all text-xs sm:text-sm font-medium">
                                <svg v-if="!isSubmitting" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                <svg v-else class="w-3.5 h-3.5 sm:w-4 sm:h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span class="hidden sm:inline" v-if="isSubmitting">Збереження...</span>
                                <span class="hidden sm:inline" v-else>Зберегти чернетку</span>
                                <span class="sm:hidden">Чернетка</span>
                            </button>
                            <button type="submit" :disabled="isSubmitting || !content.trim() || rating === 0"
                                class="flex items-center justify-center gap-1.5 flex-1 px-3 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:from-indigo-500 disabled:hover:to-purple-600 transition-all text-xs sm:text-sm font-medium">
                                <svg v-if="!isSubmitting" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <svg v-else class="w-3.5 h-3.5 sm:w-4 sm:h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span class="hidden sm:inline" v-if="isSubmitting">Відправка...</span>
                                <span class="hidden sm:inline" v-else>Опублікувати</span>
                                <span class="sm:hidden" v-if="!isSubmitting">Опублікувати</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sticky Book Sidebar -->
            <div
                class="lg:sticky top-8 h-fit gap-x-4 w-full lg:w-1/4 flex items-center lg:items-start lg:flex-col">
                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}"
                    alt="{{ $book->title }}"
                    class="w-full aspect-[3/4] w-[100px] lg:w-full object-cover rounded-lg shadow-md">
                <div>
                    <h1 class="text-xl font-bold mb-2 lg:mt-5 text-foreground leading-tight">{{ $book->title }}</h1>
                    @if($authorModel)
                        <a href="{{ route('authors.show', $authorModel->slug) }}"
                            class="text-base text-muted-foreground mb-3 hover:text-primary transition-colors inline-block">
                            {{ $authorModel->full_name ?? $authorModel->short_name ?? $book->author }}
                        </a>
                    @else
                        <p class="text-base text-muted-foreground mb-3">{{ $book->author }}</p>
                    @endif
                    <!-- Book Rating -->
                    <div class="flex items-center gap-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="text-lg font-bold text-foreground">{{ $book->display_rating }}/10</span>
                        @if($book->reviews_count > 0)
                            <span class="text-sm text-muted-foreground">({{ $book->reviews_count }})</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                        reviewType: 'review',
                        rating: {{ $userRating ?? 0 }},
                        opinionType: 'positive',
                        bookType: 'paper',
                        language: 'uk',
                        content: '',
                        containsSpoiler: false,
                        isSubmitting: false,
                        quillInstance: null
                    },
                    computed: {
                        contentLength() {
                            if (this.quillInstance) {
                                return this.quillInstance.getText().length;
                            }
                            return this.content ? this.content.length : 0;
                        },
                        minContentLength() {
                            return this.reviewType === 'opinion' ? 100 : 800;
                        },
                        maxContentLength() {
                            return this.reviewType === 'opinion' ? 1000 : 15000;
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
                                    ['link', 'image'],
                                    ['clean']
                                ];

                                this.quillInstance = new Quill('#quill-editor-review', {
                                    theme: 'snow',
                                    modules: {
                                        toolbar: toolbarOptions
                                    },
                                });

                                this.quillInstance.on('text-change', () => {
                                    this.content = this.sanitizeHTML(this.quillInstance.root.innerHTML);
                                    this.$forceUpdate();
                                });
                            });
                        },
                        sanitizeHTML(html) {
                            const temp = document.createElement('div');
                            temp.innerHTML = html;

                            const scripts = temp.querySelectorAll('script');
                            scripts.forEach(script => script.remove());

                            const styles = temp.querySelectorAll('style');
                            styles.forEach(style => style.remove());

                            const allElements = temp.querySelectorAll('*');
                            allElements.forEach(el => {
                                el.removeAttribute('style');
                                el.removeAttribute('onclick');
                                el.removeAttribute('onerror');
                                el.removeAttribute('onload');
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

                            if (this.quillInstance) {
                                this.content = this.sanitizeHTML(this.quillInstance.root.innerHTML);
                            }

                            const textLength = this.quillInstance ? this.quillInstance.getText().length : this.content.length;
                            if (textLength === 0 || !this.content.trim()) {
                                alert('Будь ласка, введіть текст рецензії');
                                return;
                            }

                            if (!isDraft) {
                                if (!this.rating || this.rating === 0) {
                                    alert('Будь ласка, поставте оцінку перед публікацією');
                                    return;
                                }
                            }

                            this.isSubmitting = true;

                            const form = document.getElementById('review-form');
                            const formData = new FormData(form);

                            formData.set('content', this.content);
                            formData.set('rating', this.rating);
                            formData.set('review_type', this.reviewType);
                            formData.set('opinion_type', this.opinionType);
                            formData.set('book_type', this.bookType);
                            formData.set('language', this.language);
                            formData.set('contains_spoiler', this.containsSpoiler ? '1' : '0');
                            formData.set('is_draft', isDraft ? '1' : '0');

                            try {
                                const response = await fetch(form.action, {
                                    method: 'POST',
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
                                        window.location.href = '{{ route("books.show", $book) }}';
                                    }
                                } else {
                                    alert(data.message || 'Помилка при збереженні рецензії');
                                    this.isSubmitting = false;
                                }
                            } catch (error) {
                                console.error('Error submitting review:', error);
                                alert('Помилка при додаванні рецензії');
                                this.isSubmitting = false;
                            }
                        }
                    }
                });

                // Handle form submit
                const form = document.getElementById('review-form');
                if (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        reviewApp.submitReview(false);
                    });
                }
            });
        </script>
    @endpush
@endsection