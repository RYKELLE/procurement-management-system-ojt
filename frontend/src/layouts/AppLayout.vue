<template>
  <div class="app-layout">
    <SideNav />
    <div class="main-content">
      <RouterView />
    </div>
  </div>
</template>

<script setup>
import SideNav from '@/components/SideNav.vue'
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  background: #f1f5f9;
  padding: 32px;
  overflow-y: auto;
}
</style>