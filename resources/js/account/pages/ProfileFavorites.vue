<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-lg font-extrabold">Збережене</div>
      <div class="mt-1 text-xs text-white/60">Цитати та рецензії, які ви зберегли</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Цитати ({{ favoriteQuotes.length }})</div>
      <div v-if="favoriteQuotes.length" class="mt-3 space-y-3">
        <div v-for="quote in favoriteQuotes" :key="`fq-${quote.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="flex items-start gap-3">
            <img v-if="quote.book_cover" :src="quote.book_cover" class="h-14 w-10 rounded object-cover border border-white/10" alt="">
            <div v-else class="h-14 w-10 rounded bg-white/10 border border-white/10 flex items-center justify-center text-[10px] text-white/40">N/A</div>
            <div class="min-w-0 flex-1">
              <div class="text-sm text-white/90">{{ quote.content }}</div>
              <a :href="`/books/${quote.book_slug}`" class="mt-2 inline-block text-xs text-indigo-300 hover:text-indigo-200">{{ quote.book_title || 'Книга' }}</a>
            </div>
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(quote, 'quote')">Керування</button>
          </div>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає збережених цитат.</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Рецензії ({{ favoriteReviews.length }})</div>
      <div v-if="favoriteReviews.length" class="mt-3 space-y-3">
        <div v-for="review in favoriteReviews" :key="`fr-${review.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="flex items-start gap-3">
            <img v-if="review.book_cover" :src="review.book_cover" class="h-14 w-10 rounded object-cover border border-white/10" alt="">
            <div v-else class="h-14 w-10 rounded bg-white/10 border border-white/10 flex items-center justify-center text-[10px] text-white/40">N/A</div>
            <div class="min-w-0 flex-1">
              <div class="text-sm text-white/90">{{ review.content }}</div>
              <a :href="`/books/${review.book_slug}/reviews/${review.id}`" class="mt-2 inline-block text-xs text-indigo-300 hover:text-indigo-200">{{ review.book_title || 'Рецензія' }}</a>
            </div>
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(review, 'review')">Керування</button>
          </div>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає збережених рецензій.</div>
    </section>
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
  computed: {
    favoriteQuotes() {
      return this.isOwner ? (this.dashboard?.favorite_quotes || []) : [];
    },
    favoriteReviews() {
      return this.isOwner ? (this.dashboard?.favorite_reviews || []) : [];
    },
  },
  data() {
    return { actionModal: null };
  },
  methods: {
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
