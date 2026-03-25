<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Колекції</div>
    <div class="mt-1 text-sm text-white/65">
      Колекції/добірки з вашої старої панелі.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="item in collections" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-center justify-between">
          <div class="font-extrabold truncate">{{ item.name }}</div>
          <span class="acc-pill">{{ item.books_count || 0 }} книг</span>
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
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileCollections',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    collections() {
      return this.dashboard?.collections || [];
    },
  },
};
</script>

