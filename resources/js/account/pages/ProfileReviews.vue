<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-lg font-extrabold tracking-tight">Рецензії</div>
      <div class="mt-1 text-sm text-white/65">Пошук, фільтри та керування рецензіями.</div>
      <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Всього</div><div class="text-base font-extrabold">{{ stats.total }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">З оцінкою</div><div class="text-base font-extrabold">{{ stats.rated }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Сер. оцінка</div><div class="text-base font-extrabold">{{ stats.avgRating }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">За 30 днів</div><div class="text-base font-extrabold">{{ stats.month }}</div></div>
      </div>
    </section>

    <section class="acc-glass rounded-2xl border border-white/10 p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
        <input v-model.trim="searchQuery" type="text" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20 md:col-span-2" placeholder="Пошук по книзі або тексту...">
        <select v-model="ratingFilter" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20">
          <option value="all">Усі оцінки</option>
          <option value="rated">Тільки з оцінкою</option>
          <option value="high">Високі (8+)</option>
          <option value="low">Низькі (до 5)</option>
        </select>
      </div>
      <div class="mt-2 text-xs text-white/60">Знайдено: {{ filteredReviews.length }}</div>
    </section>

    <div class="space-y-3">
      <div v-for="item in filteredReviews" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0 flex items-start gap-3">
            <img :src="resolveBookCover(item.book && item.book.cover)" class="h-20 w-14 rounded object-cover border border-white/10" alt="" @error="onCoverError">
            <div class="min-w-0">
              <div class="font-extrabold truncate">{{ item.book ? item.book.title : 'Рецензія' }}</div>
              <div class="text-xs text-white/60 mt-1 line-clamp-3">{{ previewText(item.content) || 'Без опису' }}</div>
              <div class="mt-2 text-xs text-white/60">{{ item.created_at_human || 'щойно' }}</div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-xs text-white/60">
          <span class="acc-pill">⭐ {{ item.rating || 0 }}</span>
        </div>
      </div>
      <div v-if="!filteredReviews.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Рецензій за поточними фільтрами не знайдено.
      </div>
    </div>

    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="acc-modal-overlay" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal w-full max-w-xl">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-white/70">{{ actionModal.subtitle }}</div>
          <div class="mt-4 flex flex-wrap justify-end gap-2">
            <button class="acc-btn w-full sm:w-auto" @click="actionModal = null">Закрити</button>
            <a v-if="actionModal.editUrl" :href="actionModal.editUrl" class="acc-btn-primary w-full sm:w-auto text-center">Редагувати</a>
            <a v-if="actionModal.viewUrl" :href="actionModal.viewUrl" class="acc-btn w-full sm:w-auto text-center">Переглянути</a>
            <button v-else class="acc-btn w-full sm:w-auto" @click="showUnavailable">Редагування недоступне</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileReviews',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    reviews() {
      return this.dashboard?.recent_reviews || [];
    },
    filteredReviews() {
      const query = String(this.searchQuery || '').toLowerCase().trim();
      return this.reviews.filter((item) => {
        const rating = Number(item?.rating || 0);
        if (this.ratingFilter === 'rated' && !rating) return false;
        if (this.ratingFilter === 'high' && rating < 8) return false;
        if (this.ratingFilter === 'low' && (rating <= 0 || rating > 5)) return false;
        if (!query) return true;
        const hay = `${item?.book?.title || ''} ${this.previewText(item?.content || '')}`.toLowerCase();
        return hay.includes(query);
      });
    },
    stats() {
      const total = this.reviews.length;
      const ratings = this.reviews.map((r) => Number(r?.rating || 0)).filter((v) => v > 0);
      const rated = ratings.length;
      const avgRating = rated ? (ratings.reduce((a, b) => a + b, 0) / rated).toFixed(1) : '0.0';
      const monthAgo = Date.now() - (30 * 24 * 60 * 60 * 1000);
      const month = this.reviews.filter((item) => (Date.parse(item?.created_at || '') || 0) >= monthAgo).length;
      return { total, rated, avgRating, month };
    },
  },
  data() {
    return {
      actionModal: null,
      searchQuery: '',
      ratingFilter: 'all',
    };
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
    previewText(value) {
      if (!value) return '';
      return String(value).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    },
    openAction(item) {
      this.actionModal = {
        title: item?.book?.title || 'Рецензія',
        subtitle: 'Перехід на сторінку редагування рецензії.',
        editUrl: item?.book?.slug && item?.id ? `/books/${item.book.slug}/reviews/${item.id}/edit` : '',
        viewUrl: item?.book?.slug && item?.id ? `/books/${item.book.slug}/reviews/${item.id}` : '',
      };
    },
    showUnavailable() {
      alert('Для цього елемента ще немає окремої сторінки редагування.');
    },
  },
};
</script>

