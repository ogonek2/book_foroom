<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Бібліотека</div>
    <div class="mt-1 text-sm text-white/65">
      Останні зміни у статусах читання з вашої старої панелі.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="item in books" :key="item.id" class="acc-glass rounded-2xl p-4 border border-white/10">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-center gap-3 min-w-0">
            <a v-if="item.book && item.book.slug" :href="`/books/${item.book.slug}`">
              <img v-if="item.book && item.book.cover" :src="item.book.cover" class="h-14 w-10 rounded object-cover border border-white/10" alt="">
              <div v-else class="h-14 w-10 rounded bg-white/10 border border-white/10 flex items-center justify-center text-[10px] text-white/40">N/A</div>
            </a>
            <div v-else class="h-14 w-10 rounded bg-white/10 border border-white/10 flex items-center justify-center text-[10px] text-white/40">N/A</div>
            <a v-if="item.book && item.book.slug" :href="`/books/${item.book.slug}`" class="text-sm font-extrabold truncate hover:text-indigo-300">
              {{ item.book ? item.book.title : 'Книга' }}
            </a>
            <div v-else class="text-sm font-extrabold truncate">{{ item.book ? item.book.title : 'Книга' }}</div>
          </div>
          <span class="acc-pill">{{ statusLabel(item.status) }}</span>
        </div>
        <div class="mt-2 text-xs text-white/60">
          {{ item.updated_at_human || 'щойно' }}
        </div>
        <div class="mt-3 h-2 rounded-full bg-white/10 overflow-hidden">
          <div class="h-full bg-gradient-to-r from-indigo-400 to-purple-500" :style="{ width: `${barWidth(item.status)}%` }" />
        </div>
        <div class="mt-3">
          <button type="button" class="acc-btn !px-2 !py-1 text-xs" @click="openAction(item)">Керування</button>
        </div>
      </div>
      <div v-if="!books.length" class="acc-glass rounded-2xl p-4 border border-white/10 text-sm text-white/65">
        У бібліотеці поки немає активності.
      </div>
    </div>

    <div v-if="actionModal" class="fixed inset-0 z-[130]">
      <div class="acc-modal-overlay" @click="actionModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-2xl">
          <div class="text-base font-extrabold">{{ actionModal.title }}</div>

          <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <label class="text-xs text-slate-600 dark:text-white/70">
              Статус
              <select v-model="editForm.status" class="mt-1 acc-modal-input">
                <option value="want_to_read">Заплановано</option>
                <option value="reading">Читаю</option>
                <option value="read">Прочитано</option>
                <option value="abandoned">Закинуто</option>
              </select>
            </label>
            <label class="text-xs text-slate-600 dark:text-white/70">
              Кількість прочитань
              <input v-model.number="editForm.times_read" min="1" type="number" class="mt-1 acc-modal-input">
            </label>
          </div>

          <div class="mt-3">
            <label class="text-xs text-slate-600 dark:text-white/70">
              Мова читання (основна)
              <input v-model.trim="editForm.reading_language" type="text" maxlength="10" class="mt-1 acc-modal-input" placeholder="uk / en / ...">
            </label>
          </div>

          <div class="mt-4 acc-modal-subpanel">
            <div class="text-xs font-bold text-slate-700 dark:text-white/75">Історія прочитань (дата + мова)</div>
            <div class="mt-2 space-y-2">
              <div v-for="(session, idx) in editForm.sessions" :key="`session-${idx}`" class="flex items-center gap-2">
                <input v-model="session.read_at" type="date" class="w-40 acc-modal-input !px-2 !py-1 !text-xs">
                <input v-model.trim="session.language" type="text" maxlength="10" placeholder="мова" class="w-24 acc-modal-input !px-2 !py-1 !text-xs">
                <button type="button" class="acc-btn !px-2 !py-1 text-xs !border-red-400/40 !text-red-200" @click="removeSession(idx)">Видалити</button>
              </div>
            </div>
            <button type="button" class="mt-2 acc-btn !px-2 !py-1 text-xs" @click="addSession">+ Додати прочитання</button>
          </div>

          <div class="mt-4 acc-modal-subpanel">
            <div class="text-xs font-bold text-slate-700 dark:text-white/75">Колекції</div>
            <div class="mt-2 space-y-1">
              <label v-for="lib in userLibraries" :key="`lib-${lib.id}`" class="flex items-center gap-2 text-xs text-slate-700 dark:text-white/80">
                <input v-model="editForm.library_ids" :value="lib.id" type="checkbox">
                <span>{{ lib.name }}</span>
              </label>
            </div>
          </div>

          <div class="mt-4 flex justify-end gap-2">
            <button class="acc-btn" @click="actionModal = null">Скасувати</button>
            <a v-if="actionModal.bookUrl" :href="actionModal.bookUrl" class="acc-btn">До книги</a>
            <button class="acc-btn-primary" :disabled="saving" @click="saveEdit">{{ saving ? 'Збереження...' : 'Зберегти' }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

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
  data() {
    return {
      actionModal: null,
      saving: false,
      userLibraries: [],
      editForm: {
        status: 'want_to_read',
        times_read: 1,
        reading_language: '',
        sessions: [],
        library_ids: [],
      },
    };
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
    openAction(item) {
      this.actionModal = {
        id: item?.id || null,
        title: item?.book?.title || 'Книга',
        subtitle: 'Редагування статусу і колекцій.',
        bookUrl: item?.book?.slug ? `/books/${item.book.slug}` : '',
        bookSlug: item?.book?.slug || '',
      };
      this.editForm = {
        status: item?.status || 'want_to_read',
        times_read: item?.times_read || 1,
        reading_language: item?.reading_language || '',
        sessions: (item?.sessions || []).map((s) => ({
          read_at: s.read_at || '',
          language: s.language || '',
        })),
        library_ids: [],
      };
      this.loadLibrariesAndMembership(item);
    },
    async loadLibrariesAndMembership(item) {
      this.userLibraries = [];
      this.editForm.library_ids = [];
      try {
        const [{ data: libsData }, { data: inLibs }] = await Promise.all([
          axios.get('/api/account/libraries'),
          item?.book?.slug ? axios.get(`/api/books/${item.book.slug}/libraries`) : Promise.resolve({ data: { libraries: [] } }),
        ]);
        this.userLibraries = libsData?.libraries || [];
        this.editForm.library_ids = (inLibs?.libraries || []).map((l) => l.id);
      } catch (e) {
        // keep modal usable even if collections fail to load
      }
    },
    addSession() {
      this.editForm.sessions.push({
        read_at: '',
        language: this.editForm.reading_language || '',
      });
    },
    removeSession(index) {
      this.editForm.sessions.splice(index, 1);
    },
    async saveEdit() {
      if (!this.actionModal?.id) return;
      this.saving = true;
      try {
        const payload = {
          status: this.editForm.status,
          times_read: this.editForm.times_read || 1,
          reading_language: this.editForm.reading_language || null,
          sessions: this.editForm.sessions.filter((s) => s.read_at),
          library_ids: this.editForm.library_ids || [],
        };
        await axios.put(`/api/account/reading-status/${this.actionModal.id}`, payload);
        window.location.reload();
      } catch (e) {
        alert(e?.response?.data?.message || 'Не вдалося зберегти зміни книги.');
      } finally {
        this.saving = false;
      }
    },
    showUnavailable() {
      alert('Для цього елемента ще немає окремої сторінки редагування.');
    },
  },
};
</script>

