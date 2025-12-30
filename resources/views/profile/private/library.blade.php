@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Library Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Моя бібліотека</h2>
                    <p class="text-gray-600 dark:text-gray-400">Книги з різними статусами читання</p>
                </div>
            </div>

            <!-- Reading Status Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $readingStatuses = [
                        'want_to_read' => ['title' => 'Хочу прочитати', 'count' => $user->bookReadingStatuses()->where('status', 'want_to_read')->count(), 'color' => 'blue'],
                        'reading' => ['title' => 'Читаю', 'count' => $user->bookReadingStatuses()->where('status', 'reading')->count(), 'color' => 'orange'],
                        'read' => ['title' => 'Прочитано', 'count' => $user->bookReadingStatuses()->where('status', 'read')->count(), 'color' => 'green'],
                        'abandoned' => ['title' => 'Закинуто', 'count' => $user->bookReadingStatuses()->where('status', 'abandoned')->count(), 'color' => 'red'],
                    ];
                @endphp

                @foreach($readingStatuses as $status => $data)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center hover:bg-white/10 transition-all duration-200 cursor-pointer" 
                         onclick="filterByStatus('{{ $status }}')">
                        <div class="text-2xl font-bold text-{{ $data['color'] }}-400 mb-1">{{ $data['count'] }}</div>
                        <div class="text-sm text-gray-900 dark:text-white">{{ $data['title'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Library Content -->
        <div>
            <div class="flex flex-col mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Книги в бібліотеці</h3>
                <!-- Filter and Sort -->
                <div class="flex items-center space-x-2">
                    <select id="statusFilter" onchange="filterByStatus(this.value)" 
                            class="px-2 py-2 bg-white/20 light:text-gray-900 dark:text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                        <option value="">Всі статуси</option>
                        <option value="want_to_read">Хочу прочитати</option>
                        <option value="reading">Читаю</option>
                        <option value="read">Прочитано</option>
                        <option value="abandoned">Закинуто</option>
                    </select>
                    
                    <select id="sortBy" onchange="sortBooks(this.value)" 
                            class="px-2 py-2 bg-white/20 light:text-gray-900 dark:text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                        <option value="created_at">Дата додавання</option>
                        <option value="title">Назва</option>
                        <option value="rating">Оцінка</option>
                    </select>
                </div>
            </div>

            <!-- Books Grid -->
            <div id="booksGrid" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @php
                    $books = $user->bookReadingStatuses()
                        ->with(['book.author', 'book.categories'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(12);
                @endphp

                @if($books->count() > 0)
                    @foreach($books as $readingStatus)
                        <div class="group relative" 
                             data-status="{{ $readingStatus->status }}">
                            <!-- Book Cover -->
                            <div class="aspect-[3/4] mb-3 relative">
                                <img src="{{ $readingStatus->book->cover_image }}" 
                                     alt="{{ $readingStatus->book->title }}"
                                     class="w-full h-full object-cover rounded-lg shadow-md">
                                
                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2">
                                    @php
                                        $statusColors = [
                                            'want_to_read' => 'bg-blue-500',
                                            'reading' => 'bg-orange-500',
                                            'read' => 'bg-green-500',
                                            'abandoned' => 'bg-red-500',
                                        ];
                                        $statusLabels = [
                                            'want_to_read' => 'Хочу',
                                            'reading' => 'Читаю',
                                            'read' => 'Прочитано',
                                            'abandoned' => 'Закинуто',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium text-white rounded-full {{ $statusColors[$readingStatus->status] }}">
                                        {{ $statusLabels[$readingStatus->status] }}
                                    </span>
                                </div>

                                <!-- Rating -->
                                @if($readingStatus->rating)
                                    <div class="absolute bottom-2 left-2">
                                        <div class="flex items-center space-x-1 bg-black/50 backdrop-blur-sm rounded-full px-2 py-1">
                                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="text-xs font-medium text-white">{{ $readingStatus->rating }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div>
                                <!-- Additional Info -->
                                <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400 pt-1">
                                    @if($readingStatus->times_read && $readingStatus->times_read > 1)
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            <span>{{ $readingStatus->times_read }}x</span>
                                        </span>
                                    @endif
                                    @if($readingStatus->reading_language)
                                        @php
                                            $languages = [
                                                'uk' => '🇺🇦',
                                                'en' => '🇬🇧',
                                                'ru' => '🇷🇺',
                                                'pl' => '🇵🇱',
                                                'de' => '🇩🇪',
                                                'fr' => '🇫🇷',
                                                'es' => '🇪🇸',
                                                'it' => '🇮🇹',
                                            ];
                                            $langFlag = $languages[$readingStatus->reading_language] ?? '🌐';
                                        @endphp
                                        <span class="flex items-center space-x-1">
                                            <span>{{ $langFlag }}</span>
                                            <span>{{ strtoupper($readingStatus->reading_language) }}</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $readingStatus->created_at->format('d.m.Y') }}
                                    </span>
                                    
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editBookStatus({{ $readingStatus->id }})" 
                                                class="p-1 text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button onclick="removeFromLibrary({{ $readingStatus->id }})" 
                                                class="p-1 text-gray-400 hover:text-red-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Book Info -->
                            <div class="space-y-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white line-clamp-2">
                                    <a href="{{ route('books.show', $readingStatus->book->slug) }}" 
                                       class="hover:text-orange-500 transition-colors">
                                        {{ $readingStatus->book->title }}
                                    </a>
                                </h4>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $readingStatus->book->author->first_name ?? $readingStatus->book->author ?? 'Не указан' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty State -->
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Бібліотека порожня</h3>
                        <p class="text-gray-500 mb-6">Додайте книги до своєї бібліотеки</p>
                        <a href="{{ route('books.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Додати книги
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($books->hasPages())
                <div class="mt-6">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>
            // Настройка axios для работы с Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            }
            axios.defaults.timeout = 10000; // 10 секунд по умолчанию
            
            let currentReadingStatus = null;
            let editModal = null;

            function manageLibrary() {
                // TODO: Implement library management modal
                console.log('Manage library');
            }

            function filterByStatus(status) {
                const books = document.querySelectorAll('#booksGrid > div[data-status]');
                books.forEach(book => {
                    if (!status || book.dataset.status === status) {
                        book.style.display = 'block';
                    } else {
                        book.style.display = 'none';
                    }
                });
            }

            function sortBooks(sortBy) {
                // TODO: Implement client-side sorting or AJAX request
                console.log('Sort by:', sortBy);
            }

            async function editBookStatus(readingStatusId) {
                if (!readingStatusId) {
                    alert('Помилка: не вказано ID статусу', 'Помилка', 'error');
                    return;
                }
                
                try {
                    // Проверяем наличие CSRF токена
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.warn('CSRF token not found');
                    }
                    
                    const response = await axios.get(`/api/reading-status/status/${readingStatusId}`, {
                        timeout: 10000,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.data && response.data.success) {
                        currentReadingStatus = response.data.data;
                        openEditModal();
                    } else {
                        alert('Помилка при завантаженні даних: ' + (response.data?.message || 'Невідома помилка'), 'Помилка', 'error');
                    }
                } catch (error) {
                    console.error('Error loading status:', error);
                    if (error.code === 'ECONNABORTED' || error.message === 'Request aborted') {
                        console.warn('Request was aborted, this might be due to page navigation');
                        return; // Не показываем ошибку, если запрос был прерван из-за навигации
                    } else if (error.response) {
                        // Сервер ответил с ошибкой
                        alert('Помилка при завантаженні даних: ' + (error.response.data?.message || `HTTP ${error.response.status}`), 'Помилка', 'error');
                    } else if (error.request) {
                        // Запрос был отправлен, но ответа не получено
                        alert('Не вдалося отримати відповідь від сервера. Перевірте підключення до інтернету.', 'Помилка', 'error');
                    } else {
                        // Ошибка при настройке запроса
                        alert('Помилка при налаштуванні запиту: ' + error.message, 'Помилка', 'error');
                    }
                }
            }

            function openEditModal() {
                if (!editModal) {
                    // Создаем модальное окно, если его еще нет
                    const modalHtml = `
                        <div id="editBookStatusModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Редагувати статус книги</h3>
                                        <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <form id="editStatusForm" onsubmit="saveBookStatus(event)">
                                        <div class="mb-4">
                                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Статус</label>
                                            <select id="editStatus" required class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                                                <option value="want_to_read">Хочу прочитати</option>
                                                <option value="reading">Читаю</option>
                                                <option value="read">Прочитано</option>
                                                <option value="abandoned">Закинуто</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Кількість разів прочитано</label>
                                            <input id="editTimesRead" type="number" min="1" required class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                                        </div>
                                        <div class="mb-6">
                                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Мова читання</label>
                                            <select id="editReadingLanguage" class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                                                <option value="">Не вказано</option>
                                                <option value="uk">Українська</option>
                                                <option value="en">English</option>
                                                <option value="ru">Русский</option>
                                                <option value="pl">Polski</option>
                                                <option value="de">Deutsch</option>
                                                <option value="fr">Français</option>
                                                <option value="es">Español</option>
                                                <option value="it">Italiano</option>
                                                <option value="other">Інша</option>
                                            </select>
                                        </div>
                                        <div id="ratingSection" class="mb-6" style="display: none;">
                                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Оцінка</label>
                                            <div class="flex items-center space-x-1" id="ratingButtons">
                                                <button type="button" onclick="setRating(this)" data-rating="1" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">1</button>
                                                <button type="button" onclick="setRating(this)" data-rating="2" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">2</button>
                                                <button type="button" onclick="setRating(this)" data-rating="3" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">3</button>
                                                <button type="button" onclick="setRating(this)" data-rating="4" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">4</button>
                                                <button type="button" onclick="setRating(this)" data-rating="5" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">5</button>
                                                <button type="button" onclick="setRating(this)" data-rating="6" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">6</button>
                                                <button type="button" onclick="setRating(this)" data-rating="7" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">7</button>
                                                <button type="button" onclick="setRating(this)" data-rating="8" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">8</button>
                                                <button type="button" onclick="setRating(this)" data-rating="9" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">9</button>
                                                <button type="button" onclick="setRating(this)" data-rating="10" class="w-10 h-10 rounded-lg transition-all duration-200 bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30">10</button>
                                            </div>
                                            <input type="hidden" id="editRating" value="">
                                        </div>
                                        <div class="flex space-x-3">
                                            <button type="submit" id="saveStatusBtn" class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all">
                                                Зберегти
                                            </button>
                                            <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-3 px-6 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all">
                                                Скасувати
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    editModal = document.getElementById('editBookStatusModal');
                }
                
                // Заполняем форму данными
                if (currentReadingStatus) {
                    document.getElementById('editStatus').value = currentReadingStatus.status || 'want_to_read';
                    document.getElementById('editTimesRead').value = currentReadingStatus.times_read || 1;
                    document.getElementById('editReadingLanguage').value = currentReadingStatus.reading_language || '';
                    document.getElementById('editRating').value = currentReadingStatus.rating || '';
                    
                    // Показываем/скрываем секцию рейтинга
                    const ratingSection = document.getElementById('ratingSection');
                    const statusSelect = document.getElementById('editStatus');
                    if (statusSelect.value === 'read') {
                        ratingSection.style.display = 'block';
                        updateRatingButtons(currentReadingStatus.rating || 0);
                    } else {
                        ratingSection.style.display = 'none';
                    }
                    
                    // Обновляем рейтинг при изменении статуса
                    statusSelect.addEventListener('change', function() {
                        if (this.value === 'read') {
                            ratingSection.style.display = 'block';
                        } else {
                            ratingSection.style.display = 'none';
                            document.getElementById('editRating').value = '';
                        }
                    });
                }
                
                editModal.style.display = 'flex';
            }
            
            function setRating(button) {
                const rating = parseInt(button.dataset.rating) || parseInt(button.textContent.trim());
                document.getElementById('editRating').value = rating;
                updateRatingButtons(rating);
            }
            
            function updateRatingButtons(rating) {
                const buttons = document.querySelectorAll('#ratingSection button[data-rating]');
                buttons.forEach((btn) => {
                    const value = parseInt(btn.dataset.rating);
                    if (value <= rating) {
                        btn.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-400');
                        btn.classList.add('bg-yellow-400', 'text-white');
                    } else {
                        btn.classList.remove('bg-yellow-400', 'text-white');
                        btn.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-400');
                    }
                });
            }

            function closeEditModal() {
                if (editModal) {
                    editModal.style.display = 'none';
                }
            }

            async function saveBookStatus(event) {
                event.preventDefault();
                
                const btn = document.getElementById('saveStatusBtn');
                btn.disabled = true;
                btn.textContent = 'Збереження...';
                
                try {
                    const formData = {
                        status: document.getElementById('editStatus').value,
                        times_read: parseInt(document.getElementById('editTimesRead').value),
                        reading_language: document.getElementById('editReadingLanguage').value || null,
                        rating: document.getElementById('editRating').value ? parseInt(document.getElementById('editRating').value) : null
                    };
                    
                    const response = await axios.put(`/api/reading-status/status/${currentReadingStatus.id}`, formData, {
                        timeout: 10000,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (response.data.success) {
                        alert('Статус оновлено!', 'Успіх', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(response.data.message || 'Помилка при збереженні', 'Помилка', 'error');
                    }
                } catch (error) {
                    console.error('Error saving status:', error);
                    alert(error.response?.data?.message || 'Помилка при збереженні статусу', 'Помилка', 'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Зберегти';
                }
            }

            async function removeFromLibrary(readingStatusId) {
                const confirmed = await confirm('Ви впевнені, що хочете видалити цю книгу з бібліотеки?', 'Підтвердження', 'warning');
                if (!confirmed) {
                    return;
                }
                
                try {
                    // Получаем book_id из статуса
                    const statusResponse = await axios.get(`/api/reading-status/status/${readingStatusId}`, {
                        timeout: 10000,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    if (!statusResponse.data || !statusResponse.data.success) {
                        alert('Помилка при завантаженні даних', 'Помилка', 'error');
                        return;
                    }
                    
                    const bookId = statusResponse.data.data.book_id;
                    if (!bookId) {
                        alert('Помилка: не вдалося отримати ID книги', 'Помилка', 'error');
                        return;
                    }
                    
                    const response = await axios.delete(`/api/reading-status/book/${bookId}`, {
                        timeout: 10000,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.data.success) {
                        alert('Книгу видалено з бібліотеки!', 'Успіх', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(response.data.message || 'Помилка при видаленні', 'Помилка', 'error');
                    }
                } catch (error) {
                    console.error('Error removing from library:', error);
                    alert('Помилка при видаленні книги', 'Помилка', 'error');
                }
            }

            // Закрытие модального окна при клике на фон
            document.addEventListener('click', function(event) {
                if (editModal && event.target === editModal) {
                    closeEditModal();
                }
            });
        </script>
    @endpush
@endsection

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
