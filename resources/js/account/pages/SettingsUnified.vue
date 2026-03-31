<template>
  <div class="space-y-4">
    <div class="lg:hidden">
      <button class="acc-btn" type="button" @click="mobileNavOpen = true">
        <i class="fas fa-bars" aria-hidden="true"></i>
        <span>Розділи налаштувань</span>
      </button>
    </div>

    <div v-if="mobileNavOpen" class="fixed inset-0 z-[160] lg:hidden">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileNavOpen = false" />
      <aside class="absolute inset-y-0 left-0 w-[85%] max-w-sm acc-glass rounded-r-2xl border-r border-white/15 p-4 overflow-auto">
        <div class="flex items-center justify-between mb-3">
          <div class="font-extrabold">Налаштування</div>
          <button class="acc-btn !px-2 !py-1" type="button" @click="mobileNavOpen = false">✕</button>
        </div>
        <div class="space-y-2">
          <button
            v-for="item in navItems"
            :key="`m-${item.id}`"
            class="acc-btn w-full justify-start"
            :class="{ 'acc-settings-nav-active': currentSection === item.id }"
            type="button"
            @click="scrollToSection(item.id)"
          >
            <i :class="item.icon" aria-hidden="true"></i>
            <span>{{ item.label }}</span>
          </button>
        </div>
      </aside>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:items-start lg:grid-cols-[260px,minmax(0,1fr)] xl:grid-cols-[280px,minmax(0,1fr)]">
      <aside class="hidden lg:block min-w-0 lg:sticky lg:top-24 lg:self-start h-fit">
        <div class="acc-glass rounded-2xl border border-white/10 p-3">
          <div class="px-2 pb-2 text-[11px] font-extrabold uppercase tracking-widest text-slate-500 dark:text-white/40">
            Налаштування
          </div>
          <div class="space-y-2">
            <button
              v-for="item in navItems"
              :key="item.id"
              class="acc-btn w-full justify-start"
              :class="{ 'acc-settings-nav-active': currentSection === item.id }"
              type="button"
              @click="scrollToSection(item.id)"
            >
              <i :class="item.icon" aria-hidden="true"></i>
              <span>{{ item.label }}</span>
            </button>
          </div>
        </div>
      </aside>

      <div class="space-y-5 min-w-0">
        <section id="settings-profile" class="scroll-mt-24 min-w-0 overflow-hidden">
          <SettingsProfile :profile="profile" :is-owner="isOwner" @profile-updated="forwardProfileUpdated" />
        </section>

        <section id="settings-design" class="scroll-mt-24 min-w-0 overflow-hidden">
          <SettingsDesign :profile="profile" :is-owner="isOwner" @profile-updated="forwardProfileUpdated" />
        </section>

        <section id="settings-security" class="scroll-mt-24 min-w-0 overflow-hidden">
          <SettingsSecurity />
        </section>

        <section id="settings-notifications" class="scroll-mt-24 min-w-0 overflow-hidden">
          <SettingsNotifications :profile="profile" />
        </section>

        <section id="settings-privacy" class="scroll-mt-24 min-w-0 overflow-hidden">
          <SettingsPrivacy :profile="profile" />
        </section>

        <section id="settings-account" class="scroll-mt-24 min-w-0 overflow-hidden">
          <SettingsAccount />
        </section>

        <section id="settings-history" class="scroll-mt-24 acc-glass rounded-2xl p-5 border border-white/10">
          <div class="text-lg font-extrabold tracking-tight">Історія моїх дій</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-white/65">Останні зміни та публікації в акаунті.</div>
          <div v-if="historyItems.length" class="mt-4 space-y-2">
            <div v-for="item in historyItems.slice(0, 20)" :key="item.key" class="rounded-xl border border-white/10 bg-white/5 p-3 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-semibold truncate">
                  <i :class="item.icon" class="mr-2" aria-hidden="true"></i>
                  {{ item.title }}
                </div>
                <div class="mt-1 text-xs text-slate-600 dark:text-white/60">{{ item.subtitle }}</div>
              </div>
              <div class="text-xs text-slate-500 dark:text-white/55 whitespace-nowrap">{{ item.human }}</div>
            </div>
          </div>
          <div v-else class="mt-3 text-sm text-slate-500 dark:text-white/55">Поки немає подій для відображення.</div>
        </section>

        <section id="settings-rating" class="scroll-mt-24 acc-glass rounded-2xl p-5 border border-white/10">
          <div class="text-lg font-extrabold tracking-tight">Мій рейтинг</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-white/65">Рейтинг рахується на основі вашої активності на сайті.</div>
          <div class="mt-4 grid grid-cols-1 gap-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
              <div class="text-xs text-slate-600 dark:text-white/60">Поточний бал</div>
              <div class="mt-1 text-3xl font-extrabold">{{ ratingScore }}</div>
              <div class="mt-1 text-sm text-amber-500 dark:text-amber-300">
                <i class="fas fa-star mr-1" aria-hidden="true"></i>{{ ratingStars }} зірок
              </div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
              <div class="text-xs text-slate-600 dark:text-white/60 mb-2">Як працює рейтинг</div>
              <ul class="space-y-1 text-xs text-slate-700 dark:text-white/80">
                <li v-for="rule in ratingRules" :key="rule">{{ rule }}</li>
              </ul>
            </div>
          </div>
        </section>

        <section id="settings-session" class="scroll-mt-24 acc-glass rounded-2xl p-5 border border-white/10">
          <div class="text-lg font-extrabold tracking-tight">Сеанс і акаунт</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-white/65">Керування активним сеансом та видаленням облікового запису.</div>

          <div class="mt-4 flex flex-wrap gap-2">
            <button class="acc-btn" type="button" :disabled="sessionBusy" @click="logout">
              <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
              <span>Вийти</span>
            </button>
          </div>

          <div class="mt-4 rounded-xl border border-red-400/35 bg-red-500/10 p-4">
            <div class="font-bold text-red-700 dark:text-red-300">Небезпечна зона</div>
            <div class="mt-1 text-sm text-red-700/90 dark:text-red-200/90">
              Видалення акаунта необоротне. Буде виконано вихід із системи.
            </div>
            <button class="mt-3 acc-btn !border-red-500/45 !text-red-700 dark:!text-red-200" type="button" :disabled="sessionBusy" @click="deleteAccount">
              <i class="fas fa-trash-alt" aria-hidden="true"></i>
              <span>Видалити обліковий запис</span>
            </button>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import SettingsProfile from './SettingsProfile.vue';
