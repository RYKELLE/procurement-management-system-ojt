<template>
  <div v-if="loading">Loading...</div>

  <div v-else-if="error">{{ error }}</div>

  <div v-else-if="order" class="flex flex-col gap-6">

    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">PURCHASE ORDER DETAIL</h1>
      <p class="text-sm text-slate-500 mt-1">{{ order.id }}</p>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-2 gap-4">

      <!-- Supplier Information -->
      <div class="bg-white border border-slate-800 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Supplier Information</h2>
        <div class="flex flex-col gap-4">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Name</div>
            <div class="text-sm text-slate-800">{{ order.supplier.supplier_name }}</div>
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
            <div class="text-sm text-slate-800">(248) 762-0356</div>
          </div>
        </div>
      </div>

      <!-- Order Information -->
      <div class="bg-white border border-slate-800 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Order Information</h2>
        <div class="flex flex-col gap-4">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Order ID</div>
            <div class="text-sm text-slate-800">{{ order.id }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Linked Request ID</div>
            <div class="text-sm text-slate-800">{{ order.request_id }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Date Created</div>
            <div class="text-sm text-slate-800">{{ order.created_at.slice(0, 10) }}</div>
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
          <tr v-for="(item, index) in order.purchase_request.items" :key="index" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-slate-700">{{ item.item.item_name }}</td>
            <td class="px-6 py-4 text-slate-700 text-right">{{ item.item_quantity }}</td>
            <td class="px-6 py-4 text-slate-700 text-right">${{ Number(item.unit_price).toFixed(2) }}</td>
            <td class="px-6 py-4 text-slate-700 text-right">${{ (item.item_quantity * Number(item.unit_price)).toFixed(2) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="bg-slate-100 border-t border-slate-200">
            <td colspan="2" class="px-6 py-4"></td>
            <td class="px-6 py-4 text-right text-sm font-bold text-slate-700 uppercase tracking-wide">Total Amount:</td>
            <td class="px-6 py-4 text-right text-sm font-bold text-slate-800">${{ Number(totalAmount).toFixed(2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-4 pb-6">
      <button
        v-if="order.status === 'active'"
        @click="markAsCompleted"
        class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold uppercase tracking-widest px-10 py-4 transition"
      >
        Mark as Completed
      </button>
      <RouterLink
        v-if="order.status === 'completed'"
        :to="`/invoices/${order.id}`"
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
import { ref, computed,onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()

const order = ref(null);
const loading = ref(true);
const error = ref(null);

async function fetchOrder() {
  try{
    const response = await api.get(`/purchase-orders/${route.params.id}`)
    order.value = response.data;
    console.log(order.value)
  }catch(err){
    error.value = 'Failed to load Purchase Order'
  }finally{
    loading.value = false;
  }
}

onMounted(fetchOrder);

const totalAmount = computed(() => {
  if(!order.value) return 0
  return order.value.purchase_request.items.reduce((sum, item) => {
    return sum + item.item_quantity * item.unit_price;
  }, 0)
})

async function markAsCompleted(){
  try{
    const response = await api.post(`purchase-orders/${route.params.id}/complete`);
    order.value.status = 'completed';
  }catch(err){
    alert('Failed');
  }

}



function statusClass(status) {
  switch (status) {
    case 'active':    return 'border-slate-600 text-slate-700 bg-slate-50'
    case 'completed': return 'border-slate-300 text-slate-400 bg-white'
    case 'cancelled': return 'border-red-200 text-red-400 bg-white'
    default:          return 'border-slate-300 text-slate-500'
  }
}
</script>