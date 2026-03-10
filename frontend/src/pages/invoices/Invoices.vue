<template>
  <div class="flex flex-col gap-6 h-full">

    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">INVOICES</h1>
      <p class="text-sm text-slate-500 mt-1">Manage all invoices</p>
    </div>

    <!-- Table -->
    <div class="flex flex-col flex-1 bg-white border border-slate-800 min-h-0">
      <div class="flex-1 overflow-y-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-100 border-b border-slate-800 sticky top-0">
            <tr>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-32">Invoice ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Linked Order ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-32">Amount</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Due Date</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Status</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="invoices.length === 0">
              <td colspan="6" class="text-center py-12 text-slate-400 text-sm">No invoices found.</td>
            </tr>
            <tr
              v-for="invoice in invoices"
              :key="invoice.id"
              class="hover:bg-slate-50 transition"
            >
              <td class="px-6 py-4 text-slate-700 font-medium">{{ invoice.invoice_id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ invoice.linked_order_id }}</td>
              <td class="px-6 py-4 text-slate-700">${{ invoice.amount.toFixed(2) }}</td>
              <td class="px-6 py-4 text-slate-500">{{ invoice.due_date }}</td>
              <td class="px-6 py-4">
                <span
                  class="border text-xs font-semibold uppercase tracking-wide px-3 py-1.5"
                  :class="statusClass(invoice.status)"
                >
                  {{ invoice.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <RouterLink
                    :to="`/invoices/${invoice.id}`"
                    class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-50 transition"
                  >
                    View
                  </RouterLink>
                  <button
                    v-if="canMarkAsPaid && invoice.status !== 'paid'"
                    @click="handleMarkAsPaid(invoice)"
                    class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold uppercase tracking-wide px-4 py-1.5 transition"
                  >
                    Mark as Paid
                  </button>
                </div>
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

// Only admin can mark invoices as paid
const canMarkAsPaid = computed(() => {
  return (auth.user?.role || '').toLowerCase() === 'admin'
})

// Dummy data — replace with real API call later
const invoices = ref([
  { id: 89, invoice_id: 'INV-00089', linked_order_id: 'PO-00121', amount: 890.50,  due_date: '2026-03-15', status: 'paid'    },
  { id: 88, invoice_id: 'INV-00088', linked_order_id: 'PO-00120', amount: 2100.00, due_date: '2026-03-12', status: 'unpaid'  },
  { id: 87, invoice_id: 'INV-00087', linked_order_id: 'PO-00119', amount: 675.25,  due_date: '2026-03-01', status: 'overdue' },
  { id: 86, invoice_id: 'INV-00086', linked_order_id: 'PO-00118', amount: 1540.00, due_date: '2026-03-10', status: 'unpaid'  },
  { id: 85, invoice_id: 'INV-00085', linked_order_id: 'PO-00117', amount: 3200.00, due_date: '2026-02-28', status: 'overdue' },
  { id: 84, invoice_id: 'INV-00084', linked_order_id: 'PO-00116', amount: 945.00,  due_date: '2026-03-08', status: 'paid'    },
])

async function handleMarkAsPaid(invoice) {
  try {
    // TODO: replace with real API call
    // await api.post(`/invoices/${invoice.id}/pay`)

    // Update status locally for now
    invoice.status = 'paid'
  } catch (error) {
    console.error('Failed to mark as paid', error)
  }
}

function statusClass(status) {
  switch (status) {
    case 'paid':    return 'border-slate-600 text-slate-700 bg-slate-100'
    case 'unpaid':  return 'border-slate-400 text-slate-500 bg-white'
    case 'overdue': return 'border-slate-300 text-slate-400 bg-white'
    default:        return 'border-slate-300 text-slate-500'
  }
}
</script>