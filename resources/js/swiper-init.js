/**
 * Swiper initialization for reviews slider
 */
let reviewsSwiperRetryCount = 0;
const MAX_RETRIES = 10;

export function initReviewsSwiper() {
    // Wait for Swiper library to be loaded
    if (typeof Swiper === 'undefined') {
        reviewsSwiperRetryCount++;
        if (reviewsSwiperRetryCount < MAX_RETRIES) {
            // Retry after a short delay
            setTimeout(initReviewsSwiper, 300);
        } else {
            console.error('Swiper library failed to load after multiple retries');
        }
        return null;
    }
    
    reviewsSwiperRetryCount = 0;

    const swiperContainer = document.querySelector('.reviews-swiper');
    if (!swiperContainer) {
        // Container not found, might not be on this page
        return null;
    }

    // Check if swiper is already initialized
    if (swiperContainer.swiper) {
        console.log('Reviews Swiper already initialized');
        return swiperContainer.swiper;
    }

    try {
        const reviewsSwiper = new Swiper('.reviews-swiper', {
            slidesPerView: 1,
            spaceBetween: 16,
            loop: false,
            // Touch/Swipe support - основные настройки для свайпа
            touchEventsTarget: 'container',
            allowTouchMove: true,
            touchRatio: 1,
            touchAngle: 45,
            grabCursor: true,
            // Настройки для более плавного свайпа
            resistance: true,
            resistanceRatio: 0.85,
            // Navigation - кнопки назад/вперед
            navigation: {
                nextEl: '.reviews-slider-next',
                prevEl: '.reviews-slider-prev',
                disabledClass: 'swiper-button-disabled',
                hideOnClick: false,
            },
            // Keyboard control - стрелки на клавиатуре
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            // Mouse wheel control (опционально)
            mousewheel: {
                forceToAxis: true,
                sensitivity: 1,
                releaseOnEdges: false,
            },
            // Адаптивные настройки - минимум 3 блока на десктопе
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                1280: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
            },
            // События для отладки
            on: {
                init: function() {
                    console.log('Reviews Swiper initialized successfully');
                },
                slideChange: function() {
                    // Обновление состояния кнопок происходит автоматически
                },
            },
        });

        console.log('Reviews Swiper created:', reviewsSwiper);
        return reviewsSwiper;
    } catch (error) {
        console.error('Error initializing Reviews Swiper:', error);
        return null;
    }
}

/**
 * Swiper initialization for featured books slider
 */
let featuredBooksSwiperRetryCount = 0;
const MAX_RETRIES_FEATURED = 10;

export function initFeaturedBooksSwiper() {
    // Wait for Swiper library to be loaded
    if (typeof Swiper === 'undefined') {
        featuredBooksSwiperRetryCount++;
        if (featuredBooksSwiperRetryCount < MAX_RETRIES_FEATURED) {
            // Retry after a short delay
            setTimeout(initFeaturedBooksSwiper, 300);
        } else {
            console.error('Swiper library failed to load after multiple retries');
        }
        return null;
    }
    
    featuredBooksSwiperRetryCount = 0;

    const swiperContainer = document.querySelector('.featured-books-swiper');
    if (!swiperContainer) {
        // Container not found, might not be on this page
        return null;
    }

    // Check if swiper is already initialized
    if (swiperContainer.swiper) {
        console.log('Featured Books Swiper already initialized');
        return swiperContainer.swiper;
    }

    try {
        const featuredBooksSwiper = new Swiper('.featured-books-swiper', {
            slidesPerView: 1,
            spaceBetween: 16,
            loop: false,
            // Touch/Swipe support - основные настройки для свайпа
            touchEventsTarget: 'container',
            allowTouchMove: true,
            touchRatio: 1,
            touchAngle: 45,
            grabCursor: true,
            // Настройки для более плавного свайпа
            resistance: true,
            resistanceRatio: 0.85,
            // Navigation - кнопки назад/вперед
            navigation: {
                nextEl: '.featured-books-next',
                prevEl: '.featured-books-prev',
                disabledClass: 'swiper-button-disabled',
                hideOnClick: false,
            },
            // Keyboard control - стрелки на клавиатуре
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            // Mouse wheel control (опционально)
            mousewheel: {
                forceToAxis: true,
                sensitivity: 1,
                releaseOnEdges: false,
            },
            // Адаптивные настройки
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                1280: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
            },
            // События для отладки
            on: {
                init: function() {
                    console.log('Featured Books Swiper initialized successfully');
                },
                slideChange: function() {
                    // Обновление состояния кнопок происходит автоматически
                },
            },
        });

        console.log('Featured Books Swiper created:', featuredBooksSwiper);
        return featuredBooksSwiper;
    } catch (error) {
        console.error('Error initializing Featured Books Swiper:', error);
        return null;
    }
}

