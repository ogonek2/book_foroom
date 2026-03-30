<template>
  <div>
    <div class="flex items-center justify-between gap-3">
      <div>
        <div class="text-lg font-extrabold tracking-tight">Обговорення</div>
        <div class="mt-1 text-sm text-white/65">
          Останні теми з вашої старої панелі.
        </div>
      </div>
      <a href="/discussions/create" class="acc-btn-primary !px-3 !py-1.5">Нове обговорення</a>
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
          <div class="flex items-center gap-2">
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
            <span class="acc-pill">{{ item.status || 'active' }}</span>
          </div>
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
    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="acc-modal-overlay" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-md">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-white/70">{{ actionModal.subtitle }}</div>
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
  name: 'ProfileDiscussions',
  props: {
    dashboard: { type: Object, default: null },
  },
  computed: {
    discussions() {
      return this.dashboard?.recent_discussions || [];
    },
  },
  data() {
    return {
      actionModal: null,
    };
  },
  methods: {
    openAction(item) {
      this.actionModal = {
        title: item?.title || 'Обговорення',
        subtitle: 'Перехід на сторінку редагування обговорення.',
        editUrl: item?.slug ? `/discussions/${item.slug}/edit` : '',
      };
    },
    showUnavailable() {
      alert('Редагування цього обговорення з профілю поки недоступне.');
    },
  },
};
</script>

