<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-lg font-extrabold">Чернетки</div>
      <div class="mt-1 text-xs text-white/60">Зручне керування всіма неопублікованими матеріалами</div>
      <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <div class="text-white/60">Всього</div>
          <div class="text-base font-extrabold">{{ draftItems.length }}</div>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <div class="text-white/60">Рецензії</div>
          <div class="text-base font-extrabold">{{ countsByType.review }}</div>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <div class="text-white/60">Обговорення</div>
          <div class="text-base font-extrabold">{{ countsByType.discussion }}</div>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <div class="text-white/60">Цитати</div>
          <div class="text-base font-extrabold">{{ countsByType.quote }}</div>
        </div>
      </div>
    </section>

    <div class="grid grid-cols-1 gap-4 lg:items-start lg:grid-cols-[260px,minmax(0,1fr)]">
      <aside class="acc-glass rounded-2xl border border-white/10 p-3 lg:sticky lg:top-24 lg:self-start h-fit">
        <div class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500 dark:text-white/40 px-2 pb-2">Тип чернеток</div>
        <div class="space-y-2">
          <button
            v-for="type in draftTypes"
            :key="type.id"
            type="button"
            class="acc-btn w-full justify-between"
            :class="activeType === type.id ? 'acc-settings-nav-active' : ''"
            @click="activeType = type.id"
          >
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
              placeholder="Пошук по назві, тексту, книзі..."
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
          <div class="mt-2 text-xs text-white/60">Знайдено: {{ filteredDrafts.length }}</div>
        </div>

        <div v-if="filteredDrafts.length" class="space-y-3">
          <article
            v-for="item in filteredDrafts"
            :key="`${item.type}-${item.id}`"
            class="acc-glass rounded-2xl border border-white/10 p-4"
          >
            <div class="flex items-start gap-4">
              <img
                v-if="item.type !== 'discussion'"
                :src="item.cover || '/images/placeholders/book-cover.svg'"
                class="h-24 w-16 rounded-md object-cover border border-white/10 shrink-0"
                alt=""
                @error="onCoverError"
              >
              <div
                v-else
                class="h-24 w-16 rounded-md border border-white/10 shrink-0 flex items-center justify-center bg-white/5 text-white/65"
              >
                <i class="fas fa-comments text-lg" aria-hidden="true"></i>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="acc-pill">{{ typeLabel(item.type) }}</span>
                  <span class="text-xs text-white/55">{{ item.updated_at_human || 'щойно' }}</span>
                </div>
                <div class="mt-2 text-base font-extrabold leading-tight">{{ item.title }}</div>
                <div v-if="item.book_title" class="mt-1 text-xs text-white/65">Книга: {{ item.book_title }}</div>
                <div class="mt-2 text-sm text-white/70 line-clamp-3">{{ item.content || 'Без тексту' }}</div>
                <div class="mt-3 flex flex-wrap gap-2">
                  <a v-if="item.editUrl" :href="item.editUrl" class="acc-btn-primary">Редагувати</a>
                  <button type="button" class="acc-btn" @click="openAction(item.type, item.raw)">Керування</button>
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
        <div class="acc-modal w-full max-w-xl">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-white/70">{{ actionModal.subtitle }}</div>
          <div class="mt-4 flex flex-wrap justify-end gap-2">
            <button class="acc-btn w-full sm:w-auto" @click="actionModal = null">Закрити</button>
            <a v-if="actionModal.editUrl" :href="actionModal.editUrl" class="acc-btn-primary w-full text-center sm:w-auto">Редагувати</a>
            <button class="acc-btn w-full sm:w-auto !border-red-400/40 !text-red-300 hover:!bg-red-500/20" :disabled="deleting" @click="deleteDraft">
              {{ deleting ? 'Видалення...' : 'Видалити чернетку' }}
            </button>
            <button class="acc-btn-primary w-full sm:w-auto" :disabled="deleting" @click="publishDraft">
              Опублікувати
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  props: {
    dashboard: { type: Object, default: () => ({}) },
    isOwner: { type: Boolean, default: false },
  },
  data() {
    return {
      actionModal: null,
      deleting: false,
      activeType: 'all',
      searchQuery: '',
      dateFilter: 'all',
      draftReviewsLocal: [],
      draftQuotesLocal: [],
      draftDiscussionsLocal: [],
    };
  },
  computed: {
    countsByType() {
      return {
        review: this.draftReviewsLocal.length,
        quote: this.draftQuotesLocal.length,
        discussion: this.draftDiscussionsLocal.length,
      };
    },
    draftTypes() {
      const total = this.draftItems.length;
      return [
        { id: 'all', label: 'Всі', count: total },
        { id: 'review', label: 'Рецензії', count: this.countsByType.review },
        { id: 'discussion', label: 'Обговорення', count: this.countsByType.discussion },
        { id: 'quote', label: 'Цитати', count: this.countsByType.quote },
      ];
    },
    draftItems() {
      const reviews = this.draftReviewsLocal.map((item) => ({
        id: item.id,
        type: 'review',
        title: this.draftBookTitle(item),
        book_title: item?.book_title || '',
        cover: this.draftBookCover(item),
        content: this.plainText(item.content),
        updated_at: item?.updated_at || '',
        updated_at_human: item?.updated_at_human || '',
        editUrl: item?.book_slug ? `/books/${item.book_slug}/reviews/${item.id}/edit-draft` : '',
        raw: item,
      }));
      const quotes = this.draftQuotesLocal.map((item) => ({
        id: item.id,
        type: 'quote',
        title: this.draftBookTitle(item),
        book_title: item?.book_title || '',
        cover: this.draftBookCover(item),
        content: this.plainText(item.content),
        updated_at: item?.updated_at || '',
        updated_at_human: item?.updated_at_human || '',
        editUrl: item?.book_slug ? `/books/${item.book_slug}/quotes/${item.id}/edit-draft` : '',
        raw: item,
      }));
      const discussions = this.draftDiscussionsLocal.map((item) => ({
        id: item.id,
        type: 'discussion',
        title: item?.title || 'Чернетка обговорення',
        book_title: '',
        cover: '/images/placeholders/book-cover.svg',
        content: this.plainText(item.content),
        updated_at: item?.updated_at || '',
        updated_at_human: item?.updated_at_human || '',
        editUrl: item?.slug ? `/discussions/${item.slug}/edit` : '',
        raw: item,
      }));
      return [...reviews, ...discussions, ...quotes].sort((a, b) => {
        const at = Date.parse(a.updated_at || '') || 0;
        const bt = Date.parse(b.updated_at || '') || 0;
        return bt - at;
      });
    },
    filteredDrafts() {
      const query = String(this.searchQuery || '').toLowerCase().trim();
      const days = Number(this.dateFilter || 0);
      const minTs = days > 0 ? Date.now() - (days * 24 * 60 * 60 * 1000) : 0;

      return this.draftItems.filter((item) => {
        if (this.activeType !== 'all' && item.type !== this.activeType) return false;
        if (minTs > 0) {
          const ts = Date.parse(item.updated_at || '') || 0;
          if (!ts || ts < minTs) return false;
        }
        if (!query) return true;
        return [item.title, item.book_title, item.content, this.typeLabel(item.type)]
          .filter(Boolean)
          .some((value) => String(value).toLowerCase().includes(query));
      });
    },
  },
  watch: {
    dashboard: {
      immediate: true,
      deep: true,
      handler() {
        this.syncDraftsFromDashboard();
      },
    },
  },
  methods: {
    syncDraftsFromDashboard() {
      if (!this.isOwner) {
        this.draftReviewsLocal = [];
        this.draftQuotesLocal = [];
        this.draftDiscussionsLocal = [];
        return;
      }
      this.draftReviewsLocal = [...(this.dashboard?.draft_reviews || [])];
      this.draftQuotesLocal = [...(this.dashboard?.draft_quotes || [])];
      this.draftDiscussionsLocal = [...(this.dashboard?.draft_discussions || [])];
    },
    plainText(value) {
      if (!value) return '';
      return String(value).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    },
    draftBookTitle(item) {
      return item?.book?.title || item?.book_title || item?.title || 'Без назви книги';
    },
    draftBookCover(item) {
      return item?.book?.cover || item?.book_cover || '/images/placeholders/book-cover.svg';
    },
    draftReviewType(item) {
      return item?.review_type || item?.type || 'Не вказано';
    },
    draftOpinionType(item) {
      return item?.opinion_type || item?.thought_type || item?.opinion || 'Не вказано';
    },
    draftScore(item) {
      const value = item?.score ?? item?.rating ?? item?.balls;
      return value !== undefined && value !== null && value !== '' ? value : '—';
    },
    draftLanguage(item) {
      return item?.reading_language || item?.language || item?.lang || 'Не вказано';
    },
    typeLabel(type) {
      if (type === 'review') return 'Рецензія';
      if (type === 'discussion') return 'Обговорення';
      return 'Цитата';
    },
    onCoverError(event) {
      const fallback = '/images/placeholders/book-cover.svg';
      if (event?.target && !String(event.target.src || '').includes(fallback)) {
        event.target.src = fallback;
      }
    },
    openAction(type, item) {
      if (type === 'review') {
        this.actionModal = {
          type: 'review',
          id: item?.id,
          title: item?.book_title || 'Чернетка рецензії',
          subtitle: 'Перехід до редагування чернетки рецензії.',
          editUrl: `/books/${item.book_slug}/reviews/${item.id}/edit-draft`,
        };
        return;
      }
      if (type === 'quote') {
        this.actionModal = {
          type: 'quote',
          id: item?.id,
          title: item?.book_title || 'Чернетка цитати',
          subtitle: 'Перехід до редагування чернетки цитати.',
          editUrl: `/books/${item.book_slug}/quotes/${item.id}/edit-draft`,
        };
        return;
      }
      this.actionModal = {
        type: 'discussion',
        id: item?.id,
        title: item?.title || 'Чернетка обговорення',
        subtitle: 'Перехід до редагування чернетки обговорення.',
        editUrl: item?.slug ? `/discussions/${item.slug}/edit` : '',
      };
    },
    async deleteDraft() {
      if (!this.actionModal?.type || !this.actionModal?.id || this.deleting) return;
      if (!window.confirm('Видалити цю чернетку? Дію неможливо скасувати.')) return;
      this.deleting = true;
      try {
        const { type, id } = this.actionModal;
        const { data } = await axios.delete(`/drafts/${type}/${id}`);
        if (!data?.success) {
          throw new Error(data?.message || 'Не вдалося видалити чернетку.');
        }
        if (type === 'review') {
          this.draftReviewsLocal = this.draftReviewsLocal.filter((item) => Number(item.id) !== Number(id));
        } else if (type === 'quote') {
          this.draftQuotesLocal = this.draftQuotesLocal.filter((item) => Number(item.id) !== Number(id));
        } else if (type === 'discussion') {
          this.draftDiscussionsLocal = this.draftDiscussionsLocal.filter((item) => Number(item.id) !== Number(id));
        }
        this.actionModal = null;
      } catch (error) {
        alert(error?.response?.data?.message || error?.message || 'Помилка видалення чернетки.');
      } finally {
        this.deleting = false;
      }
    },
    async publishDraft() {
      if (!this.actionModal?.type || !this.actionModal?.id || this.deleting) return;
      this.deleting = true;
      try {
        const { type, id } = this.actionModal;
        const { data } = await axios.post(`/drafts/${type}/${id}/publish`);
        if (!data?.success) {
          throw new Error(data?.message || 'Не вдалося опублікувати чернетку.');
        }
        if (type === 'review') {
          this.draftReviewsLocal = this.draftReviewsLocal.filter((item) => Number(item.id) !== Number(id));
        } else if (type === 'quote') {
          this.draftQuotesLocal = this.draftQuotesLocal.filter((item) => Number(item.id) !== Number(id));
        } else if (type === 'discussion') {
          this.draftDiscussionsLocal = this.draftDiscussionsLocal.filter((item) => Number(item.id) !== Number(id));
        }
        this.actionModal = null;
      } catch (error) {
        alert(error?.response?.data?.message || error?.message || 'Помилка публікації чернетки.');
      } finally {
        this.deleting = false;
      }
    },
  },
};
</script>
