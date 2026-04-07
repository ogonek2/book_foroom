<template>
  <div class="space-y-4">
    <div class="acc-glass rounded-2xl p-5 border border-white/10">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-lg font-extrabold tracking-tight">Нагороди</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-white/65">
            Всі нагороди користувача у розширеному форматі.
          </div>
        </div>
        <div class="text-xs text-slate-600 dark:text-white/60">
          Всього: <span class="font-bold text-slate-900 dark:text-white">{{ awards.length }}</span>
        </div>
      </div>
    </div>

    <div v-if="awards.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
      <button
        v-for="award in awards"
        :key="`award-card-${award.id}`"
        type="button"
        class="acc-glass rounded-2xl p-4 border border-white/10 text-left transition hover:bg-white/10"
        @click="openAwardModal(award)"
      >
        <div class="flex items-center gap-3">
          <img
            v-if="award.image"
            :src="award.image"
            :alt="award.name"
            class="h-16 w-16 rounded-2xl object-cover border border-white/10"
          />
          <div
            v-else
            class="h-16 w-16 rounded-2xl flex items-center justify-center text-lg font-extrabold text-white"
            :style="{ backgroundColor: award.color || '#8b5cf6' }"
          >
            {{ (award.name || '?').slice(0, 1).toUpperCase() }}
          </div>
          <div class="min-w-0">
            <div class="text-base font-extrabold truncate">{{ award.name }}</div>
            <div v-if="award.points" class="text-xs text-slate-600 dark:text-white/60">+{{ award.points }} балів</div>
          </div>
        </div>

        <div v-if="award.description" class="mt-3 text-sm text-slate-700 dark:text-white/75 line-clamp-2">
          {{ award.description }}
        </div>
        <div class="mt-3 text-xs text-slate-500 dark:text-white/55 space-y-1">
          <div v-if="award.awarded_at_human">Отримано: {{ award.awarded_at_human }}</div>
          <div v-else-if="award.awarded_at">Дата: {{ award.awarded_at }}</div>
        </div>
      </button>
    </div>

    <div v-else class="acc-glass rounded-2xl p-5 border border-white/10">
      <div class="text-sm font-bold">Нагород поки немає</div>
      <div class="mt-1 text-xs text-slate-600 dark:text-white/60">
        Після активності у профілі тут з'являться ваші відзнаки.
      </div>
    </div>

    <div v-if="awardModal" class="fixed inset-0 z-[145]">
      <div class="acc-modal-overlay" @click="awardModal = null" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="acc-modal max-w-md">
          <div class="flex items-center gap-3">
            <img
              v-if="awardModal.image"
              :src="awardModal.image"
              :alt="awardModal.name"
              class="h-14 w-14 rounded-2xl object-cover border border-white/10"
            />
            <div
              v-else
              class="h-14 w-14 rounded-2xl flex items-center justify-center text-base font-extrabold text-white"
              :style="{ backgroundColor: awardModal.color || '#8b5cf6' }"
            >
              {{ (awardModal.name || '?').slice(0, 1).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <div class="text-base font-extrabold truncate">{{ awardModal.name }}</div>
              <div v-if="awardModal.points" class="text-xs text-slate-600 dark:text-white/60">+{{ awardModal.points }} балів</div>
            </div>
          </div>
          <div v-if="awardModal.description" class="mt-3 text-sm text-slate-700 dark:text-white/80">
            {{ awardModal.description }}
          </div>
          <div class="mt-3 text-xs text-slate-500 dark:text-white/60 space-y-1">
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
export default {
  name: 'ProfileAwards',
  props: {
    dashboard: { type: Object, default: null },
  },
  data() {
    return {
      awardModal: null,
    };
  },
  computed: {
    awards() {
      return this.dashboard?.awards || [];
    },
  },
  methods: {
    openAwardModal(award) {
      this.awardModal = award || null;
    },
  },
};
</script>

