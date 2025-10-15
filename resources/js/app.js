// Импортируем стили
import '../css/app.scss';

// Импортируем Vue и Axios
import Vue from 'vue';
import axios from 'axios';

// Импортируем компоненты
import BookCard from './components/BookCard.vue';
import AddToLibraryModal from './components/AddToLibraryModal.vue';
import AddToLibraryButton from './components/AddToLibraryButton.vue';
import LibraryCollection from './components/LibraryCollection.vue';
import UserLibraryBookCard from './components/UserLibraryBookCard.vue';
import NotificationBell from './components/NotificationBell.vue';
import NotificationsPage from './components/NotificationsPage.vue';
import NotificationFilters from './components/NotificationFilters.vue';
import NotificationCard from './components/NotificationCard.vue';
import NotificationPagination from './components/NotificationPagination.vue';
import DiscussionReply from './components/DiscussionReply.vue';
import DiscussionRepliesList from './components/DiscussionRepliesList.vue';
import ReviewReply from './components/ReviewReply.vue';
import ReviewsRepliesList from './components/ReviewsRepliesList.vue';
import QuoteCard from './components/books/QuoteCard.vue';
import QuotesList from './components/books/QuotesList.vue';
import AddQuoteModal from './components/books/AddQuoteModal.vue';
import InterestingFacts from './components/books/InterestingFacts.vue';
import AddReviewModal from './components/books/AddReviewModal.vue';
import BookReviewsList from './components/books/ReviewsList.vue';
import FactCard from './components/books/FactCard.vue';
import FactsList from './components/books/FactsList.vue';
import PriceComparison from './components/books/PriceComparison.vue';
import UnifiedContentList from './components/UnifiedContentList.vue';
import ContentFilters from './components/ContentFilters.vue';
import ReportModal from './components/ReportModal.vue';
import ReportButton from './components/ReportButton.vue';
import QuotesSlider from './components/QuotesSlider.vue';
import QuotesSliderSimple from './components/QuotesSliderSimple.vue';

// Настройка Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Получаем CSRF токен
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
}

// Глобальные переменные
window.repliesPages = {};

// Функции для управления ветками (сворачивание/разворачивания)
window.toggleBranch = function(branchId) {
    console.log('toggleBranch called with:', branchId);
    
    const branchContent = document.getElementById(`branchContent${branchId}`);
    console.log('Branch content element:', branchContent);
    
    if (branchContent) {
        const isCollapsed = branchContent.classList.contains('collapsed');
        console.log('Is collapsed:', isCollapsed);
        
        if (isCollapsed) {
            // Разворачиваем ветку
            branchContent.classList.remove('collapsed');
            branchContent.classList.add('expanded');
            console.log('Branch expanded');
        } else {
            // Сворачиваем ветку
            branchContent.classList.remove('expanded');
            branchContent.classList.add('collapsed');
            console.log('Branch collapsed');
        }
    } else {
        console.error('Branch content element not found for ID:', `branchContent${branchId}`);
    }
};

// Функция для создания уникального ID ветки
window.generateBranchId = function(parentId, depth) {
    return `branch_${parentId}_${depth}`;
};

// Функции для работы с рецензиями
window.toggleReplyForm = function(reviewId) {
    const form = document.getElementById(`replyForm${reviewId}`);
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
};

