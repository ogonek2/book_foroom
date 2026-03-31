<template>
  <div class="acc-card overflow-hidden">
    <div class="relative">
      <div class="h-44 sm:h-52">
        <div
          class="absolute inset-0"
          :style="{
            background: headerBackground,
          }"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent" />
        <div class="absolute inset-0 acc-bg-grid opacity-[0.35]" />

        <div class="absolute right-4 top-4 flex items-center gap-2">
          <button v-if="isOwner" type="button" class="acc-btn" @click="$emit('edit-header')">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z" />
            </svg>
            Шапка
          </button>
          <button v-if="isOwner" type="button" class="acc-btn-primary" @click="$emit('edit-profile')">
            Редагувати
          </button>
        </div>
      </div>

      <div class="px-4 pb-5 sm:px-6 sm:pb-6 -mt-10 sm:-mt-12">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
          <div
            class="h-20 w-20 sm:h-24 sm:w-24 rounded-2xl overflow-hidden border border-white/15 bg-white/10 flex items-center justify-center shrink-0"
            :style="{ boxShadow: `0 0 0 1px rgba(255,255,255,0.06), 0 0 34px ${theme.accent}40` }"
          >
            <img v-if="profile.avatar" :src="profile.avatar" class="h-full w-full object-cover" />
            <span v-else class="text-2xl font-extrabold text-white/90">
              {{ (profile.username ? profile.username[0] : 'U').toUpperCase() }}
            </span>
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight truncate">
                {{ profile.name || profile.username }}
              </h1>
              <span class="acc-pill">
                <span class="h-2 w-2 rounded-full" :style="{ backgroundColor: theme.accent }" />
                {{ '@' + profile.username }}
              </span>
              <span class="acc-pill">
                <span class="h-2 w-2 rounded-full" :style="{ backgroundColor: theme.secondary }" />
                Форум
              </span>
            </div>

            <div class="mt-2 text-sm text-white/70 leading-relaxed max-w-3xl">
              {{ profile.bio }}
            </div>

            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2">
              <div class="acc-glass rounded-xl p-3">
                <div class="text-[11px] text-white/55 font-semibold">Читаю</div>
                <div class="mt-1 font-extrabold">0</div>
              </div>
              <div class="acc-glass rounded-xl p-3">
                <div class="text-[11px] text-white/55 font-semibold">Заплановано</div>
                <div class="mt-1 font-extrabold">0</div>
              </div>
              <div class="acc-glass rounded-xl p-3">
                <div class="text-[11px] text-white/55 font-semibold">Завершено</div>
                <div class="mt-1 font-extrabold">0</div>
              </div>
              <div class="acc-glass rounded-xl p-3">
                <div class="text-[11px] text-white/55 font-semibold">Відкладено</div>
                <div class="mt-1 font-extrabold">0</div>
              </div>
            </div>
          </div>

          <div class="sm:text-right">
            <div class="text-xs text-white/55 font-semibold">Коротка шапка</div>
            <div class="mt-1 font-extrabold">{{ profile.header && profile.header.title ? profile.header.title : '—' }}</div>
            <div class="mt-1 text-xs text-white/60 max-w-[280px] sm:max-w-[240px]">
              {{ profile.header && profile.header.subtitle ? profile.header.subtitle : '' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'ProfileHeader',
  props: {
    profile: { type: Object, required: true },
    isOwner: { type: Boolean, default: false },
    theme: { type: Object, required: true },
  },
  computed: {
    headerBackground() {
      const a = this.theme.accent || '#7c3aed';
      const b = this.theme.secondary || '#2563eb';
      return `radial-gradient(circle at 18% 25%, ${a}66 0%, transparent 46%), radial-gradient(circle at 82% 20%, ${b}55 0%, transparent 42%), linear-gradient(135deg, rgba(255,255,255,0.08), transparent 35%)`;
    },
  },
};
</script>

