<template>
  <div class="space-y-4">
    <section class="acc-glass rounded-2xl p-5 border border-white/10 w-full">
      <div class="text-lg font-extrabold tracking-tight">Налаштування · Акаунт</div>
      <div class="mt-3 text-sm text-white/70">
        Експорт ваших даних у JSON.
      </div>
      <button type="button" class="acc-btn-primary mt-4" :disabled="loading" @click="exportData">
        {{ loading ? 'Підготовка...' : 'Експортувати дані' }}
      </button>
      <pre v-if="exportPayload" class="mt-3 max-h-64 overflow-auto rounded-xl border border-white/10 bg-black/30 p-3 text-xs text-white/80">{{ exportPayload }}</pre>
    </section>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  data() {
    return {
      loading: false,
      exportPayload: '',
    };
  },
  methods: {
    async exportData() {
      this.loading = true;
      try {
        const { data } = await axios.get('/api/account/profile/export');
        this.exportPayload = JSON.stringify(data?.export || {}, null, 2);
      } catch (e) {
        this.exportPayload = JSON.stringify({ error: e?.response?.data?.message || 'Помилка експорту' }, null, 2);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
