<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-lg font-extrabold tracking-tight">Факти</div>
      <div class="mt-1 text-sm text-white/65">Факти, які ви додали до книг.</div>
      <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Всього</div><div class="text-base font-extrabold">{{ stats.total }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">За 30 днів</div><div class="text-base font-extrabold">{{ stats.month }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Лайків</div><div class="text-base font-extrabold">{{ stats.likes }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Книг</div><div class="text-base font-extrabold">{{ stats.books }}</div></div>
      </div>
    </section>

    <section class="acc-glass rounded-2xl border border-white/10 p-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <input v-model.trim="searchQuery" type="text" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20" placeholder="Пошук по книзі або тексту факту...">
        <select v-model="dateFilter" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20">
          <option value="all">Усі дати</option>
          <option value="7">За 7 днів</option>
          <option value="30">За 30 днів</option>
          <option value="90">За 90 днів</option>
        </select>
      </div>
      <div class="mt-2 text-xs text-white/60">Знайдено: {{ filteredFacts.length }}</div>
    </section>

    <div class="space-y-3">
      <div v-for="item in filteredFacts" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0 flex items-start gap-3">
            <img :src="resolveBookCover(item.book && item.book.cover)" class="h-14 w-10 rounded object-cover border border-white/10" alt="" @error="onCoverError">
            <div class="min-w-0">
              <div class="font-extrabold truncate">{{ item.book ? item.book.title : 'Факт' }}</div>
              <div class="text-xs text-white/60 mt-1 line-clamp-3">{{ previewText(item.content) || 'Без тексту' }}</div>
              <div class="mt-2 text-xs text-white/60">{{ item.created_at_human || 'щойно' }}</div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-xs text-white/60">
          <span class="acc-pill">👍 {{ item.likes_count || 0 }}</span>
        </div>
      </div>
      <div v-if="!filteredFacts.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Фактів за поточними фільтрами не знайдено.
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
            <a v-if="actionModal.viewUrl" :href="actionModal.viewUrl" class="acc-btn-primary w-full sm:w-auto text-center">Перейти</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileFacts',
  props: {
    dashboard: { type: Object, default: null },
  },
  data() {
    return {
      actionModal: null,
      searchQuery: '',
      dateFilter: 'all',
    };
  },
  computed: {
    facts() {
      return this.dashboard?.recent_facts || [];
    },
    filteredFacts() {
      const query = String(this.searchQuery || '').toLowerCase().trim();
      const days = Number(this.dateFilter || 0);
      const minTs = days > 0 ? Date.now() - (days * 24 * 60 * 60 * 1000) : 0;
      return this.facts.filter((item) => {
        if (minTs > 0) {
          const ts = Date.parse(item?.created_at || '') || 0;
          if (!ts || ts < minTs) return false;
        }
        if (!query) return true;
        const hay = `${item?.book?.title || ''} ${this.previewText(item?.content || '')}`.toLowerCase();
        return hay.includes(query);
      });
    },
    stats() {
      const total = this.facts.length;
      const monthAgo = Date.now() - (30 * 24 * 60 * 60 * 1000);
      const month = this.facts.filter((item) => (Date.parse(item?.created_at || '') || 0) >= monthAgo).length;
      const likes = this.facts.reduce((sum, item) => sum + Number(item?.likes_count || 0), 0);
      const books = new Set(this.facts.map((item) => item?.book?.slug).filter(Boolean)).size;
      return { total, month, likes, books };
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
    previewText(value) {
      if (!value) return '';
      return String(value).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    },
    openAction(item) {
      this.actionModal = {
        title: item?.book?.title || 'Факт',
        subtitle: 'Перехід до сторінки фактів книги.',
        viewUrl: item?.book?.slug ? `/books/${item.book.slug}/facts` : '',
      };
    },
  },
};
</script>
