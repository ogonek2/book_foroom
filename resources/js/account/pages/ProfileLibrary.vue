<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Бібліотека</div>
    <div class="mt-1 text-sm text-white/65">
      Останні зміни у статусах читання з вашої старої панелі.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="item in books" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-center justify-between">
          <div class="text-sm font-extrabold truncate">{{ item.book ? item.book.title : 'Книга' }}</div>
          <span class="acc-pill">{{ statusLabel(item.status) }}</span>
        </div>
        <div class="mt-2 text-xs text-white/60">
          {{ item.updated_at_human || 'щойно' }}
        </div>
        <div class="mt-3 h-2 rounded-full bg-white/10 overflow-hidden">
          <div class="h-full bg-gradient-to-r from-indigo-400 to-purple-500" :style="{ width: `${barWidth(item.status)}%` }" />
        </div>
      </div>
      <div v-if="!books.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        У бібліотеці поки немає активності.
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileLibrary',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    books() {
      return this.dashboard?.recent_read_books || [];
    },
  },
  methods: {
    statusLabel(status) {
      if (status === 'read') return 'Завершено';
      if (status === 'reading') return 'Дивлюсь';
      if (status === 'want_to_read') return 'Заплановано';
      return status || '—';
    },
    barWidth(status) {
      if (status === 'read') return 100;
      if (status === 'reading') return 65;
      if (status === 'want_to_read') return 35;
      return 20;
    },
  },
};
</script>

