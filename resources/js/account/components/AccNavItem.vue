<template>
  <router-link
    v-if="!disabled"
    class="acc-nav-item"
    :class="{ 'acc-nav-item--active': isActive }"
    :to="to"
  >
    <span class="h-9 w-9 rounded-xl flex items-center justify-center bg-white/5 border border-white/10">
      <svg v-if="icon === 'user'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 100-8 4 4 0 000 8z" />
      </svg>
      <svg v-else-if="icon === 'book'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7 2V5l7-2 7 2v16l-7-2z" />
      </svg>
      <svg v-else-if="icon === 'star'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l2.1 6.463h6.797c.969 0 1.371 1.24.588 1.81l-5.5 3.994 2.1 6.463c.3.921-.755 1.688-1.54 1.118L12 19.347l-5.5 3.994c-.784.57-1.838-.197-1.539-1.118l2.1-6.463-5.5-3.994c-.783-.57-.38-1.81.588-1.81h6.797l2.103-6.463z" />
      </svg>
      <svg v-else-if="icon === 'chat'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h4" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <svg v-else-if="icon === 'quote'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17h4V7H5v6a4 4 0 002 4zM17 17h4V7h-6v6a4 4 0 002 4z" />
      </svg>
      <svg v-else-if="icon === 'grid'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h7v7H4V6zM13 6h7v7h-7V6zM4 13h7v7H4v-7zM13 13h7v7h-7v-7z" />
      </svg>
      <svg v-else-if="icon === 'settings'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <svg v-else-if="icon === 'palette'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c4.97 0 9 3.582 9 8 0 2.485-2.239 4-5 4h-1a2 2 0 00-2 2 3 3 0 01-6 0c0-1.657 1.343-3 3-3h1a1 1 0 100-2H9C6.239 16 4 14.485 4 12c0-4.418 4.03-9 8-9z" />
      </svg>
      <svg v-else-if="icon === 'lock'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 00-8 0v4" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 11h12v10H6V11z" />
      </svg>
      <span v-else class="text-xs font-bold">•</span>
    </span>

    <span class="flex-1 min-w-0 truncate">
      <slot />
    </span>
  </router-link>

  <div v-else class="acc-nav-item opacity-60 cursor-not-allowed">
    <span class="h-9 w-9 rounded-xl flex items-center justify-center bg-white/5 border border-white/10">
      <span class="text-xs font-bold">×</span>
    </span>
    <span class="flex-1 min-w-0 truncate">
      <slot />
    </span>
  </div>
</template>

<script lang="js">
export default {
  name: 'AccNavItem',
  props: {
    to: { type: [String, Object], required: true },
    icon: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
  },
  computed: {
    isActive() {
      try {
        return this.$route && this.$route.path === (typeof this.to === 'string' ? this.to : this.to.path);
      } catch {
        return false;
      }
    },
  },
};
</script>