window.loadReplies = function(reviewId, page = 1) {
    console.log('Loading replies for review:', reviewId, 'page:', page);
    
    // Если это первая страница, просто показываем контейнер (содержимое уже загружено через Blade)
    if (page === 1) {
        const container = document.getElementById(`repliesContainer${reviewId}`);
        if (container) {
            container.classList.remove('hidden');
        }
        return;
    }
    
    // Для последующих страниц загружаем через API
    axios.get(`/api/reviews/${reviewId}/replies?page=${page}`)
        .then(response => {
            const data = response.data;
            console.log('Additional replies loaded:', data);
            
            const container = document.getElementById(`repliesContent${reviewId}`);
            if (container) {
                // Добавляем дополнительные ответы
                data.data.forEach(reply => {
                    const replyElement = createReplyElementWithBranches(reply, 0);
                    container.appendChild(replyElement);
                });
                
                // Управляем кнопкой "Загрузить еще"
                const loadMoreContainer = document.getElementById(`loadMoreContainer${reviewId}`);
                if (loadMoreContainer) {
                    if (data.next_page_url) {
                        loadMoreContainer.classList.remove('hidden');
                        window.repliesPages[reviewId] = page + 1;
                    } else {
                        loadMoreContainer.classList.add('hidden');
                    }
                }
                
                // Скрываем кнопку "Показать ответы" и показываем "Скрыть ответы"
                const showButton = document.querySelector(`button[onclick="loadReplies(${reviewId}, 1)"]`);
                if (showButton && page === 1) {
                    showButton.style.display = 'none';
                    
                    // Проверяем, есть ли уже кнопка "Скрыть ответы"
                    let hideButton = showButton.parentNode.querySelector('.hide-replies-btn');
                    if (!hideButton) {
                        // Добавляем кнопку "Скрыть ответы"
                        hideButton = document.createElement('button');
                        hideButton.className = 'hide-replies-btn text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors';
                        hideButton.onclick = () => hideReplies(reviewId);
                        hideButton.innerHTML = `
                            Скрыть ответы
                            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        `;
                        showButton.parentNode.appendChild(hideButton);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error loading replies:', error);
            showNotification('Ошибка при загрузке ответов', 'error');
        });
};

window.loadMoreReplies = function(reviewId) {
    const page = window.repliesPages[reviewId] || 2;
    loadReplies(reviewId, page);
};

// Функция для переключения показа/скрытия ответов
window.toggleReplies = function(reviewId) {
    console.log('toggleReplies called for review:', reviewId);
    
    const container = document.getElementById(`repliesContainer${reviewId}`);
    const toggleButton = document.getElementById(`replyToggle${reviewId}`);
    
    console.log('Container:', container);
    console.log('Toggle button:', toggleButton);
    
    if (!container || !toggleButton) {
        console.error('Container or toggle button not found for review:', reviewId);
        return;
    }
    
    const isHidden = container.classList.contains('hidden');
    console.log('Is hidden:', isHidden);
    
    if (isHidden) {
        // Показываем ответы
        container.classList.remove('hidden');
        console.log('Showing replies');
        
        // Обновляем кнопку
        toggleButton.innerHTML = `
            <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
            <span>Скрыть ответы</span>
        `;
    } else {
        // Скрываем ответы
        container.classList.add('hidden');
        console.log('Hiding replies');
        
        // Обновляем кнопку
        toggleButton.innerHTML = `
            <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <span>Показать ответы</span>
        `;
    }
};

// Функция для скрытия ответов
window.hideReplies = function(reviewId) {
    console.log('hideReplies called for review:', reviewId);
    
    const container = document.getElementById(`repliesContainer${reviewId}`);
    const toggleButton = document.getElementById(`replyToggle${reviewId}`);
    
    if (!container || !toggleButton) {
        console.log('Container or toggle button not found');
        return;
    }
    
    container.classList.add('hidden');
    toggleButton.innerHTML = `
        <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        <span>Показать ответы</span>
    `;
};

// Функция для создания ответа с поддержкой веток (новая)
window.createReplyElementWithBranches = function(reply, depth = 0) {
    const div = document.createElement('div');
    const currentDepth = depth + 1;
    const isThirdLevel = currentDepth == 3; // Третий уровень - ветка
    const isFourthLevel = currentDepth >= 4; // Четвертый уровень и глубше - в один ряд
    
    // Простые отступы для первых двух уровней
    const marginLeft = currentDepth <= 2 ? currentDepth * 0.5 : 1; // Максимум 1rem для третьего уровня
    
    div.className = `reply-item ml-${marginLeft} border-l-2 border-gray-200 dark:border-gray-700 pl-2 bg-gray-50 dark:bg-gray-800/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700`;
    div.setAttribute('data-reply-id', reply.id);
    div.setAttribute('data-depth', currentDepth);
    div.setAttribute('data-review-id', reply.parent_id || reply.review_id);
    
    const authorName = reply.user ? reply.user.name : 'Гость';
    const isGuest = !reply.user;
    
    const avatarHtml = isGuest ? 
        `<div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center relative">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-orange-500 rounded-full flex items-center justify-center">
                <span class="text-xs text-white font-bold">Г</span>
            </div>
        </div>` :
        `<div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-600 rounded-full flex items-center justify-center">
            <span class="text-xs font-bold text-white">${authorName.charAt(0)}</span>
        </div>`;
    
    const guestBadge = isGuest ? 
        `<span class="ml-2 px-2 py-1 text-xs bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded-full">Гость</span>` : '';
    
    // Форматируем время
    let timeText = 'только что';
    if (reply.created_at) {
        const createdDate = new Date(reply.created_at);
        const now = new Date();
        const diffMs = now - createdDate;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) {
            timeText = 'только что';
        } else if (diffMins < 60) {
            timeText = `${diffMins} мин. назад`;
        } else if (diffHours < 24) {
            timeText = `${diffHours} ч. назад`;
        } else {
            timeText = `${diffDays} дн. назад`;
        }
    }
    
    div.innerHTML = `
        <div class="flex items-start space-x-3">
            ${avatarHtml}
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="font-medium text-gray-900 dark:text-white">${authorName}</span>
                    ${guestBadge}
                    <span class="text-xs text-gray-500 dark:text-gray-400">${timeText}</span>
                </div>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">${reply.content}</p>
                <div class="mt-3 flex items-center space-x-4">
                    <!-- Like/Dislike Section для ответов -->
                    <div class="flex items-center space-x-2">
                        <!-- Like Button -->
                        <button onclick="likeReview(${reply.id})"
                                class="like-btn p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-400 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <span class="likes-count text-xs text-gray-600 dark:text-gray-400">${reply.likes_count || 0}</span>
                        
                        <!-- Dislike Button -->
                        <button onclick="dislikeReview(${reply.id})"
                                class="dislike-btn p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-400 hover:text-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.912c.163 0 .322.028.475.082l3.276 1.5c.337.154.563.504.563.877v6.541c0 .373-.226.723-.563.877l-3.276 1.5a2 2 0 01-.475.082H10V14z"/>
                            </svg>
                        </button>
                        <span class="dislikes-count text-xs text-gray-600 dark:text-gray-400">${reply.dislikes_count || 0}</span>
                    </div>
                    
                    <button onclick="toggleReplyForm(${reply.id})" class="text-xs text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        Ответить
                    </button>
                    
                    ${reply.replies && reply.replies.length > 0 ? `
                        <button onclick="toggleBranch('branch_${reply.id}_${currentDepth}')" class="text-xs text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Развернуть
                        </button>
                    ` : ''}
                </div>
            </div>
            
            ${reply.replies && reply.replies.length > 0 ? `
                <div id="branchContent_branch_${reply.id}_${currentDepth}" class="branch-content collapsed mt-3">
                    <div class="replies-container space-y-3">
                        ${isFourthLevel ? 
                            // Четвертый уровень и глубше - в один ряд как на YouTube
                            reply.replies.map(subReply => {
                                // Для четвертого уровня создаем простой элемент без рекурсии
                                const subReplyElement = createSimpleReplyElement(subReply, currentDepth);
                                subReplyElement.classList.add('flat-reply-item');
                                subReplyElement.innerHTML = `
                                    <div class="reply-to-indicator mb-2">
                                        Ответ для <span class="reply-to-name font-semibold">${reply.user ? reply.user.name : 'Гость'}</span>
                                    </div>
                                    ${subReplyElement.innerHTML}
                                `;
                                return subReplyElement.outerHTML;
                            }).join('') :
                            // Первый, второй и третий уровень - рекурсивно
                            reply.replies.map(subReply => createReplyElementWithBranches(subReply, currentDepth).outerHTML).join('')
                        }
                    </div>
                </div>
            ` : ''}
            
            <!-- Reply Form для ответов -->
            <div id="replyForm${reply.id}" class="hidden mt-3 p-3 bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700">
                <h5 class="text-xs font-medium text-gray-900 dark:text-white mb-2">Ваш ответ</h5>
                <form onsubmit="submitReply(event, ${reply.id}, null)" class="space-y-2">
                    <textarea 
                        name="content" 
                        rows="2" 
                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        placeholder="Напишите ваш ответ..."
                        required
                    ></textarea>
                    <div class="flex items-center justify-end space-x-2">
                        <button type="button" onclick="toggleReplyForm(${reply.id})" class="px-2 py-1 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                            Отмена
                        </button>
                        <button type="submit" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded transition-colors">
                            Ответить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    return div;
};

// Функция для создания простого ответа без рекурсии (для четвертого уровня)
window.createSimpleReplyElement = function(reply, depth = 0) {
    const div = document.createElement('div');
    const currentDepth = depth + 1;
    
    div.className = `reply-item ml-2 border-l-2 border-gray-200 dark:border-gray-700 pl-2 bg-gray-50 dark:bg-gray-800/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700`;
    div.setAttribute('data-reply-id', reply.id);
    div.setAttribute('data-depth', currentDepth);
    
    const authorName = reply.user ? reply.user.name : 'Гость';
    const isGuest = !reply.user;
    
    const avatarHtml = isGuest ? 
        `<div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center relative">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-orange-500 rounded-full flex items-center justify-center">
                <span class="text-xs text-white font-bold">Г</span>
            </div>
        </div>` :
        `<div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-600 rounded-full flex items-center justify-center">
            <span class="text-xs font-bold text-white">${authorName.charAt(0)}</span>
        </div>`;
    
    const guestBadge = isGuest ? 
        `<span class="ml-2 px-2 py-1 text-xs bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded-full">Гость</span>` : '';
    
    // Форматируем время
    let timeText = 'только что';
    if (reply.created_at) {
        const createdDate = new Date(reply.created_at);
        const now = new Date();
        const diffMs = now - createdDate;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) {
            timeText = 'только что';
        } else if (diffMins < 60) {
            timeText = `${diffMins} мин. назад`;
        } else if (diffHours < 24) {
            timeText = `${diffHours} ч. назад`;
        } else {
            timeText = `${diffDays} дн. назад`;
        }
    }
    
    div.innerHTML = `
        <div class="flex items-start space-x-3">
            ${avatarHtml}
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="font-medium text-gray-900 dark:text-white">${authorName}</span>
                    ${guestBadge}
                    <span class="text-xs text-gray-500 dark:text-gray-400">${timeText}</span>
                </div>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">${reply.content}</p>
                <div class="mt-3 flex items-center space-x-4">
                    <!-- Like/Dislike Section -->
                    <div class="flex items-center space-x-2">
                        <!-- Like Button -->
                        <button onclick="likeReview(${reply.id})"
                                class="like-btn p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-400 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <span class="likes-count text-xs text-gray-600 dark:text-gray-400">${reply.likes_count || 0}</span>
                        
                        <!-- Dislike Button -->
                        <button onclick="dislikeReview(${reply.id})"
                                class="dislike-btn p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-400 hover:text-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.912c.163 0 .322.028.475.082l3.276 1.5c.337.154.563.504.563.877v6.541c0 .373-.226.723-.563.877l-3.276 1.5a2 2 0 01-.475.082H10V14z"/>
                            </svg>
                        </button>
                        <span class="dislikes-count text-xs text-gray-600 dark:text-gray-400">${reply.dislikes_count || 0}</span>
                    </div>
                    
                    <button onclick="toggleReplyForm(${reply.id})" class="text-xs text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        Ответить
                    </button>
                </div>
            </div>
        </div>
    `;
    
    return div;
};

window.submitReply = function(event, reviewId, bookId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    // Определяем, авторизован ли пользователь
    const userMeta = document.querySelector('meta[name="user-id"]');
    const userId = userMeta ? userMeta.getAttribute('content') : null;
    const isGuest = !userId || userId === '';
    
    // Получаем slug книги из URL страницы
    const pathParts = window.location.pathname.split('/');
    const bookSlug = pathParts[2]; // /books/{slug}/...
    
    // Определяем, является ли это ответом на ответ или на основную рецензию
    const reviewElement = document.querySelector(`[data-review-id="${reviewId}"]`);
    let targetReviewId = reviewId;
    
    // Если это ответ на ответ, находим родительскую рецензию
    if (!reviewElement) {
        // Ищем родительский элемент с data-review-id
        const replyElement = document.querySelector(`[data-reply-id="${reviewId}"]`);
        if (replyElement) {
            const parentReview = replyElement.closest('[data-review-id]');
            if (parentReview) {
                targetReviewId = parentReview.getAttribute('data-review-id');
            }
        }
    }
    
    // URL для ответа
    const url = `/books/${bookSlug}/reviews/${targetReviewId}/replies`;
    
    // Debug information
    console.log('Reply submission debug:', {
        reviewId,
        targetReviewId,
        bookId,
        isGuest,
        url,
        formData: Object.fromEntries(formData)
    });
    
    // Отправляем запрос через Axios
    axios.post(url, formData)
        .then(response => {
            const data = response.data;
            if (data.success) {
                showNotification(data.message || 'Ответ добавлен успешно!', 'success');
                
                // Обновляем счетчик ответов
                const toggleButton = document.getElementById(`replyToggle${targetReviewId}`);
                if (toggleButton) {
                    // Обновляем текст кнопки
                    toggleButton.innerHTML = `
                        <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span>Показать ответы</span>
                    `;
                }
                
                // Скрываем форму ответа
                toggleReplyForm(reviewId);
                
                // Если ответы уже загружены, добавляем новый ответ
                const container = document.getElementById(`repliesContent${targetReviewId}`);
                if (container && !container.closest('.hidden')) {
                    // Если это ответ на ответ, находим родительский элемент
                    if (reviewId !== targetReviewId) {
                        const parentReply = document.querySelector(`[data-reply-id="${reviewId}"]`);
                        if (parentReply) {
                            // Ищем контейнер для ответов на этот ответ
                            let repliesContainer = parentReply.querySelector('.replies-container');
                            if (!repliesContainer) {
                                // Создаем контейнер если его нет
                                repliesContainer = document.createElement('div');
                                repliesContainer.className = 'replies-container space-y-3';
                                parentReply.appendChild(repliesContainer);
                            }
                            // Определяем глубину для нового ответа
                            const parentDepth = parseInt(parentReply.getAttribute('data-depth') || 0);
                            const newReply = createReplyElementWithBranches(data.reply, parentDepth);
                            repliesContainer.appendChild(newReply);
                            
                            // Настраиваем обработчики для новых кнопок
                            const newToggleButton = newReply.querySelector('.reply-toggle-btn');
                            if (newToggleButton) {
                                setupReplyToggleButton(newToggleButton);
                            }
                        }
                    } else {
                        // Если это ответ на основную рецензию
                        const newReply = createReplyElementWithBranches(data.reply, 0);
                        container.appendChild(newReply);
                        
                        // Настраиваем обработчики для новых кнопок
                        const newToggleButton = newReply.querySelector('.reply-toggle-btn');
                        if (newToggleButton) {
                            setupReplyToggleButton(newToggleButton);
                        }
                    }
                }
            } else {
                showNotification('Ошибка при добавлении ответа', 'error');
            }
        })
        .catch(error => {
            console.error('Error submitting reply:', error);
            let errorMessage = 'Ошибка при добавлении ответа';
            
            if (error.response) {
                if (error.response.status === 422) {
                    errorMessage = 'Ошибка валидации: ' + Object.values(error.response.data.errors).flat().join(', ');
                } else if (error.response.data && error.response.data.message) {
                    errorMessage = error.response.data.message;
                }
            }
            
            showNotification(errorMessage, 'error');
        });
};

window.likeReview = function(reviewId) {
    // AJAX запрос для лайка
    axios.post(`/api/reviews/${reviewId}/like`)
        .then(response => {
            const data = response.data;
            if (data.success) {
                // Обновляем счетчики для рецензий
                let likesCount = document.querySelector(`[data-review-id="${reviewId}"] .likes-count`);
                let dislikesCount = document.querySelector(`[data-review-id="${reviewId}"] .dislikes-count`);
                let likeBtn = document.querySelector(`[data-review-id="${reviewId}"] .like-btn`);
                let dislikeBtn = document.querySelector(`[data-review-id="${reviewId}"] .dislike-btn`);
                
                // Если не нашли для рецензии, ищем для ответа
                if (!likesCount) {
                    likesCount = document.querySelector(`[data-reply-id="${reviewId}"] .likes-count`);
                    dislikesCount = document.querySelector(`[data-reply-id="${reviewId}"] .dislikes-count`);
                    likeBtn = document.querySelector(`[data-reply-id="${reviewId}"] .like-btn`);
                    dislikeBtn = document.querySelector(`[data-reply-id="${reviewId}"] .dislike-btn`);
                }
                
                if (likesCount) likesCount.textContent = data.likesCount;
                if (dislikesCount) dislikesCount.textContent = data.dislikesCount;
                
                // Обновляем состояние кнопок
                if (likeBtn) {
                    if (data.isLiked) {
                        likeBtn.classList.add('text-red-500');
                        likeBtn.querySelector('svg').setAttribute('fill', 'currentColor');
                    } else {
                        likeBtn.classList.remove('text-red-500');
                        likeBtn.querySelector('svg').setAttribute('fill', 'none');
                    }
                }
                
                // Сбрасываем дизлайк если был
                if (dislikeBtn && data.isLiked) {
                    dislikeBtn.classList.remove('text-blue-500');
                    dislikeBtn.querySelector('svg').setAttribute('fill', 'none');
                }
                
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error liking review:', error);
            showNotification('Ошибка при лайке', 'error');
        });
};

window.dislikeReview = function(reviewId) {
    // AJAX запрос для дизлайка
    axios.post(`/api/reviews/${reviewId}/dislike`)
        .then(response => {
            const data = response.data;
            if (data.success) {
                // Обновляем счетчики для рецензий
                let likesCount = document.querySelector(`[data-review-id="${reviewId}"] .likes-count`);
                let dislikesCount = document.querySelector(`[data-review-id="${reviewId}"] .dislikes-count`);
                let likeBtn = document.querySelector(`[data-review-id="${reviewId}"] .like-btn`);
                let dislikeBtn = document.querySelector(`[data-review-id="${reviewId}"] .dislike-btn`);
                
                // Если не нашли для рецензии, ищем для ответа
                if (!likesCount) {
                    likesCount = document.querySelector(`[data-reply-id="${reviewId}"] .likes-count`);
                    dislikesCount = document.querySelector(`[data-reply-id="${reviewId}"] .dislikes-count`);
                    likeBtn = document.querySelector(`[data-reply-id="${reviewId}"] .like-btn`);
                    dislikeBtn = document.querySelector(`[data-reply-id="${reviewId}"] .dislike-btn`);
                }
                
                if (likesCount) likesCount.textContent = data.likesCount;
                if (dislikesCount) dislikesCount.textContent = data.dislikesCount;
                
                // Обновляем состояние кнопок
                if (dislikeBtn) {
                    if (data.isDisliked) {
                        dislikeBtn.classList.add('text-blue-500');
                        dislikeBtn.querySelector('svg').setAttribute('fill', 'currentColor');
                    } else {
                        dislikeBtn.classList.remove('text-blue-500');
                        dislikeBtn.querySelector('svg').setAttribute('fill', 'none');
                    }
                }
                
                // Сбрасываем лайк если был
                if (likeBtn && data.isDisliked) {
                    likeBtn.classList.remove('text-red-500');
                    likeBtn.querySelector('svg').setAttribute('fill', 'none');
                }
                
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error disliking review:', error);
            showNotification('Ошибка при дизлайке', 'error');
        });
};

window.editReview = function(reviewId) {
    // Реализация редактирования рецензии
    console.log('Edit review:', reviewId);
};

window.deleteReview = function(reviewId) {
    if (confirm('Вы уверены, что хотите удалить эту рецензию?')) {
        axios.delete(`/api/reviews/${reviewId}`)
            .then(response => {
                const data = response.data;
                if (data.success) {
                    const reviewElement = document.querySelector(`[data-review-id="${reviewId}"]`);
                    if (reviewElement) {
                        reviewElement.remove();
                    }
                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error deleting review:', error);
                showNotification('Ошибка при удалении рецензии', 'error');
            });
    }
};

window.showNotification = function(message, type = 'info') {
    // Простая система уведомлений
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
};

// Инициализация Vue приложения
window.Vue = Vue;

// Регистрируем компоненты глобально
Vue.component('book-card', BookCard);
Vue.component('add-to-library-modal', AddToLibraryModal);
Vue.component('add-to-library-button', AddToLibraryButton);
Vue.component('library-collection', LibraryCollection);
Vue.component('user-library-book-card', UserLibraryBookCard);
Vue.component('notification-bell', NotificationBell);
Vue.component('notifications-page', NotificationsPage);
Vue.component('notification-filters', NotificationFilters);
Vue.component('notification-card', NotificationCard);
Vue.component('notification-pagination', NotificationPagination);
Vue.component('discussion-reply', DiscussionReply);
Vue.component('discussion-replies-list', DiscussionRepliesList);
Vue.component('review-reply', ReviewReply);
Vue.component('reviews-replies-list', ReviewsRepliesList);
Vue.component('quote-card', QuoteCard);
Vue.component('quotes-list', QuotesList);
Vue.component('add-quote-modal', AddQuoteModal);
Vue.component('interesting-facts', InterestingFacts);
Vue.component('add-review-modal', AddReviewModal);
Vue.component('book-reviews-list', BookReviewsList);
Vue.component('reviews-list', BookReviewsList);
Vue.component('fact-card', FactCard);
Vue.component('facts-list', FactsList);
Vue.component('price-comparison', PriceComparison);
Vue.component('unified-content-list', UnifiedContentList);
Vue.component('content-filters', ContentFilters);
Vue.component('report-modal', ReportModal);
Vue.component('report-button', ReportButton);
Vue.component('quotes-slider', QuotesSlider);
Vue.component('quotes-slider-simple', QuotesSliderSimple);

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    console.log('App loaded successfully!');
    
    // Настройка звездочек для рейтинга
    setupRatingStars();
    
    // Настройка обработчиков для кнопок показать/скрыть ответы
    setupReplyToggleButtons();
});

// Функция настройки обработчиков для кнопок показать/скрыть ответы
function setupReplyToggleButtons() {
    // Находим все кнопки показать/скрыть ответы по ID
    const toggleButtons = document.querySelectorAll('[id^="replyToggle"]');
    
    toggleButtons.forEach(button => {
        setupReplyToggleButton(button);
    });
    
    console.log('Found toggle buttons:', toggleButtons.length);
}

// Функция для настройки одной кнопки показать/скрыть ответы
function setupReplyToggleButton(button) {
    // Убираем старые обработчики
    button.removeEventListener('click', button._replyToggleHandler);
    
    // Создаем новый обработчик
    button._replyToggleHandler = function(e) {
        e.preventDefault();
        
        // Получаем reviewId из ID кнопки (replyToggle{reviewId})
        const buttonId = this.id;
        const reviewId = buttonId.replace('replyToggle', '');
        
        if (!reviewId) {
            console.error('Could not extract reviewId from button ID:', buttonId);
            return;
        }
        
        // Просто переключаем состояние
        const container = document.getElementById(`repliesContainer${reviewId}`);
        if (container && container.classList.contains('hidden')) {
            toggleReplies(reviewId);
        } else {
            hideReplies(reviewId);
        }
    };
    
    // Добавляем обработчик
    button.addEventListener('click', button._replyToggleHandler);
}

// Функция настройки звездочек рейтинга
function setupRatingStars() {
    const ratingContainers = document.querySelectorAll('[id^="ratingStars"]');
    
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star-btn');
        const input = container.parentElement.querySelector('input[type="hidden"]');
        
        if (stars && input) {
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    const rating = index + 1;
                    input.value = rating;
                    
                    // Обновляем отображение звездочек
                    stars.forEach((s, i) => {
                        if (i < rating) {
                            s.classList.add('text-accent-500');
                            s.classList.remove('text-gray-300', 'dark:text-gray-600');
                        } else {
                            s.classList.remove('text-accent-500');
                            s.classList.add('text-gray-300', 'dark:text-gray-600');
                        }
                    });
                });
                
                star.addEventListener('mouseenter', () => {
                    const rating = index + 1;
                    stars.forEach((s, i) => {
                        if (i < rating) {
                            s.classList.add('text-accent-500');
                            s.classList.remove('text-gray-300', 'dark:text-gray-600');
                        }
                    });
                });
                
                star.addEventListener('mouseleave', () => {
                    const currentRating = parseInt(input.value) || 0;
                    stars.forEach((s, i) => {
                        if (i < currentRating) {
                            s.classList.add('text-accent-500');
                            s.classList.remove('text-gray-300', 'dark:text-gray-600');
                        } else {
                            s.classList.remove('text-accent-500');
                            s.classList.add('text-gray-300', 'dark:text-gray-600');
                        }
                    });
                });
            });
        }
    });
}
