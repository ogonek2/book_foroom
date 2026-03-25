<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Обговорення</div>
    <div class="mt-1 text-sm text-white/65">
      Останні теми з вашої старої панелі.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-3">
      <div v-for="item in discussions" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="font-extrabold truncate">{{ item.title }}</div>
            <div class="mt-1 text-xs text-white/60">
              {{ item.created_at_human || 'щойно' }}
            </div>
          </div>
          <span class="acc-pill">{{ item.status || 'active' }}</span>
        </div>
        <div class="mt-3 flex items-center gap-2 text-xs text-white/60">
          <span class="acc-pill">👍 {{ item.likes_count || 0 }}</span>
          <span class="acc-pill">💬 {{ item.replies_count || 0 }}</span>
        </div>
      </div>
      <div v-if="!discussions.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Обговорень поки немає.
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileDiscussions',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    discussions() {
      return this.dashboard?.recent_discussions || [];
    },
  },
};
</script>

