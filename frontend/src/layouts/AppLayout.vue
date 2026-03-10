<template>
  <div class="flex h-screen">
    <SideNav />
    <div class="flex-1 bg-slate-100 p-6 overflow-y-auto">
      <RouterView />
    </div>
  </div>
</template>

<script setup>
import SideNav from '@/components/SideNav.vue'
</script>