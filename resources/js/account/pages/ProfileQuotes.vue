<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Цитати</div>
    <div class="mt-1 text-sm text-white/65">
      Останні публічні цитати зі старої панелі.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2">
      <div v-for="item in quotes" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="text-xs text-white/55 font-semibold">{{ item.created_at_human || 'щойно' }}</div>
        <div class="mt-2 text-sm leading-relaxed text-white/85">
          "{{ item.content || 'Без тексту' }}"
        </div>
        <div class="mt-3 flex items-center justify-between gap-2">
          <span class="acc-pill">Книга: {{ item.book ? item.book.title : '—' }}</span>
        </div>
      </div>
      <div v-if="!quotes.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Цитат поки немає.
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileQuotes',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    quotes() {
      return this.dashboard?.recent_quotes || [];
    },
  },
};
</script>

