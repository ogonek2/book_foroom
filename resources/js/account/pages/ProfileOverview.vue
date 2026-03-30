<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 sm:p-5 border border-white/10">
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
        <div class="lg:col-span-8 flex items-center gap-4 sm:gap-5">
          <div class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl overflow-hidden border border-white/15 bg-white/10 shrink-0">
            <img
              :src="avatarSrc"
              class="h-full w-full object-cover"
              alt="avatar"
            />
          </div>
          <div class="min-w-0">
            <div v-if="profile && profile.name" class="text-xl sm:text-2xl font-extrabold truncate">{{ profile.name }}</div>
            <div v-else class="text-xl sm:text-2xl font-extrabold truncate">Користувач</div>
            <div class="text-sm text-white/60">@{{ profile && profile.username ? profile.username : 'guest' }}</div>
            <div class="mt-1 flex items-center gap-5 text-xs text-white/65">
              <span>{{ stats.reviews_count || 0 }} рецензій</span>
              <span>{{ stats.discussions_count || 0 }} обговорень</span>
            </div>
          </div>
        </div>

        <div class="lg:col-span-4">
          <div class="acc-glass rounded-2xl p-3 border border-white/10">
            <div class="flex gap-2 mb-3 text-xs">
              <button class="acc-btn !px-3 !py-1">Аніме</button>
              <button class="acc-btn !px-3 !py-1 opacity-70">Манга</button>
              <button class="acc-btn !px-3 !py-1 opacity-70">Ранобе</button>
            </div>
            <div class="grid grid-cols-[86px,1fr] items-center gap-3">
              <div class="relative h-[86px] w-[86px] rounded-full border-4 border-white/20">
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="text-2xl font-extrabold">{{ stats.read_count || 0 }}</div>
                </div>
              </div>
              <ul class="space-y-1 text-xs text-white/70">
                <li class="flex justify-between"><span>Дивлюсь</span><span>{{ stats.reading_count || 0 }}</span></li>
                <li class="flex justify-between"><span>Заплановано</span><span>{{ stats.planned_count || 0 }}</span></li>
                <li class="flex justify-between"><span>Закинуто</span><span>{{ stats.dropped_count || 0 }}</span></li>
                <li class="flex justify-between"><span>Відкладено</span><span>0</span></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-12">
      <div class="lg:col-span-8 space-y-4">
        <div class="text-base font-extrabold">Статистика</div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="text-xs text-white/60 mb-3">Активність</div>
            <div class="h-14 rounded-xl bg-white/5 border border-white/10 flex items-end px-3 pb-2 gap-2">
              <span v-for="i in 14" :key="`bar-${i}`" class="w-1 rounded-full bg-white/20" :style="{ height: `${8 + (i % 3) * 6}px` }" />
            </div>
          </div>
          <div class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="text-xs text-white/60 mb-3">Прогрес читання</div>
            <div class="text-2xl font-extrabold">{{ stats.read_count || 0 }} книг</div>
            <div class="mt-3 h-2 rounded-full bg-white/10 overflow-hidden">
              <div class="h-full bg-gradient-to-r from-purple-400 to-indigo-500" :style="{ width: `${progressWidth}%` }" />
            </div>
            <div class="mt-1 text-xs text-white/55 text-right">план: {{ stats.planned_count || 0 }} | цілі: {{ stats.planner_done_items || 0 }}/{{ stats.planner_total_items || 0 }}</div>
          </div>
        </div>

        <div class="acc-glass rounded-2xl p-4 border border-white/10">
          <div class="text-xs text-white/60 mb-2">Розширена статистика</div>
          <div class="grid grid-cols-2 gap-2 text-xs sm:grid-cols-4">
            <div class="rounded-lg border border-white/10 bg-white/5 p-2">
              <div class="text-white/55">Усього книг</div>
              <div class="font-bold text-base">{{ stats.total_books_count || 0 }}</div>
            </div>
            <div class="rounded-lg border border-white/10 bg-white/5 p-2">
              <div class="text-white/55">Оцінено</div>
              <div class="font-bold text-base">{{ stats.rated_count || 0 }}</div>
            </div>
            <div class="rounded-lg border border-white/10 bg-white/5 p-2">
              <div class="text-white/55">Середня оцінка</div>
              <div class="font-bold text-base">{{ stats.average_rating || '—' }}</div>
            </div>
            <div class="rounded-lg border border-white/10 bg-white/5 p-2">
              <div class="text-white/55">Закинуто</div>
              <div class="font-bold text-base">{{ stats.dropped_count || 0 }}</div>
            </div>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-3">
            <div class="text-base font-extrabold">Улюблені</div>
            <div class="flex gap-1 text-xs">
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'books' }" @click="activeFavoritesTab = 'books'">Книги</button>
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'reviews' }" @click="activeFavoritesTab = 'reviews'">Рецензії</button>
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'discussions' }" @click="activeFavoritesTab = 'discussions'">Обговорення</button>
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'quotes' }" @click="activeFavoritesTab = 'quotes'">Цитати</button>
            </div>
          </div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div v-if="favoriteItems.length" class="space-y-3">
              <div v-for="item in favoriteItems.slice(0, 3)" :key="`${activeFavoritesTab}-${item.id}`" class="flex items-center gap-3">
                <img v-if="item.cover" :src="item.cover" class="h-11 w-8 rounded object-cover border border-white/10" alt="" />
                <div v-else class="h-11 w-8 rounded bg-white/10 border border-white/10" />
                <div class="min-w-0">
                  <div class="text-sm font-semibold truncate">{{ item.title }}</div>
                  <div class="text-xs text-white/55">{{ item.meta }}</div>
                </div>
              </div>
            </div>
            <template v-else>
              <div class="text-sm font-bold">Список поки порожній</div>
              <div class="text-xs text-white/60 mt-1">Тут зʼявиться активність для обраного типу.</div>
            </template>
          </div>
        </div>
      </div>

      <div class="lg:col-span-4 space-y-4">
        <div>
          <div class="text-base font-extrabold mb-3">Історія</div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div class="space-y-2 text-sm">
              <div class="flex justify-between"><span class="text-white/70">Рецензії</span><span class="font-bold">{{ stats.reviews_count || 0 }}</span></div>
              <div class="flex justify-between"><span class="text-white/70">Обговорення</span><span class="font-bold">{{ stats.discussions_count || 0 }}</span></div>
              <div class="flex justify-between"><span class="text-white/70">Цитати</span><span class="font-bold">{{ stats.quotes_count || 0 }}</span></div>
              <div class="flex justify-between"><span class="text-white/70">Колекції</span><span class="font-bold">{{ stats.collections_count || 0 }}</span></div>
            </div>
          </div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-3">
            <div class="text-base font-extrabold">Колекції</div>
            <router-link class="acc-btn !px-3 !py-1" :to="`/u/${profile && profile.username ? profile.username : 'guest'}/collections`">→</router-link>
          </div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div v-if="collections.length" class="space-y-2">
              <div v-for="col in collections.slice(0, 3)" :key="col.id" class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="grid grid-cols-3 gap-0.5 h-8 w-14 shrink-0">
                    <template v-if="col.preview_covers && col.preview_covers.length">
                      <img v-for="(cover, idx) in col.preview_covers.slice(0,3)" :key="`cover-${col.id}-${idx}`" :src="cover" class="h-8 w-full rounded-sm object-cover border border-white/10" alt="">
                    </template>
                    <template v-else>
                      <div v-for="idx in 3" :key="`empty-${col.id}-${idx}`" class="h-8 rounded-sm bg-white/10 border border-white/10" />
                    </template>
                  </div>
                  <span class="truncate">{{ col.name }}</span>
                </div>
                <span class="text-white/55">{{ col.books_count }} кн.</span>
              </div>
            </div>
            <template v-else>
              <div class="text-sm font-bold">Колекції відсутні</div>
              <div class="text-xs text-white/60 mt-1">Створіть свою першу колекцію.</div>
            </template>
          </div>
        </div>
        <div v-if="isOwner">
          <div class="flex items-center justify-between mb-3">
            <div class="text-base font-extrabold">План читання</div>
            <button class="acc-btn-primary !px-3 !py-1" @click="openPlannerModal">+ Новий план</button>
          </div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div v-if="readingPlans.length" class="space-y-3">
              <div v-for="plan in readingPlans" :key="`plan-${plan.id}`" class="rounded-xl border border-white/10 bg-white/5 p-3">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-bold text-sm">{{ plan.title }}</div>
                  <div class="text-xs text-white/60">{{ plan.done_items }}/{{ plan.total_items }}</div>
                </div>
                <div class="mt-2 h-1.5 rounded-full bg-white/10 overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-500" :style="{ width: `${plan.progress || 0}%` }" />
                </div>
                <div class="mt-2 space-y-1">
                  <label v-for="item in plan.items.slice(0, 4)" :key="`plan-item-${item.id}`" class="flex items-center gap-2 text-xs">
                    <input type="checkbox" :checked="item.is_done" @change="togglePlanItem(plan, item)">
                    <span class="truncate">{{ item.book ? item.book.title : 'Книга' }}</span>
                  </label>
                </div>
              </div>
            </div>
            <div v-else class="text-sm text-white/55">Ще немає планів читання.</div>
          </div>
        </div>
      </div>
    </section>

    <div v-if="showPlannerModal" class="fixed inset-0 z-[140]">
      <div class="absolute inset-0 bg-black/70" @click="showPlannerModal = false" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl rounded-2xl border border-white/10 bg-[#0a0b14] p-5">
          <div class="text-base font-extrabold">Новий план читання</div>
          <div class="mt-3 space-y-3">
            <input v-model.trim="plannerForm.title" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none" placeholder="Назва плану">
            <textarea v-model.trim="plannerForm.goal" rows="3" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none" placeholder="Мета"></textarea>
            <input v-model="plannerForm.target_date" type="date" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none">
            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
              <div class="flex gap-2">
                <input v-model.trim="plannerBookQuery" type="text" class="w-full rounded-xl border border-white/10 bg-black/20 px-3 py-2 text-sm text-white outline-none" placeholder="Знайти книги для плану">
                <button class="acc-btn" @click="searchPlannerBooks">Пошук</button>
              </div>
              <div v-if="plannerSearchResults.length" class="mt-2 max-h-48 overflow-auto space-y-1">
                <label v-for="book in plannerSearchResults" :key="`plan-search-${book.id}`" class="flex items-center gap-2 text-xs">
                  <input type="checkbox" :checked="plannerForm.book_ids.includes(book.id)" @change="togglePlannerBook(book.id)">
                  <span class="truncate">{{ book.title }}</span>
                </label>
              </div>
            </div>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button class="acc-btn" @click="showPlannerModal = false">Скасувати</button>
            <button class="acc-btn-primary" :disabled="plannerSaving" @click="savePlanner">{{ plannerSaving ? 'Створення...' : 'Створити план' }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  name: 'ProfileOverview',
  props: {
    profile: { type: Object, default: null },
    dashboard: { type: Object, default: null },
    isOwner: { type: Boolean, default: false },
  },
  computed: {
    stats() {
      return this.dashboard?.stats || {};
    },
    recentBooks() {
      return this.dashboard?.recent_read_books || [];
    },
    collections() {
      return this.dashboard?.collections || [];
    },
    progressWidth() {
      const done = Number(this.stats.read_count || 0);
      const active = Number(this.stats.reading_count || 0);
      const planned = Number(this.stats.planned_count || 0);
      const plannerDone = Number(this.stats.planner_done_items || 0);
      const plannerTotal = Number(this.stats.planner_total_items || 0);
      const total = done + active + planned + plannerTotal;
      if (!total) return 6;
      return Math.min(100, Math.max(6, Math.round(((done + plannerDone) / total) * 100)));
    },
    readingPlans() {
      return this.dashboard?.reading_plans || [];
    },
    avatarSrc() {
      if (this.profile && this.profile.avatar) return this.profile.avatar;

      const nickname = this.profile && this.profile.username ? this.profile.username : 'User';
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(nickname)}&background=7c3aed&color=fff&size=200`;
    },
    favoriteItems() {
      if (this.activeFavoritesTab === 'reviews') {
        return (this.dashboard?.recent_reviews || []).map((item) => ({
          id: item.id,
          title: item?.book?.title || 'Рецензія',
          meta: item.created_at_human || 'щойно',
          cover: item?.book?.cover || '',
        }));
      }
      if (this.activeFavoritesTab === 'discussions') {
        return (this.dashboard?.recent_discussions || []).map((item) => ({
          id: item.id,
          title: item.title || 'Обговорення',
          meta: item.created_at_human || 'щойно',
          cover: '',
        }));
      }
      if (this.activeFavoritesTab === 'quotes') {
        return (this.dashboard?.recent_quotes || []).map((item) => ({
          id: item.id,
          title: item?.book?.title || 'Цитата',
          meta: item.created_at_human || 'щойно',
          cover: item?.book?.cover || '',
        }));
      }
      return (this.dashboard?.recent_read_books || []).map((item) => ({
        id: item.id,
        title: item?.book?.title || 'Книга',
        meta: item.updated_at_human || 'щойно',
        cover: item?.book?.cover || '',
      }));
    },
  },
  data() {
    return {
      showPlannerModal: false,
      activeFavoritesTab: 'books',
      plannerBookQuery: '',
      plannerSearchResults: [],
      plannerSaving: false,
      plannerForm: {
        title: '',
        goal: '',
        target_date: '',
        book_ids: [],
      },
    };
  },
  methods: {
    async openPlannerModal() {
      this.showPlannerModal = true;
      this.plannerForm = { title: '', goal: '', target_date: '', book_ids: [] };
      this.plannerBookQuery = '';
      this.plannerSearchResults = [];
    },
    async searchPlannerBooks() {
      if (!this.plannerBookQuery.trim()) {
        this.plannerSearchResults = [];
        return;
      }
      const { data } = await axios.get('/api/account/books/search', { params: { q: this.plannerBookQuery.trim() } });
      this.plannerSearchResults = data?.books || [];
    },
    togglePlannerBook(bookId) {
      if (this.plannerForm.book_ids.includes(bookId)) {
        this.plannerForm.book_ids = this.plannerForm.book_ids.filter((id) => id !== bookId);
        return;
      }
      this.plannerForm.book_ids.push(bookId);
    },
    async savePlanner() {
      if (!this.plannerForm.title.trim()) {
        alert('Вкажіть назву плану читання.');
        return;
      }
      this.plannerSaving = true;
      try {
        await axios.post('/api/account/reading-plans', this.plannerForm);
        window.location.reload();
      } catch (e) {
        alert(e?.response?.data?.message || 'Не вдалося створити план читання.');
      } finally {
        this.plannerSaving = false;
      }
    },
    async togglePlanItem(plan, item) {
      try {
        await axios.put(`/api/account/reading-plans/${plan.id}/items/${item.id}`, {
          is_done: !item.is_done,
        });
        item.is_done = !item.is_done;
      } catch (e) {
        alert('Не вдалося оновити пункт плану.');
      }
    },
  },
};
</script>

