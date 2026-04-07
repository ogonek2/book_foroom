<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-lg font-extrabold">Збережене</div>
      <div class="mt-1 text-xs text-white/60">Рецензії та цитати, які ви додали в збережене</div>
      <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <div class="text-white/60">Всього</div>
          <div class="text-base font-extrabold">{{ favoriteItems.length }}</div>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <div class="text-white/60">Рецензії</div>
          <div class="text-base font-extrabold">{{ countsByType.review }}</div>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <div class="text-white/60">Цитати</div>
          <div class="text-base font-extrabold">{{ countsByType.quote }}</div>
        </div>
      </div>
    </section>

    <div class="grid grid-cols-1 gap-4 lg:items-start lg:grid-cols-[260px,minmax(0,1fr)]">
      <aside class="acc-glass rounded-2xl border border-white/10 p-3 lg:sticky lg:top-24 lg:self-start h-fit">
        <div class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500 dark:text-white/40 px-2 pb-2">Тип збереженого</div>
        <div class="space-y-2">
          <button v-for="type in favoriteTypes" :key="type.id" type="button" class="acc-btn w-full justify-between" :class="activeType === type.id ? 'acc-settings-nav-active' : ''" @click="activeType = type.id">
            <span>{{ type.label }}</span>
            <span class="rounded-md bg-white/10 px-2 py-0.5 text-[11px]">{{ type.count }}</span>
          </button>
        </div>
      </aside>

      <section class="space-y-4 min-w-0">
        <div class="acc-glass rounded-2xl border border-white/10 p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <input
              v-model.trim="searchQuery"
              type="text"
              class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20"
              placeholder="Пошук по назві, тексту..."
            >
            <select
              v-model="dateFilter"
              class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20"
            >
              <option value="all">Усі дати</option>
              <option value="7">За 7 днів</option>
              <option value="30">За 30 днів</option>
              <option value="90">За 90 днів</option>
            </select>
          </div>
          <div class="mt-2 text-xs text-white/60">Знайдено: {{ filteredFavorites.length }}</div>
        </div>

        <div v-if="filteredFavorites.length" class="space-y-3">
          <article v-for="item in filteredFavorites" :key="`${item.type}-${item.id}`" class="acc-glass rounded-2xl border border-white/10 p-4">
            <div class="flex items-start gap-4">
              <img :src="resolveBookCover(item.book_cover)" class="h-24 w-16 rounded-md object-cover border border-white/10 shrink-0" alt="" @error="onCoverError">
              <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="acc-pill">{{ item.type === 'review' ? 'Рецензія' : 'Цитата' }}</span>
                  <span class="text-xs text-white/55">{{ item.created_at_human || 'щойно' }}</span>
                </div>
                <div class="mt-2 text-base font-extrabold leading-tight">{{ item.book_title || (item.type === 'review' ? 'Рецензія' : 'Цитата') }}</div>
                <div class="mt-2 text-sm text-white/70 line-clamp-3">{{ item.content || 'Без тексту' }}</div>
                <div class="mt-3 flex flex-wrap gap-2">
                  <a v-if="item.editUrl" :href="item.editUrl" class="acc-btn-primary">Перейти</a>
                  <button type="button" class="acc-btn" @click="openAction(item.raw, item.type)">Керування</button>
                </div>
              </div>
            </div>
          </article>
        </div>
        <div v-else class="acc-glass rounded-2xl border border-white/10 p-4 text-sm text-white/55">
          Нічого не знайдено за поточними фільтрами.
        </div>
      </section>
    </div>

    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="acc-modal-overlay" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-md">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-white/70">{{ actionModal.subtitle }}</div>
          <div class="mt-4 flex justify-end gap-2">
            <button class="acc-btn" @click="actionModal = null">Закрити</button>
            <a v-if="actionModal.editUrl" :href="actionModal.editUrl" class="acc-btn-primary">Перейти</a>
            <button v-else class="acc-btn" @click="showUnavailable">Редагування недоступне</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  props: {
    dashboard: { type: Object, default: () => ({}) },
    isOwner: { type: Boolean, default: false },
  },
  data() {
    return {
      actionModal: null,
      activeType: 'all',
      searchQuery: '',
      dateFilter: 'all',
    };
  },
  computed: {
    favoriteQuotes() {
      return this.isOwner ? (this.dashboard?.favorite_quotes || []) : [];
    },
    favoriteReviews() {
      return this.isOwner ? (this.dashboard?.favorite_reviews || []) : [];
    },
    countsByType() {
      return {
        review: this.favoriteReviews.length,
        quote: this.favoriteQuotes.length,
      };
    },
    favoriteTypes() {
      return [
        { id: 'all', label: 'Всі', count: this.favoriteItems.length },
        { id: 'review', label: 'Рецензії', count: this.countsByType.review },
        { id: 'quote', label: 'Цитати', count: this.countsByType.quote },
      ];
    },
    favoriteItems() {
      const reviews = this.favoriteReviews.map((item) => ({
        ...item,
        type: 'review',
        editUrl: item?.book_slug ? `/books/${item.book_slug}/reviews/${item.id}` : '',
        raw: item,
      }));
      const quotes = this.favoriteQuotes.map((item) => ({
        ...item,
        type: 'quote',
        editUrl: item?.book_slug ? `/books/${item.book_slug}` : '',
        raw: item,
      }));
      return [...reviews, ...quotes].sort((a, b) => {
        const at = Date.parse(a?.created_at || '') || 0;
        const bt = Date.parse(b?.created_at || '') || 0;
        return bt - at;
      });
    },
    filteredFavorites() {
      const query = String(this.searchQuery || '').toLowerCase().trim();
      const days = Number(this.dateFilter || 0);
      const minTs = days > 0 ? Date.now() - (days * 24 * 60 * 60 * 1000) : 0;
      return this.favoriteItems.filter((item) => {
        if (this.activeType !== 'all' && item.type !== this.activeType) return false;
        if (minTs > 0) {
          const ts = Date.parse(item?.created_at || '') || 0;
          if (!ts || ts < minTs) return false;
        }
        if (!query) return true;
        return [item?.book_title, item?.content, item.type === 'review' ? 'рецензія' : 'цитата']
          .filter(Boolean)
          .some((value) => String(value).toLowerCase().includes(query));
      });
    },
  },
  methods: {
    resolveBookCover(cover) {
      return cover || '/images/placeholders/book-cover.svg';
    },
    onCoverError(event) {
      const fallback = '/images/placeholders/book-cover.svg';
      if (event?.target && !String(event.target.src || '').includes(fallback)) {
        event.target.src = fallback;
      }
    },
    openAction(item, type) {
      const isReview = type === 'review';
      this.actionModal = {
        title: item?.book_title || (isReview ? 'Рецензія' : 'Цитата'),
        subtitle: isReview ? 'Перейти до рецензії.' : 'Перейти до сторінки книги.',
        editUrl: isReview ? `/books/${item.book_slug}/reviews/${item.id}` : `/books/${item.book_slug}`,
      };
    },
    showUnavailable() {
      alert('Для цього елемента немає доступного редагування.');
    },
  },
};
</script>
