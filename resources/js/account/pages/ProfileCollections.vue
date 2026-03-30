<template>
  <div>
    <div class="flex items-center justify-between gap-3">
      <div>
        <div class="text-lg font-extrabold tracking-tight">Колекції</div>
        <div class="mt-1 text-sm text-white/65">
          Колекції/добірки з вашої старої панелі.
        </div>
      </div>
      <a href="/libraries/create" class="acc-btn-primary !px-3 !py-1.5">Нова колекція</a>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="item in collections" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="grid grid-cols-3 gap-1 h-16 mb-3">
          <template v-if="item.preview_covers && item.preview_covers.length">
            <img v-for="(cover, idx) in item.preview_covers.slice(0,3)" :key="`pc-${item.id}-${idx}`" :src="cover" class="h-16 w-full rounded object-cover border border-white/10" alt="">
            <div v-for="idx in Math.max(0, 3 - item.preview_covers.slice(0,3).length)" :key="`ph-${item.id}-${idx}`" class="h-16 rounded bg-white/10 border border-white/10" />
          </template>
          <template v-else>
            <div v-for="idx in 3" :key="`placeholder-${item.id}-${idx}`" class="h-16 rounded bg-white/10 border border-white/10" />
          </template>
        </div>
        <div class="flex items-center justify-between">
          <div class="font-extrabold truncate">{{ item.name }}</div>
          <div class="flex items-center gap-2">
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
            <button type="button" class="acc-btn !px-2 !py-1 text-xs !border-red-400/40 !text-red-200" @click="removeCollection(item)">Видалити</button>
            <span class="acc-pill">{{ item.books_count || 0 }} книг</span>
          </div>
        </div>
        <div class="mt-2 text-xs text-white/60">
          {{ item.description || 'Без опису' }}
        </div>
        <div class="mt-3 text-xs text-white/55">
          {{ item.created_at_human || 'щойно' }}
        </div>
      </div>
      <div v-if="!collections.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Колекції відсутні.
      </div>
    </div>
    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="absolute inset-0 bg-black/70" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl rounded-2xl border border-white/10 bg-[#0a0b14] p-5">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-white/70">Додавайте або видаляйте книги з колекції.</div>

          <div class="mt-4 rounded-xl border border-white/10 bg-white/5 p-3">
            <div class="flex gap-2">
              <input v-model="bookQuery" type="text" class="w-full rounded-xl border border-white/10 bg-black/20 px-3 py-2 text-sm text-white outline-none" placeholder="Пошук книги за назвою...">
              <button class="acc-btn" @click="searchBooks">Пошук</button>
            </div>
            <div v-if="searchResults.length" class="mt-3 max-h-40 overflow-auto space-y-2">
              <div v-for="book in searchResults" :key="`search-${book.id}`" class="flex items-center justify-between rounded-lg border border-white/10 px-2 py-1">
                <div class="flex items-center gap-2 min-w-0">
                  <img v-if="book.cover" :src="book.cover" class="h-10 w-7 rounded object-cover border border-white/10" alt="">
                  <div v-else class="h-10 w-7 rounded bg-white/10 border border-white/10" />
                  <span class="truncate text-sm">{{ book.title }}</span>
                </div>
                <button class="acc-btn !px-2 !py-1 text-xs" @click="addBook(book)">Додати</button>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <div class="text-xs font-bold text-white/70 mb-2">Книги у колекції</div>
            <div v-if="collectionBooks.length" class="max-h-56 overflow-auto space-y-2">
              <div v-for="book in collectionBooks" :key="`in-col-${book.id}`" class="flex items-center justify-between rounded-lg border border-white/10 px-2 py-1">
                <div class="flex items-center gap-2 min-w-0">
                  <img v-if="book.cover" :src="book.cover" class="h-10 w-7 rounded object-cover border border-white/10" alt="">
                  <div v-else class="h-10 w-7 rounded bg-white/10 border border-white/10" />
                  <span class="truncate text-sm">{{ book.title }}</span>
                </div>
                <button class="acc-btn !px-2 !py-1 text-xs !border-red-400/40 !text-red-200" @click="removeBook(book)">Видалити</button>
              </div>
            </div>
            <div v-else class="text-sm text-white/50">У колекції ще немає книг.</div>
          </div>

          <div class="mt-4 flex justify-end gap-2">
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
      return this.dashboard?.collections || [];
    },
    currentCollectionId() {
      return this.actionModal?.id || null;
    },
  },
  data() {
    return {
      actionModal: null,
      collectionBooks: [],
      bookQuery: '',
      searchResults: [],
    };
  },
  methods: {
    async openAction(item) {
      this.actionModal = {
        id: item?.id || null,
        title: item?.name || 'Колекція',
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
        window.location.reload();
      } catch (e) {
        alert(e?.response?.data?.message || 'Не вдалося видалити колекцію.');
      }
    },
  },
};
</script>

