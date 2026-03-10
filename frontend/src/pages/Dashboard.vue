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
          {{ card.value }}
        </div>
        <div class="text-sm text-slate-700 mt-auto">→ View Details</div>
      </RouterLink>
    </div>

    <!-- Recent Activity — fills remaining space -->
    <div class="flex flex-col flex-1 bg-white border border-slate-800 min-h-0">
      <div class="px-6 py-5 border-b border-slate-800 shrink-0">
        <h2 class="text-sm font-bold text-slate-800 tracking-widest uppercase">Recent Activity</h2>
      </div>
      <div class="flex-1 overflow-y-auto divide-y divide-slate-100 px-6">
        <div
          v-for="(item, index) in recentActivity"
          :key="index"
          class="flex items-center justify-between py-4"
        >
          <div class="flex items-center gap-4">
            <span class="text-slate-800 text-xs">■</span>
            <span class="text-sm text-slate-700">{{ item.text }}</span>
          </div>
          <span class="text-sm text-slate-400 whitespace-nowrap">{{ item.time }}</span>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
// Dummy data — replace with real API calls later
const statCards = [
  { label: 'Pending Purchase Requests', value: 12, link: '/purchase-requests' },
  { label: 'Active Purchase Orders',    value: 8,  link: '/purchase-orders' },
  { label: 'Unpaid Invoices',           value: 5,  link: '/invoices' },
  { label: 'Total Suppliers',           value: 24, link: '/suppliers' },
]

const recentActivity = [
  { text: 'PR-00045 created by John Doe',      time: '2 hours ago' },
  { text: 'PO-00123 approved by Jane Smith',   time: '4 hours ago' },
  { text: 'Invoice INV-00089 marked as paid',  time: '5 hours ago' },
  { text: 'PR-00044 rejected by Manager',      time: '1 day ago' },
  { text: 'New supplier ABC Corp added',       time: '2 days ago' },
  { text: 'PO-00122 created by John Doe',      time: '2 days ago' },
  { text: 'PR-00043 approved by Jane Smith',   time: '3 days ago' },
]
</script>