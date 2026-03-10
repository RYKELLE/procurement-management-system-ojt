<template>
  <div class="flex flex-col gap-6">

    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">PURCHASE ORDER DETAIL</h1>
      <p class="text-sm text-slate-500 mt-1">{{ order.order_id }}</p>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-2 gap-4">

      <!-- Supplier Information -->
      <div class="bg-white border border-slate-800 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Supplier Information</h2>
        <div class="flex flex-col gap-4">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Name</div>
            <div class="text-sm text-slate-800">{{ order.supplier.name }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Contact Person</div>
            <div class="text-sm text-slate-800">{{ order.supplier.contact }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Email</div>
            <div class="text-sm text-slate-800">{{ order.supplier.email }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Phone</div>
            <div class="text-sm text-slate-800">{{ order.supplier.phone }}</div>
          </div>
        </div>
      </div>

      <!-- Order Information -->
      <div class="bg-white border border-slate-800 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Order Information</h2>
        <div class="flex flex-col gap-4">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Order ID</div>
            <div class="text-sm text-slate-800">{{ order.order_id }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Linked Request ID</div>
            <div class="text-sm text-slate-800">{{ order.linked_request_id }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Date Created</div>
            <div class="text-sm text-slate-800">{{ order.date_created }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Status</div>
            <span
              class="border text-xs font-semibold uppercase tracking-wide px-3 py-1.5"
              :class="statusClass(order.status)"
            >
              {{ order.status }}
            </span>
          </div>
        </div>
      </div>

    </div>

    <!-- Line Items -->
    <div class="bg-white border border-slate-800">
      <div class="px-6 py-5 border-b border-slate-200">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Line Items</h2>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-100 border-b border-slate-200">
          <tr>
            <th class="text-left px-6 py-3 font-bold text-slate-700 uppercase tracking-wide text-xs">Item</th>
            <th class="text-right px-6 py-3 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Quantity</th>
            <th class="text-right px-6 py-3 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Unit Price</th>
            <th class="text-right px-6 py-3 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="(item, index) in order.items" :key="index" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-slate-700">{{ item.name }}</td>
            <td class="px-6 py-4 text-slate-700 text-right">{{ item.quantity }}</td>
            <td class="px-6 py-4 text-slate-700 text-right">${{ item.unit_price.toFixed(2) }}</td>
            <td class="px-6 py-4 text-slate-700 text-right">${{ (item.quantity * item.unit_price).toFixed(2) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="bg-slate-100 border-t border-slate-200">
            <td colspan="2" class="px-6 py-4"></td>
            <td class="px-6 py-4 text-right text-sm font-bold text-slate-700 uppercase tracking-wide">Total Amount:</td>
            <td class="px-6 py-4 text-right text-sm font-bold text-slate-800">${{ totalAmount.toFixed(2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-4 pb-6">
      <RouterLink
        :to="`/invoices`"
        class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold uppercase tracking-widest px-10 py-4 transition"
      >
        View Invoice
      </RouterLink>
      <RouterLink
        to="/purchase-orders"
        class="border border-slate-800 text-slate-800 hover:bg-slate-100 text-sm font-bold uppercase tracking-widest px-10 py-4 transition"
      >
        Back to List
      </RouterLink>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// Dummy data — replace with real API call using route.params.id later
const order = {
  id: 123,
  order_id: 'PO-00123',
  linked_request_id: 'PR-00044',
  date_created: '2026-03-03',
  status: 'active',
  supplier: {
    name: 'ABC Office Supplies',
    contact: 'John Smith',
    email: 'john@abcoffice.com',
    phone: '+1 (555) 123-4567',
  },
  items: [
    { name: 'Office Chair', quantity: 5,  unit_price: 150.00 },
    { name: 'Desk Lamp',    quantity: 10, unit_price: 45.00  },
    { name: 'USB Cable',    quantity: 20, unit_price: 12.00  },
  ]
}

const totalAmount = computed(() => {
  return order.items.reduce((sum, item) => sum + item.quantity * item.unit_price, 0)
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