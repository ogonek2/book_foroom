<template>
  <div class="acc-glass rounded-2xl p-5 border border-white/10 max-w-2xl">
    <div class="text-lg font-extrabold tracking-tight">Налаштування · Приватність</div>
    <div class="mt-4 space-y-3">
      <label class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2">
        <span class="text-sm text-white/85">Публічний профіль</span>
        <input v-model="form.public_profile" type="checkbox">
      </label>
      <label class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2">
        <span class="text-sm text-white/85">Показувати статистику читання</span>
        <input v-model="form.show_reading_stats" type="checkbox">
      </label>
      <label class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2">
        <span class="text-sm text-white/85">Показувати оцінки</span>
        <input v-model="form.show_ratings" type="checkbox">
      </label>
      <button type="button" class="acc-btn-primary w-full justify-center" :disabled="saving" @click="save">
        {{ saving ? 'Збереження...' : 'Зберегти налаштування' }}
      </button>
      <div v-if="message" class="text-xs text-emerald-300">{{ message }}</div>
      <div v-if="error" class="text-xs text-red-300">{{ error }}</div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  props: {
    profile: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      form: {
        public_profile: true,
        show_reading_stats: true,
        show_ratings: true,
      },
      saving: false,
      message: '',
      error: '',
    };
  },
  watch: {
    profile: {
      immediate: true,
      handler(value) {
        const settings = value?.settings || {};
        this.form.public_profile = settings.public_profile !== false;
        this.form.show_reading_stats = settings.show_reading_stats !== false;
        this.form.show_ratings = settings.show_ratings !== false;
      },
    },
  },
  methods: {
    async save() {
      this.saving = true;
      this.message = '';
      this.error = '';
      try {
        const { data } = await axios.put('/api/account/profile/privacy', this.form);
        this.message = data?.message || 'Збережено.';
      } catch (e) {
        this.error = e?.response?.data?.message || 'Не вдалося зберегти.';
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>
