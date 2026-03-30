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
          <div class="text-sm text-white/90">{{ quote.content }}</div>
          <a :href="`/books/${quote.book_slug}`" class="mt-2 inline-block text-xs text-indigo-300 hover:text-indigo-200">{{ quote.book_title || 'Книга' }}</a>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає збережених цитат.</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Рецензії ({{ favoriteReviews.length }})</div>
      <div v-if="favoriteReviews.length" class="mt-3 space-y-3">
        <div v-for="review in favoriteReviews" :key="`fr-${review.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="text-sm text-white/90">{{ review.content }}</div>
          <a :href="`/books/${review.book_slug}/reviews/${review.id}`" class="mt-2 inline-block text-xs text-indigo-300 hover:text-indigo-200">{{ review.book_title || 'Рецензія' }}</a>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає збережених рецензій.</div>
    </section>
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
};
</script>
