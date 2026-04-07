@extends('layouts.app')

@section('title', 'Участники сообщества')

@section('main')
<div id="readers-rating-page" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-5 rounded-2xl border border-light-border/50 dark:border-white/10 bg-white/70 dark:bg-[#0b1225]/70 backdrop-blur-xl p-4 sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-light-text-primary dark:text-white">Рейтинг читачів</h1>
                <p class="text-sm text-light-text-secondary dark:text-white/65 mt-1">Асинхронне завантаження з пагінацією та фільтрами.</p>
            </div>
            <button id="rating-reset" type="button" class="self-start sm:self-auto rounded-xl border border-light-border/70 dark:border-white/10 px-3 py-2 text-sm text-light-text-primary dark:text-white hover:bg-light-bg-secondary dark:hover:bg-white/10 transition">Скинути фільтри</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[300px,minmax(0,1fr)] gap-4">
        <aside class="space-y-4 lg:sticky lg:top-24 h-fit">
            <div class="rounded-2xl border border-light-border/50 dark:border-white/10 bg-white/70 dark:bg-[#0b1225]/70 backdrop-blur-xl p-4">
                <div class="text-sm font-bold text-light-text-primary dark:text-white mb-3">Фільтри</div>
                <div class="space-y-3">
                    <input id="flt-search" type="text" placeholder="Ім'я або @username" class="w-full rounded-xl border border-light-border/70 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-light-text-primary dark:text-white outline-none">
                    <select id="flt-rating" class="w-full rounded-xl border border-light-border/70 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-light-text-primary dark:text-white outline-none">
                        <option value="">Всі рейтинги</option>
                        <option value="9_stars">9+ зірок</option>
                        <option value="7_stars">7+ зірок</option>
                        <option value="5_stars">5+ зірок</option>
                    </select>
                    <select id="flt-activity" class="w-full rounded-xl border border-light-border/70 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-light-text-primary dark:text-white outline-none">
                        <option value="">Вся активність</option>
                        <option value="most_reviews">Більше рецензій</option>
                        <option value="most_quotes">Більше цитат</option>
                        <option value="most_discussions">Більше обговорень</option>
                        <option value="most_books_read">Більше прочитаних книг</option>
                    </select>
                    <select id="flt-sort" class="w-full rounded-xl border border-light-border/70 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-light-text-primary dark:text-white outline-none">
                        <option value="rating">Сортувати за рейтингом</option>
                        <option value="name">За ім'ям</option>
                        <option value="username">За юзернеймом</option>
                        <option value="reviews">За рецензіями</option>
                        <option value="quotes">За цитатами</option>
                        <option value="books">За прочитаними</option>
                    </select>
                    <button id="rating-apply" type="button" class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-accent-500 text-white px-3 py-2 text-sm font-semibold hover:opacity-95 transition">Застосувати</button>
                </div>
            </div>

            <div class="rounded-2xl border border-light-border/50 dark:border-white/10 bg-white/70 dark:bg-[#0b1225]/70 backdrop-blur-xl p-4">
                <div class="text-sm font-bold text-light-text-primary dark:text-white mb-3">Статистика</div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-light-text-secondary dark:text-white/60">Всього</span>
                        <span id="st-total" class="font-bold text-light-text-primary dark:text-white">{{ $stats['total_users'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-light-text-secondary dark:text-white/60">Активних</span>
                        <span id="st-active" class="font-bold text-light-text-primary dark:text-white">{{ $stats['active_users'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-light-text-secondary dark:text-white/60">Знайдено</span>
                        <span id="st-found" class="font-bold text-light-text-primary dark:text-white">{{ $users->total() ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </aside>

        <section>
            <div id="rating-loading" class="hidden rounded-2xl border border-light-border/50 dark:border-white/10 bg-white/70 dark:bg-[#0b1225]/70 backdrop-blur-xl p-4 text-sm text-light-text-secondary dark:text-white/65">
                Завантаження...
            </div>
            <div id="rating-empty" class="hidden rounded-2xl border border-light-border/50 dark:border-white/10 bg-white/70 dark:bg-[#0b1225]/70 backdrop-blur-xl p-6 text-center text-light-text-secondary dark:text-white/65">
                Користувачів не знайдено.
            </div>
            <div id="rating-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3"></div>
            <div class="mt-5 flex items-center justify-center gap-2">
                <button id="rating-prev" type="button" class="rounded-xl border border-light-border/70 dark:border-white/10 px-3 py-2 text-sm text-light-text-primary dark:text-white hover:bg-light-bg-secondary dark:hover:bg-white/10 transition">Назад</button>
                <div id="rating-page-label" class="text-sm text-light-text-secondary dark:text-white/65">1 / 1</div>
                <button id="rating-next" type="button" class="rounded-xl border border-light-border/70 dark:border-white/10 px-3 py-2 text-sm text-light-text-primary dark:text-white hover:bg-light-bg-secondary dark:hover:bg-white/10 transition">Вперед</button>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const state = {
        page: 1,
        lastPage: 1,
        filters: {
            search: @json(request('search', '')),
            rating_filter: @json(request('rating_filter', '')),
            activity_filter: @json(request('activity_filter', '')),
            sort: @json(request('sort', 'rating')),
        },
    };

    const els = {
        search: document.getElementById('flt-search'),
        rating: document.getElementById('flt-rating'),
        activity: document.getElementById('flt-activity'),
        sort: document.getElementById('flt-sort'),
        apply: document.getElementById('rating-apply'),
        reset: document.getElementById('rating-reset'),
        loading: document.getElementById('rating-loading'),
        empty: document.getElementById('rating-empty'),
        grid: document.getElementById('rating-grid'),
        prev: document.getElementById('rating-prev'),
        next: document.getElementById('rating-next'),
        pageLabel: document.getElementById('rating-page-label'),
        stTotal: document.getElementById('st-total'),
        stActive: document.getElementById('st-active'),
        stFound: document.getElementById('st-found'),
    };

    if (!els.grid) return;

    els.search.value = state.filters.search || '';
    els.rating.value = state.filters.rating_filter || '';
    els.activity.value = state.filters.activity_filter || '';
    els.sort.value = state.filters.sort || 'rating';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function updateQueryString() {
        const url = new URL(window.location.href);
        url.searchParams.set('page', String(state.page));
        Object.entries(state.filters).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.replaceState({}, '', url.toString());
    }

    async function requestJson(url, params) {
        const query = new URLSearchParams(params).toString();
        const target = query ? `${url}?${query}` : url;
        const response = await fetch(target, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            let errorMessage = 'Помилка завантаження рейтингу.';
            try {
                const payload = await response.json();
                errorMessage = payload?.message || errorMessage;
            } catch (_) {
                // ignore json parsing error and keep fallback message
            }
            throw new Error(errorMessage);
        }

        return response.json();
    }

    async function loadUsers() {
        els.loading.classList.remove('hidden');
        els.empty.classList.add('hidden');
        try {
            const data = await requestJson("{{ route('users.index') }}", {
                page: state.page,
                per_page: 18,
                ...state.filters,
            });

            const users = data?.users || [];
            const pagination = data?.pagination || {};
            const stats = data?.stats || {};
            state.lastPage = Number(pagination.last_page || 1);
            state.page = Number(pagination.current_page || 1);

            els.grid.innerHTML = users.map((user) => `
                <a href="${escapeHtml(user.profile_url)}" class="group rounded-2xl border border-light-border/50 dark:border-white/10 bg-white/75 dark:bg-[#0b1225]/75 backdrop-blur-xl p-4 hover:shadow-xl transition">
                    <div class="flex items-center gap-3">
                        <img src="${escapeHtml(user.avatar)}" alt="${escapeHtml(user.name)}" class="h-14 w-14 rounded-xl object-cover border border-light-border/50 dark:border-white/10" />
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-light-text-primary dark:text-white truncate">${escapeHtml(user.name)}</div>
                            <div class="text-xs text-light-text-secondary dark:text-white/60 truncate">@${escapeHtml(user.username)}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-light-text-secondary dark:text-white/55">Рейтинг</div>
                            <div class="text-base font-extrabold text-amber-500 dark:text-amber-300">
                                <i class="fas fa-star mr-1"></i>${escapeHtml(user.rating_score)}
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-lg bg-light-bg-secondary/70 dark:bg-white/5 px-2 py-1.5 text-light-text-secondary dark:text-white/65">Рецензій: <span class="font-bold text-light-text-primary dark:text-white">${user.main_reviews_count}</span></div>
                        <div class="rounded-lg bg-light-bg-secondary/70 dark:bg-white/5 px-2 py-1.5 text-light-text-secondary dark:text-white/65">Цитат: <span class="font-bold text-light-text-primary dark:text-white">${user.public_quotes_count}</span></div>
                        <div class="rounded-lg bg-light-bg-secondary/70 dark:bg-white/5 px-2 py-1.5 text-light-text-secondary dark:text-white/65">Книг: <span class="font-bold text-light-text-primary dark:text-white">${user.read_books_count}</span></div>
                        <div class="rounded-lg bg-light-bg-secondary/70 dark:bg-white/5 px-2 py-1.5 text-light-text-secondary dark:text-white/65">Обгов.: <span class="font-bold text-light-text-primary dark:text-white">${user.discussions_count}</span></div>
                    </div>
                </a>
            `).join('');

            if (!users.length) {
                els.empty.classList.remove('hidden');
            }

            els.pageLabel.textContent = `${state.page} / ${state.lastPage}`;
            els.prev.disabled = state.page <= 1;
            els.next.disabled = state.page >= state.lastPage;
            els.stTotal.textContent = String(stats.total_users ?? 0);
            els.stActive.textContent = String(stats.active_users ?? 0);
            els.stFound.textContent = String(stats.found_users ?? 0);
            updateQueryString();
        } catch (e) {
            els.empty.classList.remove('hidden');
            els.empty.textContent = e?.message || 'Помилка завантаження рейтингу.';
        } finally {
            els.loading.classList.add('hidden');
        }
    }

    els.apply.addEventListener('click', () => {
        state.filters.search = (els.search.value || '').trim();
        state.filters.rating_filter = els.rating.value || '';
        state.filters.activity_filter = els.activity.value || '';
        state.filters.sort = els.sort.value || 'rating';
        state.page = 1;
        loadUsers();
    });

    els.reset.addEventListener('click', () => {
        state.filters = { search: '', rating_filter: '', activity_filter: '', sort: 'rating' };
        state.page = 1;
        els.search.value = '';
        els.rating.value = '';
        els.activity.value = '';
        els.sort.value = 'rating';
        loadUsers();
    });

    els.prev.addEventListener('click', () => {
        if (state.page <= 1) return;
        state.page -= 1;
        loadUsers();
    });

    els.next.addEventListener('click', () => {
        if (state.page >= state.lastPage) return;
        state.page += 1;
        loadUsers();
    });

    els.search.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        els.apply.click();
    });

    loadUsers();
})();
</script>
@endpush