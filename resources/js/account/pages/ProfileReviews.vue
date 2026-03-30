<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Рецензії</div>
    <div class="mt-1 text-sm text-white/65">
      Останні рецензії з вашої старої панелі.
    </div>

    <div class="mt-6 space-y-3">
      <div v-for="item in reviews" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0 flex items-start gap-3">
            <img v-if="item.book && item.book.cover" :src="item.book.cover" class="h-14 w-10 rounded object-cover border border-white/10" alt="">
            <div v-else class="h-14 w-10 rounded bg-white/10 border border-white/10 flex items-center justify-center text-[10px] text-white/40">N/A</div>
            <div class="min-w-0">
              <div class="font-extrabold truncate">{{ item.book ? item.book.title : 'Рецензія' }}</div>
              <div class="text-xs text-white/60 mt-1">
                {{ item.content || 'Без опису' }}
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
            <div class="text-xs text-white/60 mt-1">
              {{ item.created_at_human || 'щойно' }}
            </div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-xs text-white/60">
          <span class="acc-pill">⭐ {{ item.rating || 0 }}</span>
        </div>
      </div>
      <div v-if="!reviews.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Рецензій поки немає.
      </div>
    </div>
    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="absolute inset-0 bg-black/70" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl border border-white/10 bg-[#0a0b14] p-5">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-white/70">{{ actionModal.subtitle }}</div>
          <div class="mt-4 flex justify-end gap-2">
            <button class="acc-btn" @click="actionModal = null">Закрити</button>
            <a v-if="actionModal.editUrl" :href="actionModal.editUrl" class="acc-btn-primary">Редагувати</a>
            <button v-else class="acc-btn" @click="showUnavailable">Редагування недоступне</button>
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
  },
  data() {
    return { actionModal: null };
  },
  methods: {
    openAction(item) {
      this.actionModal = {
        title: item?.book?.title || 'Рецензія',
        subtitle: 'Перехід на сторінку редагування рецензії.',
        editUrl: item?.book?.slug && item?.id ? `/books/${item.book.slug}/reviews/${item.id}/edit` : '',
      };
    },
    showUnavailable() {
      alert('Для цього елемента ще немає окремої сторінки редагування.');
    },
  },
};
</script>

