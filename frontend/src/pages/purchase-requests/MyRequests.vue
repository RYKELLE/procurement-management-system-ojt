<template>
  <div class="flex flex-col gap-6 h-full">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-wide">PURCHASE REQUESTS</h1>
        <p class="text-sm text-slate-500 mt-1">Manage all purchase requests</p>
      </div>
      <RouterLink
        v-if="canCreateRequest"
        to="/purchase-requests/create"
        class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold tracking-widest uppercase px-5 py-3 transition whitespace-nowrap"
      >
        + New Request
      </RouterLink>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-slate-200 px-5 py-4">
      <div class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-3">Filters</div>
      <div class="grid grid-cols-4 gap-4 items-end">
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</label>
          <select
            v-model="filters.status"
            class="border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none focus:border-slate-500 w-full"
          >
            <option value="">All</option>
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Date From</label>
          <input
            v-model="filters.dateFrom"
            type="date"
            class="border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none focus:border-slate-500 w-full"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Date To</label>
          <input
            v-model="filters.dateTo"
            type="date"
            class="border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none focus:border-slate-500 w-full"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-400 uppercase tracking-widest opacity-0">apply</label>
          <div class="flex gap-2">
            <button
              @click="applyFilters"
              class="flex-1 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold uppercase tracking-widest py-3 transition"
            >
              Apply
            </button>
            <button
              v-if="filtersActive"
              @click="clearFilters"
              class="flex-1 border border-slate-300 text-slate-500 hover:text-slate-700 text-xs font-bold uppercase tracking-widest py-3 transition"
            >
              Clear
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="bg-white border border-slate-200 py-16 text-center text-sm text-slate-400">
      Loading...
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 text-sm">
      {{ error }}
    </div>

    <!-- Table -->
    <div v-else class="flex flex-col flex-1 bg-white border border-slate-200 min-h-0">
      <div class="flex-1 overflow-y-auto">
      <table class="w-full text-sm table-fixed">
        <colgroup>
          <col style="width: 14%" />
          <col style="width: 15%" />
          <col style="width: 25%" />
          <col style="width: 14%" />
          <col style="width: 12%" />
          <col style="width: 20%" />
        </colgroup>
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Request ID</th>
            <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Requested By</th>
            <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Reason</th>
            <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Date</th>
            <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Status</th>
            <th class="text-left px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="filteredRequests.length === 0">
            <td colspan="6" class="text-center py-16 text-slate-400 text-sm">
              No purchase requests found.
            </td>
          </tr>
          <tr
            v-for="request in filteredRequests"
            :key="request.id"
            class="hover:bg-slate-50 transition"
          >
            <td class="px-4 py-3 font-semibold text-slate-700">PR-{{ String(request.id).padStart(5, '0') }}</td>
            <td class="px-4 py-3 text-slate-700">{{ request.requester?.name || 'N/A' }}</td>
            <td class="px-4 py-3 text-slate-500 whitespace-normal break-words">{{ request.reason }}</td>
            <td class="px-4 py-3 text-slate-500">{{ formatDate(request.created_at) }}</td>
            <td class="px-4 py-3">
              <span
                class="border text-xs font-semibold uppercase tracking-wide px-2.5 py-1"
                :class="statusClass(request.request_status)"
              >
                {{ request.request_status }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <RouterLink
                  :to="`/purchase-requests/${request.id}`"
                  class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-3 py-1.5 hover:bg-slate-50 transition"
                >
                  View
                </RouterLink>
                <template v-if="request.request_status === 'submitted'">
                  <button
                    v-if="canApproveRequest"
                    @click="handleApprove(request)"
                    class="bg-slate-800 text-white text-xs font-bold uppercase tracking-wide px-3 py-1.5 hover:bg-slate-700 transition"
                  >
                    Approve
                  </button>
                  <button
                    v-if="canRejectRequest"
                    @click="handleReject(request)"
                    class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-3 py-1.5 hover:bg-slate-50 transition"
                  >
                    Reject
                  </button>
                </template>
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
import { ref, computed, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const auth = useAuthStore()

const canCreateRequest = computed(() => auth.hasPermission('create-purchase-request'))

const canApproveRequest = computed(() => auth.hasPermission('approve-purchase-request'))
const canRejectRequest = computed(() => auth.hasPermission('reject-purchase-request'))

const filters = reactive({ status: '', dateFrom: '', dateTo: '' })
const activeFilters = reactive({ status: '', dateFrom: '', dateTo: '' })
const filtersActive = computed(() => activeFilters.status || activeFilters.dateFrom || activeFilters.dateTo)

const requests = ref([])
const loading = ref(true)
const error = ref('')

async function fetchRequests() {
  try {
    const response = await api.get('/purchase-requests')
    requests.value = response.data
  } catch (e) {
    error.value = 'Failed to load requests. Please try again.'
  } finally {
    loading.value = false
  }
}

const filteredRequests = computed(() => {
  return requests.value.filter(r => {
    if (activeFilters.status && r.request_status !== activeFilters.status) return false
    if (activeFilters.dateFrom && r.created_at < activeFilters.dateFrom) return false
    if (activeFilters.dateTo && r.created_at > activeFilters.dateTo + 'T23:59:59') return false
    return true
  })
})

function applyFilters() {
  activeFilters.status = filters.status
  activeFilters.dateFrom = filters.dateFrom
  activeFilters.dateTo = filters.dateTo
}

function clearFilters() {
  filters.status = ''
  filters.dateFrom = ''
  filters.dateTo = ''
  activeFilters.status = ''
  activeFilters.dateFrom = ''
  activeFilters.dateTo = ''
}

function formatDate(date) {
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

async function handleApprove(request) {
  try {
    await api.post(`/purchase-requests/${request.id}/approve`)
    await fetchRequests()
  } catch (e) {
    alert('Failed to approve request.')
  }
}

async function handleReject(request) {
  try {
    await api.post(`/purchase-requests/${request.id}/reject`)
    await fetchRequests()
  } catch (e) {
    alert('Failed to reject request.')
  }
}

onMounted(fetchRequests)
</script>
