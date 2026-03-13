<template>
  <div class="w-84 min-h-screen bg-white border-r border-slate-200 flex flex-col">

    <!-- Header -->
    <div class="px-6 py-6 border-b border-slate-200">
      <div class="text-lg font-bold text-slate-800 tracking-wide">PROCUREMENT</div>
      <div class="text-sm text-slate-500">Management System</div>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 px-3 py-4 flex flex-col gap-1.5">
      <RouterLink
        v-for="link in navLinks"
        :key="link.to"
        :to="link.to"
        class="block px-4 py-3 text-sm text-slate-700 border border-slate-200 rounded hover:bg-slate-50 transition"
        active-class="bg-slate-100 font-semibold text-slate-900"
      >
        {{ link.label }}
      </RouterLink>
    </nav>

    <!-- Footer -->
    <div class="px-3 py-4 border-t border-slate-200 flex flex-col gap-2">
      <div class="border border-slate-200 rounded px-4 py-3">
        <div class="text-xs font-semibold text-slate-400 tracking-widest uppercase mb-1">Logged in as</div>
        <div class="text-sm font-medium text-slate-800">{{ auth.user?.name || 'User' }}</div>
      </div>
      <button
        @click="handleLogout"
        class="w-full py-2.5 text-sm font-bold tracking-widest uppercase border border-slate-800 text-slate-800 hover:bg-slate-800 hover:text-white transition rounded"
      >
        Logout
      </button>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const router = useRouter()
const auth = useAuthStore()

const navLinks = computed(() => {
  const links = []

  links.push({ to: '/dashboard', label: 'Dashboard' })

  if (auth.hasPermission('view-own-purchase-request') || auth.hasPermission('view-all-purchase-requests')) {
    links.push({ to: '/purchase-requests', label: 'Purchase Requests' })
  }

  if (auth.hasPermission('approve-purchase-request') || auth.hasPermission('reject-purchase-request')) {
    links.push({ to: '/approvals', label: 'Pending Approvals' })
  }

  if (auth.hasPermission('view-purchase-orders')) links.push({ to: '/purchase-orders', label: 'Purchase Orders' })
  if (auth.hasPermission('view-invoices')) links.push({ to: '/invoices', label: 'Invoices' })
  if (auth.hasPermission('view-suppliers')) links.push({ to: '/suppliers', label: 'Suppliers' })

  if (auth.hasPermission('manage-users') || auth.hasPermission('manage-roles')) {
    links.push({ to: '/admin/users', label: 'Users & Roles' })
  }

  return links
})

async function handleLogout() {
  try {
    await api.post('/logout')
  } catch (e) {
    // continue logout even if API fails
  } finally {
    auth.clearAuth()
    router.push('/login')
  }
}
</script>