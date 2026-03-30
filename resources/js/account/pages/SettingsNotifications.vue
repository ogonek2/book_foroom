<template>
  <div class="acc-glass rounded-2xl p-5 border border-white/10 max-w-2xl">
    <div class="text-lg font-extrabold tracking-tight">Налаштування · Сповіщення</div>
    <div class="mt-4 space-y-3">
      <label class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2">
        <span class="text-sm text-white/85">Email сповіщення</span>
        <input v-model="form.email_notifications" type="checkbox">
      </label>
      <label class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2">
        <span class="text-sm text-white/85">Нові книги</span>
        <input v-model="form.new_books_notifications" type="checkbox">
      </label>
      <label class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2">
        <span class="text-sm text-white/85">Коментарі</span>
        <input v-model="form.comments_notifications" type="checkbox">
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
        email_notifications: true,
        new_books_notifications: true,
        comments_notifications: true,
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
        this.form.email_notifications = settings.email_notifications !== false;
        this.form.new_books_notifications = settings.new_books_notifications !== false;
        this.form.comments_notifications = settings.comments_notifications !== false;
      },
    },
  },
  methods: {
    async save() {
      this.saving = true;
      this.message = '';
      this.error = '';
      try {
        const { data } = await axios.put('/api/account/profile/notifications', this.form);
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
