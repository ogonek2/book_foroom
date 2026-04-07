<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-lg font-extrabold tracking-tight">Колекції</div>
          <div class="mt-1 text-sm text-white/65">Пошук і керування публічними/приватними добірками.</div>
        </div>
        <a href="/libraries/create" class="acc-btn-primary !px-3 !py-1.5">Нова колекція</a>
      </div>
    </section>

    <div class="grid grid-cols-1 gap-4 lg:items-start lg:grid-cols-[260px,minmax(0,1fr)]">
      <aside class="acc-glass rounded-2xl border border-white/10 p-3 lg:sticky lg:top-24 lg:self-start h-fit">
        <div class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500 dark:text-white/40 px-2 pb-2">Тип бібліотек</div>
        <div class="space-y-2">
          <button v-for="item in filterItems" :key="item.id" class="acc-btn w-full justify-between" :class="typeFilter === item.id ? 'acc-settings-nav-active' : ''" type="button" @click="typeFilter = item.id">
            <span>{{ item.label }}</span>
            <span class="rounded-md bg-white/10 px-2 py-0.5 text-[11px]">{{ item.count }}</span>
          </button>
        </div>
      </aside>

      <section class="space-y-4 min-w-0">
        <div class="acc-glass rounded-2xl border border-white/10 p-4">
          <input
            v-model.trim="searchQuery"
            type="text"
            class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20"
            placeholder="Пошук по назві добірки, опису або книгам у добірці..."
          >
          <div class="mt-2 text-xs text-white/60">Знайдено: {{ filteredCollections.length }}</div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
          <article v-for="item in filteredCollections" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="grid grid-cols-3 gap-1 h-20 mb-3">
              <template v-if="item.preview_covers && item.preview_covers.length">
                <img v-for="(cover, idx) in item.preview_covers.slice(0,3)" :key="`pc-${item.id}-${idx}`" :src="resolveBookCover(cover)" class="h-20 w-full rounded object-cover border border-white/10" alt="" @error="onCoverError">
                <div v-for="idx in Math.max(0, 3 - item.preview_covers.slice(0,3).length)" :key="`ph-${item.id}-${idx}`" class="h-20 rounded bg-white/10 border border-white/10" />
              </template>
              <template v-else>
                <div v-for="idx in 3" :key="`placeholder-${item.id}-${idx}`" class="h-20 rounded bg-white/10 border border-white/10" />
              </template>
            </div>
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <div class="font-extrabold truncate">{{ item.name }}</div>
                <div class="mt-1 text-xs text-white/55">{{ item.created_at_human || 'щойно' }}</div>
              </div>
              <span class="acc-pill">{{ item.is_private ? 'Приватна' : 'Публічна' }}</span>
            </div>
            <div class="mt-2 text-xs text-white/70 line-clamp-3">{{ item.description || 'Без опису' }}</div>
            <div class="mt-2 text-xs text-white/60 line-clamp-2">
              {{ previewTitles(item) }}
            </div>
            <div class="mt-3 flex items-center justify-between gap-2">
              <span class="acc-pill">{{ item.books_count || 0 }} книг</span>
              <div class="flex items-center gap-2">
                <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
                <button type="button" class="acc-btn !px-2 !py-1 text-xs !border-red-400/40 !text-red-200" @click="removeCollection(item)">Видалити</button>
              </div>
            </div>
          </article>

          <div v-if="!filteredCollections.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65 sm:col-span-2 xl:col-span-3">
            Колекції за поточними фільтрами не знайдено.
          </div>
        </div>
      </section>
    </div>

    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="acc-modal-overlay" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-2xl">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-white/70">Додавайте або видаляйте книги з колекції.</div>

          <div class="mt-4 acc-modal-subpanel">
            <div class="flex gap-2">
              <input v-model="bookQuery" type="text" class="acc-modal-input" placeholder="Пошук книги за назвою...">
              <button class="acc-btn" @click="searchBooks">Пошук</button>
            </div>
            <div v-if="searchResults.length" class="mt-3 max-h-40 overflow-auto space-y-2">
              <div v-for="book in searchResults" :key="`search-${book.id}`" class="flex items-center justify-between rounded-lg border border-white/10 px-2 py-1">
                <div class="flex items-center gap-2 min-w-0">
                  <img :src="resolveBookCover(book.cover)" class="h-10 w-7 rounded object-cover border border-white/10" alt="" @error="onCoverError">
                  <span class="truncate text-sm">{{ book.title }}</span>
                </div>
                <button class="acc-btn !px-2 !py-1 text-xs" @click="addBook(book)">Додати</button>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <div class="mb-2 text-xs font-bold text-slate-700 dark:text-white/70">Книги у колекції</div>
            <div v-if="collectionBooks.length" class="max-h-56 overflow-auto space-y-2">
              <div v-for="book in collectionBooks" :key="`in-col-${book.id}`" class="flex items-center justify-between rounded-lg border border-white/10 px-2 py-1">
                <div class="flex items-center gap-2 min-w-0">
                  <img :src="resolveBookCover(book.cover)" class="h-10 w-7 rounded object-cover border border-white/10" alt="" @error="onCoverError">
                  <span class="truncate text-sm">{{ book.title }}</span>
                </div>
                <button class="acc-btn !px-2 !py-1 text-xs !border-red-400/40 !text-red-200" @click="removeBook(book)">Видалити</button>
              </div>
            </div>
            <div v-else class="text-sm text-slate-500 dark:text-white/50">У колекції ще немає книг.</div>
          </div>

          <div class="mt-4 flex justify-end gap-2">
            <a v-if="actionModal.editInfoUrl" :href="actionModal.editInfoUrl" class="acc-btn-primary">Редагувати інформацію</a>
            <button class="acc-btn" @click="actionModal = null">Закрити</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  name: 'ProfileCollections',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    collections() {
      return this.collectionsLocal;
    },
    filteredCollections() {
      const query = String(this.searchQuery || '').toLowerCase().trim();
      return this.collections.filter((item) => {
        if (this.typeFilter === 'public' && item.is_private) return false;
        if (this.typeFilter === 'private' && !item.is_private) return false;
        if (!query) return true;
        const hay = [
          item?.name || '',
          item?.description || '',
          ...(item?.preview_titles || []),
        ].join(' ').toLowerCase();
        return hay.includes(query);
      });
    },
    filterItems() {
      const total = this.collections.length;
      const publicCount = this.collections.filter((item) => !item?.is_private).length;
      const privateCount = this.collections.filter((item) => item?.is_private).length;
      return [
        { id: 'all', label: 'Всі', count: total },
        { id: 'public', label: 'Публічні', count: publicCount },
        { id: 'private', label: 'Приватні', count: privateCount },
      ];
    },
    currentCollectionId() {
      return this.actionModal?.id || null;
    },
  },
  data() {
    return {
      actionModal: null,
      collectionsLocal: [],
      searchQuery: '',
      typeFilter: 'all',
      collectionBooks: [],
      bookQuery: '',
      searchResults: [],
    };
  },
  watch: {
    dashboard: {
      immediate: true,
      deep: true,
      handler() {
        this.collectionsLocal = [...(this.dashboard?.collections || [])];
      },
    },
  },
  methods: {
    resolveBookCover(cover) {
      return cover || '/images/placeholders/book-cover.svg';
    },
    previewTitles(item) {
      const titles = Array.isArray(item?.preview_titles) ? item.preview_titles : [];
      return titles.length ? `Книги: ${titles.join(', ')}` : 'Книги: —';
    },
    onCoverError(event) {
      const fallback = '/images/placeholders/book-cover.svg';
      if (event?.target && !String(event.target.src || '').includes(fallback)) {
        event.target.src = fallback;
      }
    },
    async openAction(item) {
      this.actionModal = {
        id: item?.id || null,
        title: item?.name || 'Колекція',
        editInfoUrl: item?.slug ? `/libraries/${item.slug}/edit` : '',
      };
      this.bookQuery = '';
      this.searchResults = [];
      await this.loadCollectionBooks(item.id);
    },
    async loadCollectionBooks(collectionId) {
      try {
        const { data } = await axios.get(`/api/account/collections/${collectionId}/books`);
        this.collectionBooks = data?.books || [];
      } catch (e) {
        this.collectionBooks = [];
        alert('Не вдалося завантажити книги колекції.');
      }
    },
    async searchBooks() {
      if (!this.actionModal || !this.bookQuery.trim()) {
        this.searchResults = [];
        return;
      }
      try {
        const { data } = await axios.get('/api/account/books/search', {
          params: { q: this.bookQuery.trim() },
        });
        this.searchResults = data?.books || [];
      } catch (e) {
        this.searchResults = [];
      }
    },
    async addBook(book) {
      if (!this.actionModal) return;
      try {
        await axios.post(`/api/account/collections/${this.currentCollectionId}/books`, {
          book_slug: book.slug,
        });
        await this.loadCollectionBooks(this.currentCollectionId);
      } catch (e) {
        alert(e?.response?.data?.message || 'Не вдалося додати книгу.');
      }
    },
    async removeBook(book) {
      if (!this.actionModal) return;
      try {
        await axios.delete(`/api/account/collections/${this.currentCollectionId}/books/${book.id}`);
        await this.loadCollectionBooks(this.currentCollectionId);
      } catch (e) {
        alert(e?.response?.data?.message || 'Не вдалося видалити книгу.');
      }
    },
    async removeCollection(item) {
      if (!window.confirm('Видалити цю колекцію? Дію неможливо скасувати.')) return;
      try {
        await axios.delete(`/api/account/collections/${item.id}`);
        this.collectionsLocal = this.collectionsLocal.filter((x) => Number(x.id) !== Number(item.id));
        if (this.actionModal?.id === item.id) {
          this.actionModal = null;
        }
      } catch (e) {
        alert(e?.response?.data?.message || 'Не вдалося видалити колекцію.');
      }
    },
  },
};
</script>

