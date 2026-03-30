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
          <button type="button" class="mt-2 acc-btn !px-2 !py-1 text-xs" @click="openAction('review', review)">Керування</button>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає чернеток рецензій.</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Чернетки цитат ({{ draftQuotes.length }})</div>
      <div v-if="draftQuotes.length" class="mt-3 space-y-3">
        <div v-for="quote in draftQuotes" :key="`dq-${quote.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="text-sm text-white/90">{{ quote.content }}</div>
          <button type="button" class="mt-2 acc-btn !px-2 !py-1 text-xs" @click="openAction('quote', quote)">Керування</button>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає чернеток цитат.</div>
    </section>

    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="text-sm font-bold text-white/80">Чернетки обговорень ({{ draftDiscussions.length }})</div>
      <div v-if="draftDiscussions.length" class="mt-3 space-y-3">
        <div v-for="discussion in draftDiscussions" :key="`dd-${discussion.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
          <div class="text-sm text-white/90">{{ discussion.title }}</div>
          <button type="button" class="mt-2 acc-btn !px-2 !py-1 text-xs" @click="openAction('discussion', discussion)">Керування</button>
        </div>
      </div>
      <div v-else class="mt-2 text-sm text-white/50">Немає чернеток обговорень.</div>
    </section>
    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="absolute inset-0 bg-black/70" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl border border-white/10 bg-[#0a0b14] p-5">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-white/70">{{ actionModal.subtitle }}</div>
          <div class="mt-4 flex justify-end gap-2">
            <button class="acc-btn" @click="actionModal = null">Закрити</button>
            <a v-if="actionModal.editUrl" :href="actionModal.editUrl" class="acc-btn-primary">Редагувати</a>
            <button class="acc-btn" @click="showUnavailable">Опублікація з профілю недоступна</button>
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
  data() {
    return { actionModal: null };
  },
  methods: {
    openAction(type, item) {
      if (type === 'review') {
        this.actionModal = {
          title: item?.book_title || 'Чернетка рецензії',
          subtitle: 'Перехід до редагування чернетки рецензії.',
          editUrl: `/books/${item.book_slug}/reviews/${item.id}/edit-draft`,
        };
        return;
      }
      if (type === 'quote') {
        this.actionModal = {
          title: item?.book_title || 'Чернетка цитати',
          subtitle: 'Перехід до редагування чернетки цитати.',
          editUrl: `/books/${item.book_slug}/quotes/${item.id}/edit-draft`,
        };
        return;
      }
      this.actionModal = {
        title: item?.title || 'Чернетка обговорення',
        subtitle: 'Перехід до редагування чернетки обговорення.',
        editUrl: item?.slug ? `/discussions/${item.slug}/edit` : '',
      };
    },
    showUnavailable() {
      alert('Публікація/видалення через новий профіль буде додана окремо.');
    },
  },
};
</script>
