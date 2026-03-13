<template>
  <div class="w-84 min-h-screen bg-white border-r border-slate-200 flex flex-col">

    <!-- Header -->
    <div class="px-6 py-6 border-b border-slate-200">
      <div class="text-lg font-bold text-slate-800 tracking-wide">PROCUREMENT</div>
      <div class="text-sm text-slate-500">Management System</div>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 px-3 py-4 flex flex-col gap-1.5">
      <template v-for="item in navItems" :key="item.key">
        <div
          v-if="item.type === 'section'"
          class="px-3 pt-4 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-400"
        >
          {{ item.label }}
        </div>

        <RouterLink
          v-else-if="item.enabled"
          :to="item.to"
          class="block px-4 py-3 text-sm text-slate-700 border border-slate-200 rounded hover:bg-slate-50 transition"
          active-class="bg-slate-100 font-semibold text-slate-900"
        >
          {{ item.label }}
        </RouterLink>

        <button
          v-else
          type="button"
          class="w-full text-left px-4 py-3 text-sm border border-slate-200 rounded text-slate-400 bg-slate-50 cursor-not-allowed"
          :title="item.title"
          aria-disabled="true"
          @click="showAccessDenied(item)"
        >
          {{ item.label }}
        </button>
      </template>
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

function hasAnyPermission(perms) {
  return Array.isArray(perms) && perms.some(p => auth.hasPermission(p))
}

function showAccessDenied(item) {
  const payload = { type: 'error', message: 'Access Denied' }
  sessionStorage.setItem('flash', JSON.stringify(payload))
  window.dispatchEvent(new CustomEvent('app:flash', { detail: payload }))
}

const navItems = computed(() => {
  const items = []

  items.push({ type: 'section', key: 'sec-general', label: 'General' })
  items.push({ type: 'link', key: 'dashboard', to: '/dashboard', label: 'Dashboard', enabled: true })

  items.push({ type: 'section', key: 'sec-proc', label: 'Procurement' })
  items.push({
    type: 'link',
    key: 'purchase-requests',
    to: '/purchase-requests',
    label: 'Purchase Requests',
    enabled: hasAnyPermission(['view-own-purchase-request', 'view-all-purchase-requests']),
    title: 'Requires: view-own-purchase-request or view-all-purchase-requests',
  })
  items.push({
    type: 'link',
    key: 'approvals',
    to: '/approvals',
    label: 'Pending Approvals',
    enabled: hasAnyPermission(['approve-purchase-request', 'reject-purchase-request']),
    title: 'Requires: approve-purchase-request or reject-purchase-request',
  })
  items.push({
    type: 'link',
    key: 'purchase-orders',
    to: '/purchase-orders',
    label: 'Purchase Orders',
    enabled: hasAnyPermission(['view-purchase-orders']),
    title: 'Requires: view-purchase-orders',
  })
  items.push({
    type: 'link',
    key: 'invoices',
    to: '/invoices',
    label: 'Invoices',
    enabled: hasAnyPermission(['view-invoices']),
    title: 'Requires: view-invoices',
  })
  items.push({
    type: 'link',
    key: 'suppliers',
    to: '/suppliers',
    label: 'Suppliers',
    enabled: hasAnyPermission(['view-suppliers']),
    title: 'Requires: view-suppliers',
  })

  items.push({ type: 'section', key: 'sec-admin', label: 'Admin' })
  items.push({
    type: 'link',
    key: 'admin-users',
    to: '/admin/users',
    label: 'Users & Roles',
    enabled: hasAnyPermission(['manage-users', 'manage-roles']),
    title: 'Requires: manage-users or manage-roles',
  })

  return items
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
