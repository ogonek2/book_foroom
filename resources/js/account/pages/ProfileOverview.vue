<template>
  <div class="space-y-5">
    <section class="acc-glass rounded-2xl p-4 sm:p-5 border border-white/10">
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
        <div class="lg:col-span-8 flex flex-col sm:flex-row items-center gap-4 sm:gap-5">
          <div class="h-36 w-36 sm:h-44 sm:w-44 lg:h-50 lg:w-50 aspect-[1/1] rounded-full p-[3px] shrink-0 acc-avatar-ring"
            :style="{ backgroundImage: avatarRingGradient }">
            <div class="h-full w-full rounded-full overflow-hidden border border-white/15 bg-white/10">
              <img :src="avatarSrc" class="h-full w-full object-cover" alt="avatar" />
            </div>
          </div>
          <div class="min-w-0 w-full text-center sm:text-left">
            <div v-if="profile && profile.name" class="text-xl sm:text-2xl font-extrabold break-words sm:truncate">{{ profile.name }}
            </div>
            <div v-else class="text-xl sm:text-2xl font-extrabold">Користувач</div>
            <div class="text-sm text-white/60">@{{ profile && profile.username ? profile.username : 'guest' }}</div>
            <div v-if="profile && profile.header && profile.header.title"
              class="mt-1 text-xs font-bold acc-accent-text break-words sm:truncate">
              {{ profile.header.title }}
            </div>
            <div v-if="profile && profile.header && profile.header.subtitle"
              class="mt-0.5 text-xs text-white/60 break-words sm:truncate">
              {{ profile.header.subtitle }}
            </div>
            <div v-if="profile && profile.bio" class="mt-2 text-xs text-white/70 line-clamp-2">
              {{ profile.bio }}
            </div>
            <div class="mt-1 flex flex-wrap justify-center sm:justify-start items-center gap-x-5 gap-y-1 text-xs text-white/65">
              <span>{{ stats.reviews_count || 0 }} рецензій</span>
              <span>{{ stats.discussions_count || 0 }} обговорень</span>
            </div>
          </div>
        </div>

        <div class="lg:col-span-4">
          <div class="acc-glass rounded-2xl p-3 border border-white/10">
            <div class="flex gap-2 mb-3 text-xs">
              <button class="acc-btn !px-3 !py-1" :class="{ 'opacity-70': overviewStatsTab !== 'books' }"
                @click="overviewStatsTab = 'books'">Книги</button>
              <button class="acc-btn !px-3 !py-1" :class="{ 'opacity-70': overviewStatsTab !== 'reviews' }"
                @click="overviewStatsTab = 'reviews'">Рецензії</button>
              <button class="acc-btn !px-3 !py-1" :class="{ 'opacity-70': overviewStatsTab !== 'discussions' }"
                @click="overviewStatsTab = 'discussions'">Обговорення</button>
              <!-- <button class="acc-btn !px-3 !py-1" :class="{ 'opacity-70': overviewStatsTab !== 'quotes' }" @click="overviewStatsTab = 'quotes'">Цитати</button> -->
            </div>
            <div class="grid grid-cols-[86px,1fr] items-center gap-3">
              <div class="relative h-[86px] w-[86px] rounded-full">
                <svg class="h-[86px] w-[86px] -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                  <circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" class="text-white/15"
                    stroke-width="8" />
                  <circle cx="50" cy="50" r="44" fill="none" :stroke="overviewRingColor" stroke-width="8"
                    stroke-linecap="round" :stroke-dasharray="overviewRingDasharray" stroke-dashoffset="0" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="text-2xl font-extrabold" :style="{ color: overviewRingColor }">{{ overviewMainValue }}
                  </div>
                </div>
              </div>
              <ul class="space-y-1 text-xs text-white/70">
                <li v-for="row in overviewRows" :key="row.key" class="flex justify-between">
                  <span>{{ row.label }}</span><span>{{ row.value }}</span>
                </li>
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
              <span v-for="(bar, idx) in activityBars" :key="`bar-${idx}`"
                class="w-1 rounded-full bg-gradient-to-t from-indigo-400 to-purple-500"
                :style="{ height: `${bar.height}px` }" :title="`${bar.label}: ${bar.value}`" />
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-[11px] text-white/70">
              <div class="rounded-lg border border-white/10 bg-white/5 px-2 py-1">
                За 14 днів: <span class="font-bold text-white/90">{{ activitySummary.total }}</span>
              </div>
              <div class="rounded-lg border border-white/10 bg-white/5 px-2 py-1">
                Сьогодні: <span class="font-bold text-white/90">{{ activitySummary.today }}</span>
              </div>
              <div class="rounded-lg border border-white/10 bg-white/5 px-2 py-1">
                Середнє/день: <span class="font-bold text-white/90">{{ activitySummary.avg }}</span>
              </div>
              <div class="rounded-lg border border-white/10 bg-white/5 px-2 py-1">
                Пік: <span class="font-bold text-white/90">{{ activitySummary.peakLabel }}</span>
              </div>
            </div>
            <div class="mt-2 text-[11px] text-white/55">
              Враховуються дії: оновлення статусів книг, рецензії, обговорення, цитати.
            </div>
          </div>
          <div class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="text-xs text-white/60 mb-3">Прогрес читання</div>
            <div class="text-2xl font-extrabold">{{ stats.read_count || 0 }} книг</div>
            <div class="mt-3 h-2 rounded-full bg-white/10 overflow-hidden">
              <div class="h-full bg-gradient-to-r from-purple-400 to-indigo-500"
                :style="{ width: `${progressWidth}%` }" />
            </div>
            <div class="mt-1 text-xs text-white/55 text-right">план: {{ stats.planned_count || 0 }} | цілі: {{
              stats.planner_done_items || 0 }}/{{ stats.planner_total_items || 0 }}</div>
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
          <div class="flex items-start md:items-center flex-col md:flex-row justify-between mb-3 gap-2">
            <div class="text-base font-extrabold">Улюблені</div>
            <div class="flex gap-1 text-xs">
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'books' }"
                @click="activeFavoritesTab = 'books'">Книги</button>
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'reviews' }"
                @click="activeFavoritesTab = 'reviews'">Рецензії</button>
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'discussions' }"
                @click="activeFavoritesTab = 'discussions'">Обговорення</button>
              <button class="acc-btn !px-2 !py-1" :class="{ 'opacity-70': activeFavoritesTab !== 'quotes' }"
                @click="activeFavoritesTab = 'quotes'">Цитати</button>
            </div>
          </div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div v-if="favoriteItems.length" class="space-y-3">
              <div v-for="item in favoriteItems.slice(0, 3)" :key="`${activeFavoritesTab}-${item.id}`"
                class="flex items-center gap-3">
                <img :src="resolveBookCover(item.cover)" class="h-11 w-8 rounded object-cover border border-white/10"
                  alt="" @error="onCoverError" />
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
              <div class="flex justify-between"><span class="text-white/70">Рецензії</span><span class="font-bold">{{
                stats.reviews_count || 0 }}</span></div>
              <div class="flex justify-between"><span class="text-white/70">Обговорення</span><span class="font-bold">{{
                stats.discussions_count || 0 }}</span></div>
              <div class="flex justify-between"><span class="text-white/70">Цитати</span><span class="font-bold">{{
                stats.quotes_count || 0 }}</span></div>
              <div class="flex justify-between"><span class="text-white/70">Колекції</span><span class="font-bold">{{
                stats.collections_count || 0 }}</span></div>
            </div>
          </div>
        </div>
        <div>
          <div class="text-base font-extrabold mb-3">Нагороди</div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div v-if="awards.length" class="grid grid-cols-2 gap-2">
              <button v-for="award in awards.slice(0, 8)" :key="`award-${award.id}`" type="button"
                class="rounded-xl border border-white/10 bg-white/5 p-2 text-left transition hover:bg-white/10"
                @click="openAwardModal(award)">
                <div class="flex items-center gap-2">
                  <div class="flex items-center">
                    <img v-if="award.image" :src="award.image" :alt="award.name"
                      class="h-8 w-8 rounded-full object-cover border border-white/10" />
                    <div v-else
                      class="h-8 w-8 rounded-full flex items-center justify-center text-[11px] font-bold text-white/95"
                      :style="{ backgroundColor: award.color || '#8b5cf6' }">
                      {{ (award.name || '?').slice(0, 1).toUpperCase() }}
                    </div>
                  </div>
                  <div class="min-w-0">
                    <div class="text-xs font-bold truncate">{{ award.name }}</div>
                    <div v-if="award.points" class="text-[11px] text-white/60">+{{ award.points }} балів</div>
                  </div>
                </div>
              </button>
            </div>
            <template v-else>
              <div class="text-sm font-bold">Нагород поки немає</div>
              <div class="text-xs text-white/60 mt-1">Після активності в профілі тут з’являться ваші відзнаки.</div>
            </template>
          </div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-3">
            <div class="text-base font-extrabold">Колекції</div>
            <router-link class="acc-btn !px-3 !py-1"
              :to="`/u/${profile && profile.username ? profile.username : 'guest'}/collections`">→</router-link>
          </div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div v-if="collections.length" class="space-y-2">
              <div v-for="col in collections.slice(0, 3)" :key="col.id"
                class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="grid grid-cols-3 gap-0.5 h-8 w-14 shrink-0">
                    <template v-if="col.preview_covers && col.preview_covers.length">
                      <img v-for="(cover, idx) in col.preview_covers.slice(0, 3)" :key="`cover-${col.id}-${idx}`"
                        :src="resolveBookCover(cover)" class="h-8 w-full rounded-sm object-cover border border-white/10" alt="" @error="onCoverError">
                    </template>
                    <template v-else>
                      <div v-for="idx in 3" :key="`empty-${col.id}-${idx}`"
                        class="h-8 rounded-sm bg-white/10 border border-white/10" />
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
              <div v-for="plan in readingPlans" :key="`plan-${plan.id}`"
                class="rounded-xl border border-white/10 bg-white/5 p-3">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-bold text-sm">{{ plan.title }}</div>
                  <div class="text-xs text-white/60">{{ plan.done_items }}/{{ plan.total_items }}</div>
                </div>
                <div class="mt-2 h-1.5 rounded-full bg-white/10 overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-500"
                    :style="{ width: `${plan.progress || 0}%` }" />
                </div>
                <div class="mt-2 space-y-1">
                  <label v-for="item in plan.items.slice(0, 4)" :key="`plan-item-${item.id}`"
                    class="flex items-center gap-2 text-xs">
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
      <div class="acc-modal-overlay" @click="showPlannerModal = false" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-2xl">
          <div class="text-base font-extrabold">Новий план читання</div>
          <div class="mt-3 space-y-3">
            <input v-model.trim="plannerForm.title" type="text" class="acc-modal-input" placeholder="Назва плану">
            <textarea v-model.trim="plannerForm.goal" rows="3" class="acc-modal-input" placeholder="Мета"></textarea>
            <input v-model="plannerForm.target_date" type="date" class="acc-modal-input">
            <div class="acc-modal-subpanel">
              <div class="flex gap-2">
                <input v-model.trim="plannerBookQuery" type="text" class="acc-modal-input"
                  placeholder="Знайти книги для плану">
                <button class="acc-btn" @click="searchPlannerBooks">Пошук</button>
              </div>
              <div v-if="plannerSearchResults.length" class="mt-2 max-h-48 overflow-auto space-y-1">
                <label v-for="book in plannerSearchResults" :key="`plan-search-${book.id}`"
                  class="flex items-center gap-2 text-xs">
                  <input type="checkbox" :checked="plannerForm.book_ids.includes(book.id)"
                    @change="togglePlannerBook(book.id)">
                  <span class="truncate">{{ book.title }}</span>
                </label>
              </div>
            </div>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button class="acc-btn" @click="showPlannerModal = false">Скасувати</button>
            <button class="acc-btn-primary" :disabled="plannerSaving" @click="savePlanner">{{ plannerSaving ?
              'Створення...'
              : 'Створити план' }}</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="awardModal" class="fixed inset-0 z-[145]">
      <div class="acc-modal-overlay" @click="awardModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-md">
          <div class="flex items-center gap-3">
            <img v-if="awardModal.image" :src="awardModal.image" :alt="awardModal.name"
              class="h-12 w-12 rounded-full object-cover border border-white/10" />
            <div v-else class="h-12 w-12 rounded-full flex items-center justify-center text-sm font-bold text-white"
              :style="{ backgroundColor: awardModal.color || '#8b5cf6' }">
              {{ (awardModal.name || '?').slice(0, 1).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <div class="text-base font-extrabold truncate">{{ awardModal.name }}</div>
              <div v-if="awardModal.points" class="text-xs text-white/60">+{{ awardModal.points }} балів</div>
            </div>
          </div>

          <div v-if="awardModal.description" class="mt-3 text-sm text-white/80">
            {{ awardModal.description }}
          </div>
          <div class="mt-3 space-y-1 text-xs text-white/65">
            <div v-if="awardModal.awarded_at_human">Отримано: {{ awardModal.awarded_at_human }}</div>
            <div v-if="awardModal.awarded_at">Дата: {{ awardModal.awarded_at }}</div>
            <div v-if="awardModal.note">Примітка: {{ awardModal.note }}</div>
          </div>

          <div class="mt-4 flex justify-end">
            <button class="acc-btn" type="button" @click="awardModal = null">Закрити</button>
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
    awards() {
      return this.dashboard?.awards || [];
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
    avatarRingGradient() {
      const accent = this.profile?.theme?.accent || '#7c3aed';
      const secondary = this.profile?.theme?.secondary || '#2563eb';
      return `linear-gradient(120deg, ${accent}, ${secondary}, ${accent})`;
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
    activityBars() {
      const series = this.dashboard?.activity_series || [];
      const max = Number(this.stats.activity_max || 1);
      if (!series.length) {
        return Array.from({ length: 14 }).map(() => ({ value: 0, label: '', height: 8 }));
      }
      return series.map((point) => {
        const value = Number(point.value || 0);
        const height = Math.max(8, Math.round((value / max) * 40));
        return {
          value,
          label: point.label || '',
          height,
        };
      });
    },
    activitySummary() {
      const series = this.dashboard?.activity_series || [];
      if (!series.length) {
        return { total: 0, today: 0, avg: 0, peakLabel: '—' };
      }
      const values = series.map((p) => Number(p.value || 0));
      const total = values.reduce((acc, v) => acc + v, 0);
      const today = values[values.length - 1] || 0;
      const avg = (total / values.length).toFixed(1);
      let peakIndex = 0;
      for (let i = 1; i < values.length; i += 1) {
        if (values[i] > values[peakIndex]) peakIndex = i;
      }
      const peakValue = values[peakIndex] || 0;
      const peakLabel = peakValue > 0 ? `${series[peakIndex].label}: ${peakValue}` : '—';
      return { total, today, avg, peakLabel };
    },
    overviewMainValue() {
      if (this.overviewStatsTab === 'reviews') return Number(this.stats.reviews_count || 0);
      if (this.overviewStatsTab === 'discussions') return Number(this.stats.discussions_count || 0);
      if (this.overviewStatsTab === 'quotes') return Number(this.stats.quotes_count || 0);
      return Number(this.stats.read_count || 0);
    },
    overviewRows() {
      if (this.overviewStatsTab === 'reviews') {
        return [
          { key: 'reviews-total', label: 'Усього', value: Number(this.stats.reviews_count || 0) },
          { key: 'reviews-fav', label: 'Збережені', value: Number(this.stats.favorite_reviews_count || 0) },
          { key: 'reviews-rated', label: 'Оцінено книг', value: Number(this.stats.rated_count || 0) },
          { key: 'reviews-avg', label: 'Сер. оцінка', value: this.stats.average_rating ?? '—' },
        ];
      }
      if (this.overviewStatsTab === 'discussions') {
        return [
          { key: 'disc-total', label: 'Усього', value: Number(this.stats.discussions_count || 0) },
          { key: 'disc-drafts', label: 'Чернетки', value: Number((this.dashboard?.draft_discussions || []).length) },
          { key: 'disc-activity', label: 'Активність 14д', value: Number(this.activitySummary.total || 0) },
          { key: 'disc-today', label: 'Сьогодні', value: Number(this.activitySummary.today || 0) },
        ];
      }
      if (this.overviewStatsTab === 'quotes') {
        return [
          { key: 'quote-total', label: 'Усього', value: Number(this.stats.quotes_count || 0) },
          { key: 'quote-fav', label: 'Збережені', value: Number(this.stats.favorite_quotes_count || 0) },
          { key: 'quote-drafts', label: 'Чернетки', value: Number((this.dashboard?.draft_quotes || []).length) },
          { key: 'quote-today', label: 'Сьогодні', value: Number(this.activitySummary.today || 0) },
        ];
      }
      return [
        { key: 'book-reading', label: 'Читаю', value: Number(this.stats.reading_count || 0) },
        { key: 'book-planned', label: 'Заплановано', value: Number(this.stats.planned_count || 0) },
        { key: 'book-dropped', label: 'Закинуто', value: Number(this.stats.dropped_count || 0) },
        { key: 'book-total', label: 'Усього книг', value: Number(this.stats.total_books_count || 0) },
      ];
    },
    overviewRingColor() {
      if (this.overviewStatsTab === 'reviews') return '#f59e0b';
      if (this.overviewStatsTab === 'discussions') return '#06b6d4';
      if (this.overviewStatsTab === 'quotes') return '#10b981';
      return '#8b5cf6';
    },
    overviewRingPercent() {
      if (this.overviewStatsTab === 'reviews') {
        const total = Number(this.stats.reviews_count || 0);
        const fav = Number(this.stats.favorite_reviews_count || 0);
        if (!total) return 0;
        return Math.min(100, Math.round((fav / total) * 100));
      }
      if (this.overviewStatsTab === 'discussions') {
        const total = Number(this.stats.discussions_count || 0);
        const today = Number(this.activitySummary.today || 0);
        if (!total) return 0;
        return Math.min(100, Math.round((today / total) * 100));
      }
      if (this.overviewStatsTab === 'quotes') {
        const total = Number(this.stats.quotes_count || 0);
        const fav = Number(this.stats.favorite_quotes_count || 0);
        if (!total) return 0;
        return Math.min(100, Math.round((fav / total) * 100));
      }
      const totalBooks = Number(this.stats.total_books_count || 0);
      const read = Number(this.stats.read_count || 0);
      if (!totalBooks) return 0;
      return Math.min(100, Math.round((read / totalBooks) * 100));
    },
    overviewRingDasharray() {
      const circumference = 2 * Math.PI * 44;
      const progress = (this.overviewRingPercent / 100) * circumference;
      return `${progress} ${circumference}`;
    },
  },
  data() {
    return {
      showPlannerModal: false,
      overviewStatsTab: 'books',
      activeFavoritesTab: 'books',
      plannerBookQuery: '',
      plannerSearchResults: [],
      plannerSaving: false,
      awardModal: null,
      plannerForm: {
        title: '',
        goal: '',
        target_date: '',
        book_ids: [],
      },
    };
  },
  methods: {
    resolveBookCover(cover) {
      return cover || '/images/placeholders/book-cover.svg';
    },
    onCoverError(event) {
      const fallback = '/images/placeholders/book-cover.svg';
      if (event?.target && !String(event.target.src || '').includes(fallback)) {
        event.target.src = fallback;
      }
    },
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
    openAwardModal(award) {
      this.awardModal = award || null;
    },
  },
};
</script>

<style scoped>
.acc-avatar-ring {
  background-size: 200% 200%;
  animation: acc-avatar-flow 5.5s ease-in-out infinite;
  box-shadow: 0 0 20px color-mix(in srgb, var(--acc-accent, #7c3aed) 35%, transparent);
}

@keyframes acc-avatar-flow {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}
</style>