import SettingsDesign from './SettingsDesign.vue';
import SettingsSecurity from './SettingsSecurity.vue';
import SettingsNotifications from './SettingsNotifications.vue';
import SettingsPrivacy from './SettingsPrivacy.vue';
import SettingsAccount from './SettingsAccount.vue';

export default {
  name: 'SettingsUnified',
  components: {
    SettingsProfile,
    SettingsDesign,
    SettingsSecurity,
    SettingsNotifications,
    SettingsPrivacy,
    SettingsAccount,
  },
  props: {
    profile: { type: Object, default: null },
    dashboard: { type: Object, default: null },
    isOwner: { type: Boolean, default: false },
  },
  data() {
    return {
      mobileNavOpen: false,
      sessionBusy: false,
      currentSection: 'settings-profile',
      sectionObserver: null,
      ratingRules: [
        'Рецензія (основна): +10',
        'Цитата (публічна): +5',
        'Публікація: +15',
        'Оцінка книги: +2',
        'Статус "Прочитано": +3',
        'Обговорення: +8',
        'Відповідь в обговоренні: +3',
        'Відповідь у рецензії: +2',
        'Максимум загального рейтингу: 100',
      ],
    };
  },
  computed: {
    navItems() {
      return [
        { id: 'settings-profile', label: 'Профіль', icon: 'fas fa-user' },
        { id: 'settings-design', label: 'Оформлення', icon: 'fas fa-palette' },
        { id: 'settings-security', label: 'Безпека', icon: 'fas fa-shield-alt' },
        { id: 'settings-notifications', label: 'Сповіщення', icon: 'fas fa-bell' },
        { id: 'settings-privacy', label: 'Приватність', icon: 'fas fa-user-shield' },
        { id: 'settings-account', label: 'Акаунт', icon: 'fas fa-file-export' },
        { id: 'settings-history', label: 'Історія дій', icon: 'fas fa-history' },
        { id: 'settings-rating', label: 'Мій рейтинг', icon: 'fas fa-star' },
        { id: 'settings-session', label: 'Сеанс', icon: 'fas fa-sign-out-alt' },
      ];
    },
    stats() {
      return this.dashboard?.stats || {};
    },
    ratingScore() {
      const value = Number(this.stats.rating_score || 1);
      return Number.isFinite(value) ? value.toFixed(1) : '1.0';
    },
    ratingStars() {
      const value = Number(this.stats.rating_stars || 1);
      return Number.isFinite(value) ? value : 1;
    },
    historyItems() {
      const books = (this.dashboard?.recent_read_books || []).map((item) => ({
        key: `book-${item.id}`,
        icon: 'fas fa-book text-indigo-400',
        title: item?.book?.title || 'Оновлено статус книги',
        subtitle: `Статус: ${item?.status || '—'}`,
        human: item?.updated_at_human || 'щойно',
        ts: Date.parse(item?.updated_at || '') || 0,
      }));
      const reviews = (this.dashboard?.recent_reviews || []).map((item) => ({
        key: `review-${item.id}`,
        icon: 'fas fa-pen text-emerald-400',
        title: item?.book?.title || 'Нова рецензія',
        subtitle: `Рейтинг: ${item?.rating ?? '—'}`,
        human: item?.created_at_human || 'щойно',
        ts: Date.parse(item?.created_at || '') || 0,
      }));
      const discussions = (this.dashboard?.recent_discussions || []).map((item) => ({
        key: `discussion-${item.id}`,
        icon: 'fas fa-comments text-cyan-400',
        title: item?.title || 'Обговорення',
        subtitle: `Відповідей: ${item?.replies_count || 0}, лайків: ${item?.likes_count || 0}`,
        human: item?.created_at_human || 'щойно',
        ts: Date.parse(item?.created_at || '') || 0,
      }));
      const quotes = (this.dashboard?.recent_quotes || []).map((item) => ({
        key: `quote-${item.id}`,
        icon: 'fas fa-quote-left text-fuchsia-400',
        title: item?.book?.title || 'Нова цитата',
        subtitle: 'Опублікована цитата',
        human: item?.created_at_human || 'щойно',
        ts: Date.parse(item?.created_at || '') || 0,
      }));

      return [...books, ...reviews, ...discussions, ...quotes]
        .sort((a, b) => b.ts - a.ts)
        .map((item) => ({ ...item, ts: undefined }));
    },
  },
  methods: {
    forwardProfileUpdated(payload) {
      this.$emit('profile-updated', payload);
    },
    scrollToSection(id) {
      this.currentSection = id;
      const target = document.getElementById(id);
      if (!target) return;
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      window.history.replaceState({}, '', `#${id}`);
      this.mobileNavOpen = false;
    },
    buildPostForm(action, extras = {}) {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = action;
      form.style.display = 'none';

      const tokenInput = document.createElement('input');
      tokenInput.type = 'hidden';
      tokenInput.name = '_token';
      tokenInput.value = token;
      form.appendChild(tokenInput);

      Object.entries(extras).forEach(([key, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
      });

      document.body.appendChild(form);
      return form;
    },
    logout() {
      if (this.sessionBusy) return;
      this.sessionBusy = true;
      const form = this.buildPostForm('/logout');
      form.submit();
    },
    deleteAccount() {
      if (this.sessionBusy) return;
      const confirmed = window.confirm('Видалити обліковий запис? Цю дію неможливо скасувати.');
      if (!confirmed) return;
      this.sessionBusy = true;
      const form = this.buildPostForm('/profile', { _method: 'DELETE' });
      form.submit();
    },
    initSectionObserver() {
      const ids = this.navItems.map((item) => item.id);
      const targets = ids
        .map((id) => document.getElementById(id))
        .filter(Boolean);
      if (!targets.length) return;

      if (this.sectionObserver) {
        this.sectionObserver.disconnect();
      }

      this.sectionObserver = new IntersectionObserver(
        (entries) => {
          const visible = entries
            .filter((entry) => entry.isIntersecting)
            .sort((a, b) => b.intersectionRatio - a.intersectionRatio);
          if (!visible.length) return;
          const top = visible[0].target?.id;
          if (top) this.currentSection = top;
        },
        {
          root: null,
          rootMargin: '-90px 0px -55% 0px',
          threshold: [0.15, 0.35, 0.6],
        },
      );

      targets.forEach((el) => this.sectionObserver.observe(el));
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.initSectionObserver();
      const hash = window.location.hash ? window.location.hash.replace('#', '') : '';
      if (hash && this.navItems.some((item) => item.id === hash)) {
        this.currentSection = hash;
      }
    });
  },
  beforeDestroy() {
    if (this.sectionObserver) {
      this.sectionObserver.disconnect();
      this.sectionObserver = null;
    }
  },
};
</script>

<style scoped>
.acc-settings-nav-active {
  border-color: var(--acc-accent, #7c3aed) !important;
  box-shadow: 0 0 0 1px var(--acc-accent, #7c3aed) inset;
  color: var(--acc-accent, #7c3aed) !important;
}
</style>
