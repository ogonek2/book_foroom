<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Налаштування · Безпека</div>
    <div class="mt-1 text-sm text-white/65">Зміна пароля для акаунта.</div>

    <div class="mt-6">
      <div class="acc-glass rounded-2xl p-5 border border-white/10 max-w-2xl">
        <div class="text-sm font-extrabold">Зміна пароля</div>
        <div class="mt-4 space-y-3">
          <input v-model="form.current_password" type="password" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none" placeholder="Поточний пароль">
          <input v-model="form.password" type="password" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none" placeholder="Новий пароль">
          <input v-model="form.password_confirmation" type="password" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none" placeholder="Підтвердження нового пароля">
          <button type="button" class="acc-btn-primary w-full justify-center" :disabled="saving" @click="save">
            {{ saving ? 'Оновлення...' : 'Оновити пароль' }}
          </button>
          <div v-if="message" class="text-xs text-emerald-300">{{ message }}</div>
          <div v-if="error" class="text-xs text-red-300">{{ error }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  name: 'SettingsSecurity',
  data() {
    return {
      form: {
        current_password: '',
        password: '',
        password_confirmation: '',
      },
      saving: false,
      message: '',
      error: '',
    };
  },
  methods: {
    async save() {
      this.saving = true;
      this.message = '';
      this.error = '';
      try {
        const { data } = await axios.put('/api/account/profile/password', this.form);
        this.message = data?.message || 'Пароль оновлено.';
        this.form.current_password = '';
        this.form.password = '';
        this.form.password_confirmation = '';
      } catch (e) {
        this.error = e?.response?.data?.message || 'Не вдалося оновити пароль.';
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>

