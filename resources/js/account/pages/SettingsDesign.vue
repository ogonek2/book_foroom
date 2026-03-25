<template>
  <div>
    <div>
      <div class="text-lg font-extrabold tracking-tight">Налаштування · Оформлення</div>
      <div class="mt-1 text-sm text-white/65">
        Налаштування шапки профілю, кольорів та стилів відображення.
      </div>

      <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="acc-glass rounded-2xl p-5 border border-white/10">
          <div class="text-sm font-extrabold">Акцент та рамки</div>
          <div class="mt-4 grid grid-cols-2 gap-3">
          <button
            v-for="c in colors"
            :key="c"
            type="button"
            class="acc-glass rounded-2xl p-4 border border-white/10 text-left hover:bg-white/10 transition"
            :class="{ 'ring-2 ring-white/50': form.accent_color === c }"
            @click="form.accent_color = c"
            :disabled="!isOwner || saving"
          >
            <div class="flex items-center justify-between">
              <div class="text-xs text-white/60 font-semibold">Accent</div>
              <span class="h-4 w-4 rounded-full" :style="{ backgroundColor: c }" />
            </div>
            <div class="mt-2 font-extrabold">{{ c }}</div>
          </button>
        </div>

          <div class="mt-4 space-y-3">
          <div>
            <div class="text-xs text-white/55 font-semibold mb-1">Secondary color</div>
            <input
              v-model="form.secondary_color"
              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-2 text-sm text-white/90 outline-none focus:ring-2 focus:ring-purple-500/40"
              :disabled="!isOwner || saving"
              placeholder="#2563eb"
            />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <div class="text-xs text-white/55 font-semibold mb-1">Frame style</div>
              <select v-model="form.frame_style" class="w-full rounded-xl bg-white/5 border border-white/10 px-3 py-2 text-sm text-white/90" :disabled="!isOwner || saving">
                <option value="default">default</option>
                <option value="neon">neon</option>
                <option value="soft">soft</option>
              </select>
            </div>
            <div>
              <div class="text-xs text-white/55 font-semibold mb-1">Card style</div>
              <select v-model="form.card_style" class="w-full rounded-xl bg-white/5 border border-white/10 px-3 py-2 text-sm text-white/90" :disabled="!isOwner || saving">
                <option value="glass">glass</option>
                <option value="solid">solid</option>
              </select>
            </div>
          </div>
          </div>
        </div>

        <div class="acc-glass rounded-2xl p-5 border border-white/10">
          <div class="text-sm font-extrabold">Шапка профілю</div>
          <div class="mt-4 space-y-3">
          <div class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="text-xs text-white/60 font-semibold mb-2">Превʼю</div>
            <div class="h-28 rounded-2xl overflow-hidden border border-white/10 bg-white/5">
              <img v-if="headerPreview" :src="headerPreview" class="h-full w-full object-cover" alt="header" />
              <div v-else class="h-full w-full flex items-center justify-center text-xs text-white/50">
                Немає зображення шапки
              </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
              <label class="acc-btn" :class="{ 'opacity-60 pointer-events-none': !isOwner || headerUploading }">
                {{ headerUploading ? 'Завантаження...' : 'Змінити файлом' }}
                <input type="file" class="hidden" accept="image/*" :disabled="!isOwner || headerUploading" @change="onHeaderSelected" />
              </label>
              <div class="text-xs text-white/55">Рекомендовано 1500×500</div>
            </div>
          </div>

          <div class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="text-xs text-white/60 font-semibold">Заголовок</div>
            <input
              v-model="form.header_title"
              class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 px-3 py-2 text-sm text-white/90"
              :disabled="!isOwner || saving"
              placeholder="Наприклад: Мій профіль"
            />
          </div>
          <div class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="text-xs text-white/60 font-semibold">Підзаголовок</div>
            <input
              v-model="form.header_subtitle"
              class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 px-3 py-2 text-sm text-white/90"
              :disabled="!isOwner || saving"
              placeholder="Короткий опис шапки"
            />
          </div>
          <div class="acc-glass rounded-2xl p-4 border border-white/10">
            <div class="text-xs text-white/60 font-semibold">Фон шапки (URL)</div>
            <input
              v-model="form.header_image"
              class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 px-3 py-2 text-sm text-white/90"
              :disabled="!isOwner || saving"
              placeholder="https://..."
            />
          </div>
          <div v-if="message" class="text-xs text-emerald-300">{{ message }}</div>
          <div v-if="error" class="text-xs text-red-300">{{ error }}</div>
          <button type="button" class="acc-btn-primary w-full justify-center" :disabled="!isOwner || saving" @click="saveDesign">
            {{ saving ? 'Збереження...' : 'Зберегти оформлення' }}
          </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="cropModalOpen" class="fixed inset-0 z-[120]">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeCropModal" />
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-3xl rounded-2xl border border-white/10 bg-[#0a0b14] shadow-2xl">
          <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
            <div class="text-lg font-extrabold">Редагувати шапку</div>
            <button class="acc-btn !px-3 !py-1.5" type="button" @click="closeCropModal">✕</button>
          </div>

          <div class="p-5 space-y-4">
            <div
              ref="cropViewport"
              class="relative w-full aspect-[3/1] rounded-xl overflow-hidden border border-white/10 bg-black/40 cursor-grab active:cursor-grabbing"
              @mousedown="startDrag"
              @mousemove="onDrag"
              @mouseup="endDrag"
              @mouseleave="endDrag"
            >
              <img
                v-if="cropSource"
                ref="cropImage"
                :src="cropSource"
                alt="crop source"
                class="absolute select-none pointer-events-none max-w-none"
                :style="cropImageStyle"
                draggable="false"
              />
            </div>

            <div>
              <div class="text-xs text-white/60 mb-2">Масштаб</div>
              <input
                v-model.number="cropScale"
                type="range"
                :min="cropMinScale"
                :max="Math.max(cropMinScale * 3, cropMinScale + 0.1)"
                step="0.01"
                class="w-full"
                @input="onScaleChange"
              />
            </div>

            <div class="flex justify-end gap-2">
              <button class="acc-btn" type="button" @click="closeCropModal">Скасувати</button>
              <button class="acc-btn-primary" type="button" :disabled="headerUploading" @click="applyCropAndUpload">
                {{ headerUploading ? 'Завантаження...' : 'Зберегти' }}
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
  name: 'SettingsDesign',
  props: {
    profile: { type: Object, default: null },
    isOwner: { type: Boolean, default: false },
  },
  data() {
    return {
      colors: ['#7c3aed', '#2563eb', '#ec4899', '#22c55e', '#f59e0b', '#ef4444'],
      form: {
        header_title: '',
        header_subtitle: '',
        header_image: '',
        accent_color: '#7c3aed',
        secondary_color: '#2563eb',
        frame_style: 'default',
        card_style: 'glass',
      },
      saving: false,
      headerUploading: false,
      cropModalOpen: false,
      cropSource: '',
      cropImageNatural: { width: 0, height: 0 },
      cropViewportSize: { width: 0, height: 0 },
      cropScale: 1,
      cropMinScale: 1,
      cropX: 0,
      cropY: 0,
      dragState: {
        active: false,
        startX: 0,
        startY: 0,
        originX: 0,
        originY: 0,
      },
      message: '',
      error: '',
    };
  },
  watch: {
    profile: {
      immediate: true,
      handler(value) {
        if (!value) return;
        this.form.header_title = value?.header?.title || '';
        this.form.header_subtitle = value?.header?.subtitle || '';
        this.form.header_image = value?.header?.image || '';
        this.form.accent_color = value?.theme?.accent || '#7c3aed';
        this.form.secondary_color = value?.theme?.secondary || '#2563eb';
        this.form.frame_style = value?.theme?.frame || 'default';
        this.form.card_style = value?.theme?.card || 'glass';
      },
    },
  },
  computed: {
    headerPreview() {
      return this.form.header_image || '';
    },
    cropImageStyle() {
      return {
        width: `${this.cropImageNatural.width}px`,
        height: `${this.cropImageNatural.height}px`,
        transform: `translate(${this.cropX}px, ${this.cropY}px) scale(${this.cropScale})`,
        transformOrigin: 'top left',
      };
    },
  },
  methods: {
    async saveDesign() {
      if (!this.isOwner) return;

      this.saving = true;
      this.error = '';
      this.message = '';

      try {
        const response = await axios.put('/api/account/profile/design', this.form);
        this.message = response.data.message || 'Оформлення оновлено.';
        this.$emit('profile-updated', response.data.profile);
      } catch (e) {
        this.error = e?.response?.data?.message || 'Помилка збереження оформлення.';
      } finally {
        this.saving = false;
      }
    },
    async onHeaderSelected(event) {
      const file = event?.target?.files?.[0];
      if (!file || !this.isOwner) return;
      await this.openCropModal(file);
      event.target.value = '';
    },
    async openCropModal(file) {
      const objectUrl = URL.createObjectURL(file);
      this.cropSource = objectUrl;
      this.cropModalOpen = true;
      this.error = '';
      this.message = '';

      try {
        const image = await this.loadImage(objectUrl);
        this.cropImageNatural = { width: image.naturalWidth, height: image.naturalHeight };
        await this.$nextTick();
        this.initCropState();
      } catch (e) {
        this.error = 'Не вдалося відкрити інструмент обрізки.';
        this.closeCropModal();
      }
    },
    closeCropModal() {
      if (this.cropSource) {
        URL.revokeObjectURL(this.cropSource);
      }
      this.cropModalOpen = false;
      this.cropSource = '';
      this.dragState.active = false;
    },
    loadImage(src) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
      });
    },
    initCropState() {
      const viewport = this.$refs.cropViewport;
      if (!viewport) return;

      const vw = viewport.clientWidth;
      const vh = viewport.clientHeight;
      this.cropViewportSize = { width: vw, height: vh };

      const iw = this.cropImageNatural.width;
      const ih = this.cropImageNatural.height;
      if (!iw || !ih) return;

      const minScale = Math.max(vw / iw, vh / ih);
      this.cropMinScale = minScale;
      this.cropScale = minScale;

      const scaledW = iw * minScale;
      const scaledH = ih * minScale;
      this.cropX = (vw - scaledW) / 2;
      this.cropY = (vh - scaledH) / 2;
      this.clampCropPosition();
    },
    clampCropPosition() {
      const vw = this.cropViewportSize.width;
      const vh = this.cropViewportSize.height;
      const scaledW = this.cropImageNatural.width * this.cropScale;
      const scaledH = this.cropImageNatural.height * this.cropScale;

      const minX = Math.min(0, vw - scaledW);
      const minY = Math.min(0, vh - scaledH);
      const maxX = 0;
      const maxY = 0;

      this.cropX = Math.min(maxX, Math.max(minX, this.cropX));
      this.cropY = Math.min(maxY, Math.max(minY, this.cropY));
    },
    onScaleChange() {
      this.clampCropPosition();
    },
    startDrag(event) {
      if (!this.cropModalOpen) return;
      this.dragState.active = true;
      this.dragState.startX = event.clientX;
      this.dragState.startY = event.clientY;
      this.dragState.originX = this.cropX;
      this.dragState.originY = this.cropY;
    },
    onDrag(event) {
      if (!this.dragState.active) return;
      const dx = event.clientX - this.dragState.startX;
      const dy = event.clientY - this.dragState.startY;
      this.cropX = this.dragState.originX + dx;
      this.cropY = this.dragState.originY + dy;
      this.clampCropPosition();
    },
    endDrag() {
      this.dragState.active = false;
    },
    async applyCropAndUpload() {
      if (!this.cropSource || !this.isOwner) return;

      this.headerUploading = true;
      this.error = '';
      this.message = '';

      try {
        const blob = await this.renderCroppedBlob();
        const data = new FormData();
        data.append('header_image', blob, 'profile-header.jpg');

        const response = await axios.post('/api/account/profile/header-image', data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        const imageUrl = response?.data?.profile?.header?.image;
        if (imageUrl) {
          this.form.header_image = imageUrl;
        }

        this.message = response.data.message || 'Шапку оновлено.';
        this.$emit('profile-updated', response.data.profile);
        this.closeCropModal();
      } catch (e) {
        this.error = e?.response?.data?.message || 'Помилка обрізки або завантаження шапки.';
      } finally {
        this.headerUploading = false;
      }
    },
    renderCroppedBlob() {
      return new Promise((resolve, reject) => {
        const vw = this.cropViewportSize.width;
        const vh = this.cropViewportSize.height;
        if (!vw || !vh) {
          reject(new Error('Viewport is not ready'));
          return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = 1500;
        canvas.height = 500;
        const ctx = canvas.getContext('2d');

        const img = this.$refs.cropImage;
        if (!ctx || !img) {
          reject(new Error('Canvas or image unavailable'));
          return;
        }

        const sx = -this.cropX / this.cropScale;
        const sy = -this.cropY / this.cropScale;
        const sw = vw / this.cropScale;
        const sh = vh / this.cropScale;

        ctx.drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
        canvas.toBlob((blob) => {
          if (!blob) {
            reject(new Error('Failed to create image blob'));
            return;
          }
          resolve(blob);
        }, 'image/jpeg', 0.92);
      });
    },
  },
};
</script>

