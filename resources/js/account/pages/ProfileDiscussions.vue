<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 border border-white/10">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-lg font-extrabold tracking-tight">Обговорення</div>
          <div class="mt-1 text-sm text-white/65">Керування темами, фільтри та швидкі дії.</div>
        </div>
        <a href="/discussions/create" class="acc-btn-primary !px-3 !py-1.5">Нове обговорення</a>
      </div>
      <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-2 text-xs">
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Всього</div><div class="text-base font-extrabold">{{ stats.total }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Активні</div><div class="text-base font-extrabold">{{ stats.active }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Чернетки</div><div class="text-base font-extrabold">{{ stats.draft }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Лайки</div><div class="text-base font-extrabold">{{ stats.likes }}</div></div>
        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"><div class="text-white/60">Відповіді</div><div class="text-base font-extrabold">{{ stats.replies }}</div></div>
      </div>
    </section>

    <section class="acc-glass rounded-2xl border border-white/10 p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
        <input v-model.trim="searchQuery" type="text" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20 md:col-span-2" placeholder="Пошук по заголовку...">
        <select v-model="statusFilter" class="w-full rounded-xl border border-slate-300/70 dark:border-slate-600/70 bg-white text-slate-900 dark:bg-slate-900/60 dark:text-slate-100 px-3 py-2 text-sm outline-none focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/20">
          <option value="all">Усі статуси</option>
          <option value="active">Активні</option>
          <option value="draft">Чернетки</option>
          <option value="closed">Закриті</option>
          <option value="blocked">Заблоковані</option>
        </select>
      </div>
      <div class="mt-2 text-xs text-white/60">Знайдено: {{ filteredDiscussions.length }}</div>
    </section>

    <div class="grid grid-cols-1 gap-3">
      <div v-for="item in filteredDiscussions" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="font-extrabold truncate">{{ item.title }}</div>
            <div class="mt-1 text-xs text-white/60">{{ item.created_at_human || 'щойно' }}</div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
            <span class="acc-pill">{{ statusLabel(item) }}</span>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-xs text-white/60">
          <span class="acc-pill">👍 {{ item.likes_count || 0 }}</span>
          <span class="acc-pill">💬 {{ item.replies_count || 0 }}</span>
        </div>
      </div>
      <div v-if="!filteredDiscussions.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        Обговорень за поточними фільтрами не знайдено.
      </div>
    </div>

    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="acc-modal-overlay" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal w-full max-w-xl">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-white/70">{{ actionModal.subtitle }}</div>
          <div class="mt-4 flex flex-wrap justify-end gap-2">
            <button class="acc-btn w-full sm:w-auto" @click="actionModal = null">Закрити</button>
            <a v-if="actionModal.editUrl" :href="actionModal.editUrl" class="acc-btn-primary w-full sm:w-auto text-center">Редагувати</a>
            <button class="acc-btn w-full sm:w-auto !border-red-400/40 !text-red-300 hover:!bg-red-500/20" :disabled="deleting" @click="deleteDiscussion">
              {{ deleting ? 'Видалення...' : 'Видалити' }}
            </button>
            <button class="acc-btn w-full sm:w-auto" @click="showUnavailable">Редагування недоступне</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  name: 'ProfileDiscussions',
  props: {
    dashboard: { type: Object, default: null },
  },
  data() {
    return {
      actionModal: null,
      deleting: false,
      discussionsLocal: [],
      searchQuery: '',
      statusFilter: 'all',
    };
  },
  computed: {
    discussions() {
      return this.discussionsLocal;
    },
    filteredDiscussions() {
      const query = String(this.searchQuery || '').toLowerCase().trim();
      return this.discussions.filter((item) => {
        const status = String(item?.status || '').toLowerCase();
        const isDraft = Boolean(item?.is_draft) || status === 'draft';
        if (this.statusFilter === 'draft' && !isDraft) return false;
        if (this.statusFilter !== 'all' && this.statusFilter !== 'draft' && status !== this.statusFilter) return false;
        if (!query) return true;
        return String(item?.title || '').toLowerCase().includes(query);
      });
    },
    stats() {
      return this.discussions.reduce((acc, item) => {
        const status = String(item?.status || '').toLowerCase();
        const isDraft = Boolean(item?.is_draft) || status === 'draft';
        acc.total += 1;
        if (status === 'active') acc.active += 1;
        if (isDraft) acc.draft += 1;
        acc.likes += Number(item?.likes_count || 0);
        acc.replies += Number(item?.replies_count || 0);
        return acc;
      }, { total: 0, active: 0, draft: 0, likes: 0, replies: 0 });
    },
  },
  watch: {
    dashboard: {
      immediate: true,
      deep: true,
      handler() {
        this.discussionsLocal = [...(this.dashboard?.recent_discussions || [])];
      },
    },
  },
  methods: {
    statusLabel(item) {
      const status = String(item?.status || '').toLowerCase();
      if (item?.is_draft || status === 'draft') return 'draft';
      return status || 'active';
    },
    openAction(item) {
      this.actionModal = {
        title: item?.title || 'Обговорення',
        subtitle: 'Перехід на сторінку редагування обговорення.',
        editUrl: item?.slug ? `/discussions/${item.slug}/edit` : '',
        slug: item?.slug || '',
      };
    },
    async deleteDiscussion() {
      if (!this.actionModal?.slug || this.deleting) return;
      if (!window.confirm('Видалити це обговорення? Дію неможливо скасувати.')) return;
      this.deleting = true;
      try {
        const { data } = await axios.delete(`/discussions/${this.actionModal.slug}`, {
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });
        if (!data?.success) {
          throw new Error(data?.message || 'Не вдалося видалити обговорення.');
        }
        this.discussionsLocal = this.discussionsLocal.filter((item) => item.slug !== this.actionModal.slug);
        this.actionModal = null;
      } catch (error) {
        alert(error?.response?.data?.message || error?.message || 'Помилка видалення обговорення.');
      } finally {
        this.deleting = false;
      }
    },
    showUnavailable() {
      alert('Редагування цього обговорення з профілю поки недоступне.');
    },
  },
};
</script>

