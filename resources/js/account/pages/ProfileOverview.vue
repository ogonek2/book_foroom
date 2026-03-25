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
            <div class="mt-1 text-xs text-white/55 text-right">план: {{ stats.planned_count || 0 }}</div>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-3">
            <div class="text-base font-extrabold">Улюблені</div>
            <div class="flex gap-1 text-xs">
              <button class="acc-btn !px-2 !py-1">Аніме</button>
              <button class="acc-btn !px-2 !py-1 opacity-70">Манга</button>
              <button class="acc-btn !px-2 !py-1 opacity-70">Ранобе</button>
            </div>
          </div>
          <div class="acc-glass rounded-2xl p-5 border border-white/10">
            <div v-if="recentBooks.length" class="space-y-3">
              <div v-for="item in recentBooks.slice(0, 3)" :key="item.id" class="flex items-center gap-3">
                <img v-if="item.book && item.book.cover" :src="item.book.cover" class="h-11 w-8 rounded object-cover border border-white/10" alt="" />
                <div v-else class="h-11 w-8 rounded bg-white/10 border border-white/10" />
                <div class="min-w-0">
                  <div class="text-sm font-semibold truncate">{{ item.book ? item.book.title : 'Книга' }}</div>
                  <div class="text-xs text-white/55">{{ item.updated_at_human || 'щойно' }}</div>
                </div>
              </div>
            </div>
            <template v-else>
              <div class="text-sm font-bold">Список поки порожній</div>
              <div class="text-xs text-white/60 mt-1">Тут зʼявляться останні книги з бібліотеки.</div>
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
                <span class="truncate">{{ col.name }}</span>
                <span class="text-white/55">{{ col.books_count }} кн.</span>
              </div>
            </div>
            <template v-else>
              <div class="text-sm font-bold">Колекції відсутні</div>
              <div class="text-xs text-white/60 mt-1">Створіть свою першу колекцію.</div>
            </template>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script lang="js">
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
      const total = done + active + planned;
      if (!total) return 6;
      return Math.min(100, Math.max(6, Math.round((done / total) * 100)));
    },
    avatarSrc() {
      if (this.profile && this.profile.avatar) return this.profile.avatar;

      const nickname = this.profile && this.profile.username ? this.profile.username : 'User';
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(nickname)}&background=7c3aed&color=fff&size=200`;
    },
  },
};
</script>

