<template>
  <div class="flex flex-col gap-6">

    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">INVOICE DETAIL</h1>
      <p class="text-sm text-slate-500 mt-1">{{ invoice.invoice_id }}</p>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-2 gap-4">

      <!-- Invoice Information -->
      <div class="bg-white border border-slate-800 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Invoice Information</h2>
        <div class="flex flex-col gap-4">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Invoice ID</div>
            <div class="text-sm text-slate-800">{{ invoice.invoice_id }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Linked Purchase Order</div>
            <div class="text-sm text-slate-800">{{ invoice.linked_order_id }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Date Created</div>
            <div class="text-sm text-slate-800">{{ invoice.date_created }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Processed By</div>
            <div class="text-sm text-slate-800">{{ invoice.processed_by }}</div>
          </div>
        </div>
      </div>

      <!-- Payment Information -->
      <div class="bg-white border border-slate-800 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Payment Information</h2>
        <div class="flex flex-col gap-4">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Amount Due</div>
            <div class="text-3xl font-bold text-slate-800">${{ invoice.amount.toFixed(2) }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Due Date</div>
            <div class="text-sm text-slate-800">{{ invoice.due_date }}</div>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Status</div>
            <span
              class="border text-xs font-semibold uppercase tracking-wide px-3 py-1.5"
              :class="statusClass(invoice.status)"
            >
              {{ invoice.status }}
            </span>
          </div>
        </div>
      </div>

    </div>

    <!-- Record Payment — admin only, hidden if already paid -->
    <div
      v-if="isAdmin && invoice.status !== 'paid'"
      class="bg-white border border-slate-800 px-6 py-5"
    >
      <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Record Payment</h2>

      <div class="flex items-end gap-4">

        <!-- Amount Paid -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
            Amount Paid <span class="text-red-500">*</span>
          </label>
          <input
            v-model.number="form.amount_paid"
            type="number"
            min="0"
            step="0.01"
            placeholder="0.00"
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-44 transition"
            :class="{ 'border-red-400': errors.amount_paid }"
            @input="errors.amount_paid = ''"
          />
          <span v-if="errors.amount_paid" class="text-xs text-red-500">{{ errors.amount_paid }}</span>
        </div>

        <!-- Payment Date -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
            Payment Date <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.payment_date"
            type="date"
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-44 transition"
            :class="{ 'border-red-400': errors.payment_date }"
            @input="errors.payment_date = ''"
          />
          <span v-if="errors.payment_date" class="text-xs text-red-500">{{ errors.payment_date }}</span>
        </div>

        <!-- Payment Method -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
            Payment Method <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.payment_method"
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-48 transition"
            :class="{ 'border-red-400': errors.payment_method }"
            @change="errors.payment_method = ''"
          >
            <option value="">Select method...</option>
            <option value="cash">Cash</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="check">Check</option>
          </select>
          <span v-if="errors.payment_method" class="text-xs text-red-500">{{ errors.payment_method }}</span>
        </div>

      </div>

      <!-- Error -->
      <div v-if="errors.general" class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
        {{ errors.general }}
      </div>

      <!-- Record Payment Button -->
      <button
        @click="handleRecordPayment"
        :disabled="loading"
        class="mt-5 bg-slate-800 hover:bg-slate-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-bold uppercase tracking-widest px-10 py-4 transition"
      >
        {{ loading ? 'Recording...' : 'Record Payment' }}
      </button>

    </div>

    <!-- Back to List -->
    <div class="pb-6">
      <RouterLink
        to="/invoices"
        class="border border-slate-800 text-slate-800 hover:bg-slate-100 text-sm font-bold uppercase tracking-widest px-10 py-4 transition"
      >
        Back to List
      </RouterLink>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const auth = useAuthStore()

const isAdmin = computed(() => {
  return (auth.user?.role || '').toLowerCase() === 'admin'
})

// Dummy data — replace with real API call using route.params.id later
const invoice = reactive({
  id: 88,
  invoice_id: 'INV-00088',
  linked_order_id: 'PO-00120',
  date_created: '2026-03-05',
  processed_by: 'Jane Smith',
  amount: 2100.00,
  due_date: '2026-03-12',
  status: 'unpaid',
})

const today = new Date().toISOString().split('T')[0]

const form = reactive({
  amount_paid: '',
  payment_date: today,
  payment_method: '',
})

const errors = reactive({
  amount_paid: '',
  payment_date: '',
  payment_method: '',
  general: '',
})

const loading = ref(false)

function validate() {
  let valid = true
  errors.amount_paid = ''
  errors.payment_date = ''
  errors.payment_method = ''
  errors.general = ''

  if (!form.amount_paid || form.amount_paid <= 0) {
    errors.amount_paid = 'Amount is required.'
    valid = false
  }
  if (!form.payment_date) {
    errors.payment_date = 'Payment date is required.'
    valid = false
  }
  if (!form.payment_method) {
    errors.payment_method = 'Payment method is required.'
    valid = false
  }
  return valid
}

async function handleRecordPayment() {
  if (!validate()) return

  loading.value = true
  try {
    // TODO: replace with real API call
    // await api.post(`/invoices/${invoice.id}/pay`, {
    //   amount_paid: form.amount_paid,
    //   payment_date: form.payment_date,
    //   payment_method: form.payment_method,
    // })

    // Simulate for now
    await new Promise(r => setTimeout(r, 800))
    invoice.status = 'paid'

  } catch (error) {
    errors.general = 'Failed to record payment. Please try again.'
  } finally {
    loading.value = false
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