<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Цитати</div>
    <div class="mt-1 text-sm text-white/65">
      Останні публічні цитати зі старої панелі.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2">
      <div v-for="item in quotes" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="text-xs text-white/55 font-semibold">{{ item.created_at_human || 'щойно' }}</div>
        <div class="mt-2 flex items-start gap-3">
          <img v-if="item.book && item.book.cover" :src="item.book.cover" class="h-14 w-10 rounded object-cover border border-white/10" alt="">
          <div v-else class="h-14 w-10 rounded bg-white/10 border border-white/10 flex items-center justify-center text-[10px] text-white/40">N/A</div>
          <button type="button" class="acc-btn !px-2 !py-1 text-xs ml-auto" @click="openAction(item)">Керування</button>
        </div>
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
    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="acc-modal-overlay" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-md">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-white/70">{{ actionModal.subtitle }}</div>
          <div class="mt-4 flex justify-end gap-2">
            <button class="acc-btn" @click="actionModal = null">Закрити</button>
            <button class="acc-btn" @click="showUnavailable">Редагування недоступне</button>
          </div>
        </div>
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
  data() {
    return { actionModal: null };
  },
  methods: {
    openAction(item) {
      this.actionModal = {
        title: item?.book?.title || 'Цитата',
        subtitle: 'Для цього елемента поки немає редактора у новому профілі.',
      };
    },
    showUnavailable() {
      alert('Редагування цієї цитати з профілю поки недоступне.');
    },
  },
};
</script>

