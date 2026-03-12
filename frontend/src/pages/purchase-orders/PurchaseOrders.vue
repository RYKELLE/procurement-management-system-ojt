<template>
  <div class="flex flex-col gap-6 h-full">

    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">PURCHASE ORDERS</h1>
      <p class="text-sm text-slate-500 mt-1">View all purchase orders</p>
    </div>

    <!-- Table -->
    <div class="flex flex-col flex-1 bg-white border border-slate-800 min-h-0">
      <div class="flex-1 overflow-y-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-100 border-b border-slate-800 sticky top-0">
            <tr>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-32">Order ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Linked Request ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Supplier</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Total Amount</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Status</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Date Created</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="visibleOrders.length === 0">
              <td colspan="7" class="text-center py-12 text-slate-400 text-sm">
                No purchase orders found.
              </td>
            </tr>
            <tr
              v-for="order in visibleOrders"
              :key="order.id"
              class="hover:bg-slate-50 transition"
            >
              <td class="px-6 py-4 text-slate-700 font-medium">{{ order.id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ order.request_id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ order.supplier.supplier_name }}</td>
              <td class="px-6 py-4 text-slate-700">${{ Number(order.order_total_amount).toFixed(2) }}</td>
              <td class="px-6 py-4">
                <span
                  class="border text-xs font-semibold uppercase tracking-wide px-3 py-1.5"
                  :class="statusClass(order.status)"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-500">{{ order.created_at.slice(0, 10) }}</td>
              <td class="px-6 py-4">
                <RouterLink
                  :to="`/purchase-orders/${order.id}`"
                  class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-50 transition"
                >
                  View
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api/axios'


const orders = ref([])
const loading = ref(true)
const error = ref(null)

const visibleOrders = computed(() => orders.value)

async function fetchOrders() {
  try {
    const response = await api.get('/purchase-orders')
    orders.value = response.data
  } catch (err) {
    error.value = 'Failed to load purchase orders.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchOrders)

function statusClass(status) {
  switch (status) {
    case 'active':    return 'border-slate-600 text-slate-700 bg-slate-50'
    case 'completed': return 'border-slate-300 text-slate-400 bg-white'
    case 'cancelled': return 'border-red-200 text-red-400 bg-white'
    default:          return 'border-slate-300 text-slate-500'
  }
}
</script>