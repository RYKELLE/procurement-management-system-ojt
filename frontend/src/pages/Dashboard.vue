<template>
  <div class="flex flex-col gap-6 h-full">

    <!-- Header -->
    <div class="mb-7">
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">DASHBOARD</h1>
      <p class="text-sm text-slate-500 mt-1">Overview of procurement activities</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-4 gap-4 mb-7">
      <RouterLink
        v-for="card in statCards"
        :key="card.label"
        :to="card.link"
        class="flex flex-col bg-white border border-slate-800 p-6 hover:bg-slate-50 transition"
      >
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide leading-snug mb-3">
          {{ card.label }}
        </div>
        <div class="text-5xl font-bold text-slate-800 mb-4">
          {{ loading ? '—' : card.value }}
        </div>
        <div class="text-sm text-slate-700 mt-auto">→ View Details</div>
      </RouterLink>
    </div>

    <!-- Recent Activity -->
    <div class="flex flex-col flex-1 bg-white border border-slate-800 min-h-0">
      <div class="px-6 py-5 border-b border-slate-800 shrink-0">
        <h2 class="text-sm font-bold text-slate-800 tracking-widest uppercase">Recent Activity</h2>
      </div>
      <div class="flex-1 overflow-y-auto divide-y divide-slate-100 px-6">

        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <span class="text-sm text-slate-400">Loading...</span>
        </div>

        <!-- Empty -->
        <div v-else-if="recentActivity.length === 0" class="flex items-center justify-center py-12">
          <span class="text-sm text-slate-400">No recent activity.</span>
        </div>

        <!-- Activity rows -->
        <div
          v-else
          v-for="item in recentActivity"
          :key="item.id"
          class="flex items-center justify-between py-4"
        >
          <div class="flex items-center gap-4">
            <span class="text-slate-800 text-xs">■</span>
            <span class="text-sm text-slate-700">{{ item.description }}</span>
          </div>
          <span class="text-sm text-slate-400 whitespace-nowrap">{{ item.created_at }}</span>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'

const loading = ref(true)
const recentActivity = ref([])

const statCards = ref([
  { label: 'Pending Purchase Requests', value: 0, link: '/purchase-requests' },
  { label: 'Active Purchase Orders',    value: 0, link: '/purchase-orders' },
  { label: 'Unpaid Invoices',           value: 0, link: '/invoices' },
  { label: 'Total Suppliers',           value: 0, link: '/suppliers' },
])

async function fetchDashboard() {
  try {
    const response = await api.get('/dashboard')
    const data = response.data

    statCards.value[0].value = data.pending_requests ?? 0
    statCards.value[1].value = data.active_orders    ?? 0
    statCards.value[2].value = data.unpaid_invoices  ?? 0
    statCards.value[3].value = data.total_suppliers  ?? 0

    recentActivity.value = data.recent_activity ?? []
  } catch (error) {
    console.error('Failed to load dashboard', error)
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboard)
</script>