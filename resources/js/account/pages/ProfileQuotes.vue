<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-lg font-extrabold tracking-tight">Цитати</div>
      <div class="mt-1 text-sm text-white/65">Пошук, фільтри та керування цитатами.</div>
      <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Всього</div><div class="text-base font-extrabold">{{ stats.total }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">За 30 днів</div><div class="text-base font-extrabold">{{ stats.month }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Довгі (120+)</div><div class="text-base font-extrabold">{{ stats.long }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Книг у цитатах</div><div class="text-base font-extrabold">{{ stats.books }}</div></div>
      </div>
    </section>

    <section class="acc-glass rounded-2xl border border-white/10 p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
        <input v-model.trim="searchQuery" type="text" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20 md:col-span-2" placeholder="Пошук по книзі або тексту...">
        <select v-model="lengthFilter" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20">
          <option value="all">Усі цитати</option>
          <option value="short">Короткі (&lt; 120)</option>
          <option value="long">Довгі (120+)</option>
        </select>
      </div>
      <div class="mt-2 text-xs text-white/60">Знайдено: {{ filteredQuotes.length }}</div>
    </section>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
      <div v-for="item in filteredQuotes" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="text-xs text-white/55 font-semibold">{{ item.created_at_human || 'щойно' }}</div>
        <div class="mt-2 flex items-start gap-3">
          <img :src="resolveBookCover(item.book && item.book.cover)" class="h-14 w-10 rounded object-cover border border-white/10" alt="" @error="onCoverError">
          <button type="button" class="acc-btn !px-2 !py-1 text-xs ml-auto" @click="openAction(item)">Керування</button>
        </div>
        <div class="mt-2 text-sm leading-relaxed text-white/85">
          "{{ previewText(item.content) || 'Без тексту' }}"
        </div>
        <div class="mt-3 flex items-center justify-between gap-2">
          <span class="acc-pill">Книга: {{ item.book ? item.book.title : '—' }}</span>
        </div>
      </div>
      <div v-if="!filteredQuotes.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Цитат за поточними фільтрами не знайдено.
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
            <button class="acc-btn w-full sm:w-auto" @click="showUnavailable">Редагування недоступне</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileQuotes',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    quotes() {
      return this.dashboard?.recent_quotes || [];
    },
    filteredQuotes() {
      const query = String(this.searchQuery || '').toLowerCase().trim();
      return this.quotes.filter((item) => {
        const text = this.previewText(item?.content || '');
        const len = text.length;
        if (this.lengthFilter === 'short' && len >= 120) return false;
        if (this.lengthFilter === 'long' && len < 120) return false;
        if (!query) return true;
        const hay = `${item?.book?.title || ''} ${text}`.toLowerCase();
        return hay.includes(query);
      });
    },
    stats() {
      const total = this.quotes.length;
      const monthAgo = Date.now() - (30 * 24 * 60 * 60 * 1000);
      const month = this.quotes.filter((item) => (Date.parse(item?.created_at || '') || 0) >= monthAgo).length;
      const long = this.quotes.filter((item) => this.previewText(item?.content || '').length >= 120).length;
      const books = new Set(this.quotes.map((item) => item?.book?.slug).filter(Boolean)).size;
      return { total, month, long, books };
    },
  },
  data() {
    return {
      actionModal: null,
      searchQuery: '',
      lengthFilter: 'all',
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
        title: item?.book?.title || 'Цитата',
        subtitle: 'Перехід до сторінки книги з цитатами.',
        viewUrl: item?.book?.slug ? `/books/${item.book.slug}/quotes` : '',
      };
    },
    showUnavailable() {
      alert('Редагування цієї цитати з профілю поки недоступне.');
    },
  },
};
</script>

