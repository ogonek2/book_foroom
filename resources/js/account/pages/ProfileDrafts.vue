<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-lg font-extrabold">Чернетки</div>
      <div class="mt-1 text-xs text-white/60">Усі ваші неопубліковані матеріали</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Чернетки рецензій ({{ draftReviews.length }})</div>
      <div v-if="draftReviews.length" class="mt-3 space-y-3">
        <div v-for="review in draftReviews" :key="`dr-${review.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="text-sm text-white/90">{{ review.content }}</div>
          <a :href="`/books/${review.book_slug}/reviews/${review.id}/edit-draft`" class="mt-2 inline-block text-xs text-indigo-300 hover:text-indigo-200">Редагувати</a>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає чернеток рецензій.</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Чернетки цитат ({{ draftQuotes.length }})</div>
      <div v-if="draftQuotes.length" class="mt-3 space-y-3">
        <div v-for="quote in draftQuotes" :key="`dq-${quote.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="text-sm text-white/90">{{ quote.content }}</div>
          <a :href="`/books/${quote.book_slug}/quotes/${quote.id}/edit-draft`" class="mt-2 inline-block text-xs text-indigo-300 hover:text-indigo-200">Редагувати</a>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає чернеток цитат.</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Чернетки обговорень ({{ draftDiscussions.length }})</div>
      <div v-if="draftDiscussions.length" class="mt-3 space-y-3">
        <div v-for="discussion in draftDiscussions" :key="`dd-${discussion.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="text-sm text-white/90">{{ discussion.title }}</div>
          <a :href="`/discussions/${discussion.slug}/edit`" class="mt-2 inline-block text-xs text-indigo-300 hover:text-indigo-200">Редагувати</a>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає чернеток обговорень.</div>
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
    draftReviews() {
      return this.isOwner ? (this.dashboard?.draft_reviews || []) : [];
    },
    draftQuotes() {
      return this.isOwner ? (this.dashboard?.draft_quotes || []) : [];
    },
    draftDiscussions() {
      return this.isOwner ? (this.dashboard?.draft_discussions || []) : [];
    },
  },
};
</script>
