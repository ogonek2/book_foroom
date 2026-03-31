<template>
  <div>
    <div
      class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden transition"
      v-if="isOpen"
      @click="$emit('close')"
    />

    <aside
      class="fixed z-50 lg:sticky top-0 left-0 h-full lg:h-[calc(100vh-6.5rem)] w-[290px] lg:w-[320px] p-4 sm:p-6 transition-transform duration-200 ease-out"
      :class="[
        isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      ]"
      style="top: 6.5rem;"
    >
      <div class="acc-card h-full flex flex-col overflow-hidden">
        <div class="p-5 border-b border-white/10">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
              <div class="relative">
                <div
                  class="h-12 w-12 rounded-2xl overflow-hidden border border-white/10 bg-white/10 flex items-center justify-center"
                  :style="{ boxShadow: `0 0 0 1px rgba(255,255,255,0.06), 0 0 24px ${accent}44` }"
                >
                  <img v-if="profile && profile.avatar" :src="profile.avatar" class="h-full w-full object-cover" />
                  <span v-else class="text-lg font-extrabold text-white/90">
                    {{ (profile && profile.username ? profile.username[0] : 'U').toUpperCase() }}
                  </span>
                </div>
                <div
                  class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border border-white/10"
                  :style="{ backgroundColor: isOwner ? '#22c55e' : '#64748b' }"
                  :title="isOwner ? 'Власник' : 'Перегляд'"
                />
              </div>

              <div class="min-w-0">
                <div class="truncate font-extrabold leading-tight">
                  {{ profileLabel }}
                </div>
                <div class="truncate text-xs text-white/55 font-semibold">
                  {{ isOwner ? 'Ваш кабінет' : 'Публічний перегляд' }}
                </div>
              </div>
            </div>

            <button type="button" class="acc-btn lg:hidden" @click="$emit('close')" aria-label="Закрити меню">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div class="flex-1 overflow-auto acc-scrollbar p-3 sm:p-4">
          <div class="px-2 pb-2 text-[11px] font-extrabold uppercase tracking-widest text-white/40">
            Профіль
          </div>
          <div class="space-y-1">
            <AccNavItem :to="`/u/${username}/overview`" icon="user">Огляд</AccNavItem>
            <AccNavItem :to="`/u/${username}/library`" icon="book">Бібліотека</AccNavItem>
            <AccNavItem :to="`/u/${username}/reviews`" icon="star">Рецензії</AccNavItem>
            <AccNavItem :to="`/u/${username}/discussions`" icon="chat">Обговорення</AccNavItem>
            <AccNavItem :to="`/u/${username}/quotes`" icon="quote">Цитати</AccNavItem>
            <AccNavItem :to="`/u/${username}/collections`" icon="grid">Колекції</AccNavItem>
          </div>

          <div class="mt-6 px-2 pb-2 text-[11px] font-extrabold uppercase tracking-widest text-white/40">
            Кабінет
          </div>
          <div class="space-y-1">
            <AccNavItem to="/settings" icon="settings" :disabled="!viewer">Налаштування</AccNavItem>
          </div>

          <div v-if="!viewer" class="mt-5 px-2">
            <div class="acc-glass rounded-2xl p-4 border border-white/10">
              <div class="text-sm font-extrabold">Увійдіть</div>
              <div class="mt-1 text-xs text-white/60 leading-relaxed">
                Налаштування доступні лише авторизованим користувачам.
              </div>
              <a href="/login" class="acc-btn-primary w-full justify-center mt-3">Увійти</a>
            </div>
          </div>
        </div>

        <div class="p-4 border-t border-white/10">
          <div class="flex items-center justify-between gap-3">
            <a class="acc-btn w-full justify-center" :href="`/users/${username}`">
              Публічний профіль
            </a>
            <a v-if="isOwner" class="acc-btn w-full justify-center" href="/profile">
              Стара приватна
            </a>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>

<script lang="js">
import AccNavItem from './AccNavItem.vue';

export default {
  name: 'AccountSidebar',
  components: { AccNavItem },
  props: {
    isOpen: { type: Boolean, default: false },
    viewer: { type: Object, default: null },
    profile: { type: Object, required: true },
    isOwner: { type: Boolean, default: false },
    accent: { type: String, default: '#7c3aed' },
  },
  computed: {
    username() {
      return (this.profile && this.profile.username) || 'guest';
    },
    profileLabel() {
      const u = this.profile;
      if (!u) return 'Профіль';
      return u.name || u.username || 'Профіль';
    },
  },
};
</script>

