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
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Invoice ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Linked Order ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Amount</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Due Date</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Status</th>
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
              <td class="px-6 py-4 text-slate-700 font-medium">{{ invoice.id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ invoice.order_id }}</td>
              <td class="px-6 py-4 text-slate-700">${{ Number(invoice.amount).toFixed(2) }}</td>
              <td class="px-6 py-4 text-slate-500">{{ invoice.due_date.slice(0, 10) }}</td>
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
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '../../api/axios'

const auth = useAuthStore();

const invoices = ref([]);
const loading = ref(true);
const error = ref(null);

async function fetchInvoices(){
  try{
    const response = await api.get('/invoices')
    invoices.value = response.data;
    console.log(response.data);
  }catch(err){
    error.value = 'Failed'
  }finally{
    loading.value = false;
  }
}

onMounted(fetchInvoices)

// Only admin can mark invoices as paid
const canMarkAsPaid = computed(() => {
  return auth.hasPermission('manage-invoices')
})

async function handleMarkAsPaid(invoice) {
  try {
    await api.post(`/invoices/${invoice.id}/paid`);
    invoice.status = 'paid'
  } catch (error) {
    alert('Failed to mark as paid');
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
