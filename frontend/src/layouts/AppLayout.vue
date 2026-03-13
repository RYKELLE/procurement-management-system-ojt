<template>
  <div class="flex h-screen">
    <SideNav />
    <div class="flex-1 bg-slate-100 p-6 overflow-y-auto">
      <div
        v-if="flash"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[min(720px,calc(100vw-2rem))] px-5 py-3 border text-sm flex items-start justify-between gap-4 shadow-lg"
        :class="flash.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-slate-50 border-slate-200 text-slate-700'"
      >
        <div class="font-semibold tracking-wide">{{ flash.message }}</div>
        <button
          class="text-xs font-bold uppercase tracking-widest opacity-70 hover:opacity-100"
          @click="flash = null"
        >
          Dismiss
        </button>
      </div>
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
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import SideNav from '@/components/Sidenav.vue'

const route = useRoute()
const flash = ref(null)
let flashTimeout = null

function setFlash(payload) {
  if (!payload || typeof payload.message !== 'string') return

  flash.value = payload
  if (flashTimeout) clearTimeout(flashTimeout)
  flashTimeout = setTimeout(() => { flash.value = null }, 3000)
}

function onFlashEvent(event) {
  setFlash(event?.detail)
}

function loadFlash() {
  try {
    const raw = sessionStorage.getItem('flash')
    if (!raw) return

    const parsed = JSON.parse(raw)
    setFlash(parsed)
  } catch {
    // ignore malformed flash payload
  } finally {
    sessionStorage.removeItem('flash')
  }
}

watch(() => route.fullPath, loadFlash, { immediate: true })

onMounted(() => window.addEventListener('app:flash', onFlashEvent))
onBeforeUnmount(() => window.removeEventListener('app:flash', onFlashEvent))
</script>
