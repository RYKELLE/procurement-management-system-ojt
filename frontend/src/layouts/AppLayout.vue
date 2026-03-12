<template>
  <div class="flex h-screen">
    <SideNav />
    <div class="flex-1 bg-slate-100 p-6 overflow-y-auto">
      <RouterView v-slot="{ Component, route }">
        <template v-if="route.meta.keepAlive">
          <KeepAlive>
            <component :is="Component" />
          </KeepAlive>
        </template>
        <template v-else>
          <component :is="Component" />
        </template>
      </RouterView>
    </div>
  </div>
</template>

<script setup>
import SideNav from '@/components/SideNav.vue'
</script>
