<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Рецензії</div>
    <div class="mt-1 text-sm text-white/65">
      Останні рецензії з вашої старої панелі.
    </div>

    <div class="mt-6 space-y-3">
      <div v-for="item in reviews" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="font-extrabold truncate">{{ item.book ? item.book.title : 'Рецензія' }}</div>
            <div class="text-xs text-white/60 mt-1">
              {{ item.content || 'Без опису' }}
            </div>
          </div>
          <span class="acc-pill">
            <span class="h-2 w-2 rounded-full bg-yellow-400" />
            {{ item.created_at_human || 'щойно' }}
          </span>
        </div>
        <div class="mt-3 flex items-center gap-2 text-xs text-white/60">
          <span class="acc-pill">⭐ {{ item.rating || 0 }}</span>
        </div>
      </div>
      <div v-if="!reviews.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Рецензій поки немає.
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
};
</script>

