<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Налаштування · Профіль</div>
    <div class="mt-1 text-sm text-white/65">
      Редагування ніку, email, опису та аватарки. Збереження асинхронне.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4">
      <div class="acc-glass rounded-2xl p-5 border border-white/10">
        <div class="text-sm font-extrabold">Основні дані</div>
        <form class="mt-4 space-y-3" @submit.prevent="saveProfile">
          <div>
            <div class="text-xs text-white/55 font-semibold mb-1">Імʼя</div>
            <input
              v-model="form.name"
              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white/90 outline-none focus:ring-2 focus:ring-purple-500/40"
              placeholder="Ваше ім'я"
              :disabled="!isOwner || saving"
            />
          </div>
          <div>
            <div class="text-xs text-white/55 font-semibold mb-1">Нікнейм</div>
            <input
              v-model="form.username"
              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white/90 outline-none focus:ring-2 focus:ring-purple-500/40"
              placeholder="Введіть нік"
              :disabled="!isOwner || saving"
            />
          </div>
          <div>
            <div class="text-xs text-white/55 font-semibold mb-1">Email</div>
            <input
              v-model="form.email"
              type="email"
              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white/90 outline-none focus:ring-2 focus:ring-purple-500/40"
              placeholder="mail@example.com"
              :disabled="!isOwner || saving"
            />
          </div>
          <div>
            <div class="text-xs text-white/55 font-semibold mb-1">Короткий опис</div>
            <textarea
              v-model="form.bio"
              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white/90 outline-none focus:ring-2 focus:ring-purple-500/40"
              rows="4"
              placeholder="Про себе..."
              :disabled="!isOwner || saving"
            />
          </div>
          <div v-if="message" class="text-xs text-emerald-300">{{ message }}</div>
          <div v-if="error" class="text-xs text-red-300">{{ error }}</div>
          <button type="submit" class="acc-btn-primary w-full justify-center" :disabled="!isOwner || saving">
            {{ saving ? 'Збереження...' : 'Зберегти' }}
          </button>
        </form>
      </div>

      <div class="acc-glass rounded-2xl p-5 border border-white/10">
        <div class="text-sm font-extrabold">Аватар</div>
        <div class="mt-4 flex items-center gap-4">
          <img :src="avatarPreview" class="h-16 w-16 rounded-2xl bg-white/10 border border-white/10 object-cover" alt="avatar" />
          <div class="flex-1">
            <div class="text-xs text-white/60">PNG/JPG/GIF/WEBP до 2MB.</div>
            <div class="mt-2 flex items-center gap-2">
              <label class="acc-btn" :class="{ 'opacity-60 pointer-events-none': !isOwner || avatarSaving }">
                {{ avatarSaving ? 'Завантаження...' : 'Змінити' }}
                <input type="file" class="hidden" accept="image/*" :disabled="!isOwner || avatarSaving" @change="onAvatarSelected" />
              </label>
              <button class="acc-btn" type="button" :disabled="!isOwner || avatarSaving" @click="removeAvatar">
                Видалити
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  name: 'SettingsProfile',
  props: {
    profile: { type: Object, default: null },
    isOwner: { type: Boolean, default: false },
  },
  data() {
    return {
      form: {
        name: '',
        username: '',
        email: '',
        bio: '',
      },
      saving: false,
      avatarSaving: false,
      message: '',
      error: '',
    };
  },
  computed: {
    avatarPreview() {
      if (this.profile && this.profile.avatar) return this.profile.avatar;
      const nickname = this.form.username || 'User';
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(nickname)}&background=7c3aed&color=fff&size=200`;
    },
  },
  watch: {
    profile: {
      immediate: true,
      handler(value) {
        if (!value) return;
        this.form.name = value.name || '';
        this.form.username = value.username || '';
        this.form.email = value.email || '';
        this.form.bio = value.bio || '';
      },
    },
  },
  methods: {
    async saveProfile() {
      if (!this.isOwner) return;
      this.saving = true;
      this.error = '';
      this.message = '';

      try {
        const response = await axios.put('/api/account/profile', {
          name: this.form.name,
          username: this.form.username,
          email: this.form.email,
          bio: this.form.bio,
        });

        this.message = response.data.message || 'Збережено.';
        this.$emit('profile-updated', response.data.profile);
      } catch (e) {
        this.error = e?.response?.data?.message || 'Помилка збереження профілю.';
      } finally {
        this.saving = false;
      }
    },
    async onAvatarSelected(event) {
      const file = event?.target?.files?.[0];
      if (!file || !this.isOwner) return;

      this.avatarSaving = true;
      this.error = '';
      this.message = '';

      try {
        const data = new FormData();
        data.append('avatar', file);

        const response = await axios.post('/api/account/profile/avatar', data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        this.message = response.data.message || 'Аватар оновлено.';
        this.$emit('profile-updated', { avatar: response.data.avatar });
      } catch (e) {
        this.error = e?.response?.data?.message || 'Помилка завантаження аватара.';
      } finally {
        this.avatarSaving = false;
        event.target.value = '';
      }
    },
    async removeAvatar() {
      if (!this.isOwner) return;

      this.avatarSaving = true;
      this.error = '';
      this.message = '';

      try {
        const response = await axios.delete('/api/account/profile/avatar');
        this.message = response.data.message || 'Аватар видалено.';
        this.$emit('profile-updated', { avatar: response.data.avatar });
      } catch (e) {
        this.error = e?.response?.data?.message || 'Помилка видалення аватара.';
      } finally {
        this.avatarSaving = false;
      }
    },
  },
};
</script>

