<template>
  <div class="relative min-h-[calc(100vh-6.5rem)] text-slate-900 dark:text-white acc-profile-page">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-screen h-full pointer-events-none z-0">
      <div
        v-if="headerImage"
        class="absolute top-0 left-1/2 -translate-x-1/2 w-screen h-[420px] sm:h-[500px] lg:h-[560px] overflow-hidden"
      >
        <img :src="headerImage" class="h-full w-full object-cover" alt="profile header background" />
      </div>
      <div class="absolute inset-0 w-screen bg-gradient-to-b from-slate-100/90 via-slate-100/88 to-slate-100/86 dark:from-dark-bg/80 dark:via-dark-bg/80 dark:to-dark-bg/80 backdrop-blur-md" />

      <div
        class="absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl opacity-35"
        :style="{ background: `radial-gradient(circle, ${theme.accent} 0%, transparent 65%)` }"
      />
      <div
        class="absolute top-10 -right-24 h-96 w-96 rounded-full blur-3xl opacity-25"
        :style="{ background: `radial-gradient(circle, ${theme.secondary} 0%, transparent 65%)` }"
      />
    </div>
    <div class="relative z-10 pb-10">
      <div class="mx-auto w-full pt-4">
          <div v-if="profile" class="mb-4 flex flex-wrap items-center gap-2">
            <router-link class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/overview`">Загальне</router-link>
            <router-link class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/library`">Бібліотека</router-link>
            <router-link class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/reviews`">Рецензії</router-link>
            <router-link class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/discussions`">Обговорення</router-link>
            <router-link class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/quotes`">Цитати</router-link>
            <router-link class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/collections`">Колекції</router-link>
            <router-link v-if="isOwner" class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/favorites`">Збережене</router-link>
            <router-link v-if="isOwner" class="acc-btn !px-3 !py-1.5" :to="`/u/${profile.username}/drafts`">Чернетки</router-link>
            <router-link v-if="isOwner" class="acc-btn-primary !px-3 !py-1.5" to="/settings/profile">Редагувати</router-link>
            <router-link v-if="isOwner" class="acc-btn !px-3 !py-1.5" to="/settings/design">Оформлення</router-link>
            <router-link v-if="isOwner" class="acc-btn !px-3 !py-1.5" to="/settings/security">Безпека</router-link>
            <router-link v-if="isOwner" class="acc-btn !px-3 !py-1.5" to="/settings/notifications">Сповіщення</router-link>
            <router-link v-if="isOwner" class="acc-btn !px-3 !py-1.5" to="/settings/privacy">Приватність</router-link>
            <router-link v-if="isOwner" class="acc-btn !px-3 !py-1.5" to="/settings/account">Акаунт</router-link>
          </div>

          <div v-if="loading" class="acc-glass rounded-2xl p-6 border border-slate-200/70 dark:border-white/10 text-sm text-slate-600 dark:text-white/70">
            Завантаження профілю...
          </div>
          <div v-else-if="loadError" class="acc-glass rounded-2xl p-6 border border-red-400/30 text-sm text-red-700 dark:text-red-200">
            {{ loadError }}
          </div>
          <transition name="acc-fade" mode="out-in">
            <router-view
              v-if="!loading && !loadError"
              :key="$route.fullPath"
              :profile="profile"
              :dashboard="dashboard"
              :is-owner="isOwner"
              @profile-updated="onProfileUpdated"
            />
          </transition>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

function getBootstrap() {
  return (window && window.__ACCOUNT_BOOTSTRAP__) || { viewer: null, profileUsername: null };
}

function normalizeUsername(input) {
  if (!input) return null;
  const raw = String(input);
  if (!raw) return null;

  // If server accidentally provided a SPA path (e.g. "u/john/overview"), extract username
  const match = raw.match(/(?:^|\/)u\/([^/]+)(?:\/|$)/i);
  if (match && match[1]) return match[1];

  // If it contains slashes, it's not a username
  if (raw.includes('/')) return null;

  return raw;
}

export default {
  name: 'AccountApp',
  data() {
    const boot = getBootstrap();
    return {
      viewer: boot.viewer || null,
      theme: {
        accent: '#7c3aed',
        secondary: '#2563eb',
      },
      profile: null,
      dashboard: null,
      isOwner: false,
      loading: true,
      loadError: '',
    };
  },
  computed: {
    headerImage() {
      return this.profile?.header?.image || '';
    },
  },
  watch: {
    '$route.params.username': {
      immediate: true,
      handler(value) {
        const boot = getBootstrap();
        const fallbackUsername = normalizeUsername(boot.profileUsername) || (boot.viewer && boot.viewer.username) || null;
        const username = normalizeUsername(value) || fallbackUsername;

        if (!username) {
          this.loading = false;
          this.loadError = 'Не вдалося визначити користувача профілю.';
          return;
        }

        this.fetchProfile(username);
      },
    },
  },
  methods: {
    async fetchProfile(username) {
      this.loading = true;
      this.loadError = '';

      try {
        const response = await axios.get(`/api/account/profile/${username}`);
        this.profile = response.data.profile;
        this.dashboard = response.data.dashboard || null;
        this.isOwner = Boolean(response.data.isOwner);
        this.theme = this.profile.theme || this.theme;
      } catch (error) {
        this.loadError = error?.response?.data?.message || 'Помилка завантаження профілю.';
      } finally {
        this.loading = false;
      }
    },
    onProfileUpdated(nextProfile) {
      this.profile = {
        ...this.profile,
        ...nextProfile,
      };

      const routeUsername = this.$route?.params?.username;
      if (routeUsername && nextProfile?.username && routeUsername !== nextProfile.username) {
        this.$router.replace({ name: 'acc.profile.overview', params: { username: nextProfile.username } });
      }
    },
  },
};
</script>

<style scoped>
.acc-fade-enter-active,
.acc-fade-leave-active {
  transition: opacity 160ms ease, transform 160ms ease;
}
.acc-fade-enter,
.acc-fade-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
</style>

