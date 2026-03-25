<template>
  <div>
    <div class="text-lg font-extrabold tracking-tight">Налаштування · Безпека</div>
    <div class="mt-1 text-sm text-white/65">
      Тут буде зміна пароля/сесії/2FA. Поки — макет.
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
      <div class="acc-glass rounded-2xl p-5 border border-white/10">
        <div class="text-sm font-extrabold">Зміна пароля</div>
        <div class="mt-4 space-y-3">
          <div class="h-10 rounded-xl bg-white/5 border border-white/10" />
          <div class="h-10 rounded-xl bg-white/5 border border-white/10" />
          <div class="h-10 rounded-xl bg-white/5 border border-white/10" />
          <button type="button" class="acc-btn-primary w-full justify-center" disabled>Оновити (пізніше)</button>
        </div>
      </div>

      <div class="acc-glass rounded-2xl p-5 border border-white/10">
        <div class="text-sm font-extrabold">Сесії</div>
        <div class="mt-4 space-y-2">
          <div class="acc-glass rounded-xl p-3 border border-white/10">
            <div class="font-bold text-sm">Chrome · Windows</div>
            <div class="text-xs text-white/60 mt-1">Остання активність: зараз</div>
          </div>
          <div class="acc-glass rounded-xl p-3 border border-white/10 opacity-80">
            <div class="font-bold text-sm">Mobile · Android</div>
            <div class="text-xs text-white/60 mt-1">Остання активність: 2 дні тому</div>
          </div>
          <button type="button" class="acc-btn w-full justify-center" disabled>Вийти з інших</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
export default {
  name: 'SettingsSecurity',
};
</script>

