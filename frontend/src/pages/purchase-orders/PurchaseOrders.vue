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
              <td class="px-6 py-4 text-slate-700 font-medium">{{ order.order_id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ order.linked_request_id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ order.supplier }}</td>
              <td class="px-6 py-4 text-slate-700">${{ order.total_amount.toFixed(2) }}</td>
              <td class="px-6 py-4">
                <span
                  class="border text-xs font-semibold uppercase tracking-wide px-3 py-1.5"
                  :class="statusClass(order.status)"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-500">{{ order.date_created }}</td>
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
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const isStaff = computed(() => {
  return (auth.user?.role || '').toLowerCase() === 'staff'
})

// Dummy data — replace with real API call later
const orders = ref([
  { id: 123, order_id: 'PO-00123', linked_request_id: 'PR-00044', supplier: 'ABC Office Supplies', total_amount: 1250.00, status: 'active',    date_created: '2026-03-03', created_by: 3 },
  { id: 122, order_id: 'PO-00122', linked_request_id: 'PR-00043', supplier: 'TechCorp Inc',         total_amount: 3400.00, status: 'active',    date_created: '2026-03-02', created_by: 3 },
  { id: 121, order_id: 'PO-00121', linked_request_id: 'PR-00040', supplier: 'Global Traders',       total_amount: 890.50,  status: 'completed', date_created: '2026-02-27', created_by: 2 },
  { id: 120, order_id: 'PO-00120', linked_request_id: 'PR-00038', supplier: 'Office Depot Pro',     total_amount: 2100.00, status: 'active',    date_created: '2026-02-26', created_by: 3 },
  { id: 119, order_id: 'PO-00119', linked_request_id: 'PR-00036', supplier: 'ABC Office Supplies',  total_amount: 675.25,  status: 'completed', date_created: '2026-02-24', created_by: 2 },
  { id: 118, order_id: 'PO-00118', linked_request_id: 'PR-00035', supplier: 'Stationery World',     total_amount: 1540.00, status: 'active',    date_created: '2026-02-23', created_by: 3 },
])

// Staff only sees orders linked to their own requests
// When connected to backend, the API will handle this filtering
const visibleOrders = computed(() => {
  if (isStaff.value) {
    // TODO: filter by auth.user.id against the request's requested_by
    // For now, dummy filter by created_by matching user id 3 as example
    return orders.value.filter(o => o.created_by === auth.user?.id)
  }
  return orders.value
})

function statusClass(status) {
  switch (status) {
    case 'active':    return 'border-slate-600 text-slate-700 bg-slate-50'
    case 'completed': return 'border-slate-300 text-slate-400 bg-white'
    case 'cancelled': return 'border-red-200 text-red-400 bg-white'
    default:          return 'border-slate-300 text-slate-500'
  }
}
</script>