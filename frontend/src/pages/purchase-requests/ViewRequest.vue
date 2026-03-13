<template>
  <div class="flex flex-col gap-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-wide">VIEW REQUEST</h1>
        <p class="text-sm text-slate-500 mt-1">Purchase request details</p>
      </div>
      <RouterLink
        to="/purchase-requests"
        class="border border-slate-800 text-slate-800 hover:bg-slate-100 text-sm font-bold uppercase tracking-widest px-5 py-3 transition"
      >
        Back
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="bg-white border border-slate-200 py-16 text-center text-sm text-slate-400">
      Loading...
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 text-sm">
      {{ error }}
    </div>

    <template v-else>

      <!-- Request Details -->
      <div class="bg-white border border-slate-200 px-6 py-5">
        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-5">Request Details</h2>
        <div class="grid grid-cols-2 gap-6">

          <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Request ID</span>
            <span class="text-sm text-slate-800 font-semibold">PR-{{ String(request.id).padStart(5, '0') }}</span>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</span>
            <span
              class="border text-xs font-semibold uppercase tracking-wide px-2.5 py-1 w-fit"
              :class="statusClass(request.request_status)"
            >
              {{ request.request_status }}
            </span>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Requested By</span>
            <span class="text-sm text-slate-700">{{ request.requester?.name || 'N/A' }}</span>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Date Created</span>
            <span class="text-sm text-slate-700">{{ formatDate(request.created_at) }}</span>
          </div>

          <div class="flex flex-col gap-1 col-span-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Reason</span>
            <span class="text-sm text-slate-700">{{ request.reason }}</span>
          </div>

          <div v-if="request.approved_by" class="flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Approved / Rejected By</span>
            <span class="text-sm text-slate-700">{{ request.approver?.name || 'N/A' }}</span>
          </div>

          <div v-if="request.date_approved" class="flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Date Approved</span>
            <span class="text-sm text-slate-700">{{ formatDate(request.date_approved) }}</span>
          </div>

        </div>
      </div>

      <!-- Line Items -->
      <div class="bg-white border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
          <h2 class="text-xs font-bold text-slate-700 uppercase tracking-widest">Line Items</h2>
        </div>
        <table class="w-full text-sm table-fixed">
          <colgroup>
            <col style="width: 5%" />
            <col style="width: 40%" />
            <col style="width: 20%" />
            <col style="width: 20%" />
            <col style="width: 15%" />
          </colgroup>
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">#</th>
              <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Item</th>
              <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Quantity</th>
              <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Unit Price</th>
              <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Subtotal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(item, index) in request.items" :key="item.id" class="hover:bg-slate-50 transition">
              <td class="px-4 py-3 text-slate-500">{{ index + 1 }}</td>
              <td class="px-4 py-3 text-slate-700">{{ item.item?.item_name || 'N/A' }}</td>
              <td class="px-4 py-3 text-slate-700">{{ item.item_quantity }}</td>
              <td class="px-4 py-3 text-slate-700">${{ Number(item.unit_price).toFixed(2) }}</td>
              <td class="px-4 py-3 text-slate-700 font-semibold">${{ (item.item_quantity * item.unit_price).toFixed(2) }}</td>
            </tr>
          </tbody>
          <tfoot class="bg-slate-50 border-t border-slate-200">
            <tr>
              <td colspan="4" class="px-4 py-4 text-right text-sm font-bold text-slate-700 uppercase tracking-widest">Total</td>
              <td class="px-4 py-4 text-sm font-bold text-slate-800">${{ total.toFixed(2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3 pb-6">

        <!-- Staff: submit if draft -->
        <button
          v-if="canSubmit"
          @click="handleSubmit"
          :disabled="actionLoading"
          class="bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white text-sm font-bold uppercase tracking-widest px-8 py-3 transition"
        >
          {{ actionLoading ? 'Submitting...' : 'Submit for Approval' }}
        </button>

        <!-- Approver: approve/reject if submitted -->
        <template v-if="canApprove || canReject">
          <button
            v-if="canApprove"
            @click="handleApprove"
            :disabled="actionLoading"
            class="bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white text-sm font-bold uppercase tracking-widest px-8 py-3 transition"
          >
            {{ actionLoading ? 'Processing...' : 'Approve' }}
          </button>
          <button
            v-if="canReject"
            @click="handleReject"
            :disabled="actionLoading"
            class="border border-slate-800 text-slate-800 hover:bg-slate-100 disabled:opacity-60 text-sm font-bold uppercase tracking-widest px-8 py-3 transition"
          >
            {{ actionLoading ? 'Processing...' : 'Reject' }}
          </button>
        </template>

      </div>

    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const request = ref({})
const loading = ref(true)
const error = ref('')
const actionLoading = ref(false)

async function fetchRequest() {
  try {
    const response = await api.get(`/purchase-requests/${route.params.id}`)
    request.value = response.data
  } catch (e) {
    error.value = 'Failed to load request. Please try again.'
  } finally {
    loading.value = false
  }
}

const total = computed(() => {
  return (request.value.items || []).reduce((sum, item) => {
    return sum + item.item_quantity * item.unit_price
  }, 0)
})

const canSubmit = computed(() => {
  return auth.hasPermission('submit-purchase-request') && request.value.request_status === 'draft'
})

const canApprove = computed(() => auth.hasPermission('approve-purchase-request') && request.value.request_status === 'submitted')
const canReject = computed(() => auth.hasPermission('reject-purchase-request') && request.value.request_status === 'submitted')

async function handleSubmit() {
  actionLoading.value = true
  try {
    await api.post(`/purchase-requests/${route.params.id}/submit`)
    await fetchRequest()
  } catch (e) {
    error.value = 'Failed to submit request.'
  } finally {
    actionLoading.value = false
  }
}

async function handleApprove() {
  actionLoading.value = true
  try {
    await api.post(`/purchase-requests/${route.params.id}/approve`)
    await fetchRequest()
  } catch (e) {
    error.value = 'Failed to approve request.'
  } finally {
    actionLoading.value = false
  }
}

async function handleReject() {
  actionLoading.value = true
  try {
    await api.post(`/purchase-requests/${route.params.id}/reject`)
    await fetchRequest()
  } catch (e) {
    error.value = 'Failed to reject request.'
  } finally {
    actionLoading.value = false
  }
}

function formatDate(date) {
  if (!date) return 'N/A'
  return new Date(date).toISOString().split('T')[0]
}

function statusClass(status) {
  switch (status) {
    case 'submitted': return 'border-slate-400 text-slate-600 bg-slate-50'
    case 'approved':  return 'border-slate-600 text-slate-700 bg-slate-100'
    case 'rejected':  return 'border-slate-300 text-slate-400 bg-white'
    case 'draft':     return 'border-slate-200 text-slate-400 bg-white'
    default:          return 'border-slate-300 text-slate-500'
  }
}

onMounted(fetchRequest)
</script>
