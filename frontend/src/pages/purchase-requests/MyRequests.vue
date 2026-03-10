<template>
  <div class="flex flex-col gap-6 h-full">

    <!-- Header -->
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-wide">PURCHASE REQUESTS</h1>
        <p class="text-sm text-slate-500 mt-1">Manage all purchase requests</p>
      </div>
      <RouterLink
        to="/purchase-requests/create"
        class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold tracking-widest uppercase px-6 py-4 transition whitespace-nowrap"
      >
        + New Request
      </RouterLink>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-slate-800 px-6 py-5">
      <div class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">Filters</div>
      <div class="flex items-end gap-4">

        <!-- Status -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</label>
          <select
            v-model="filters.status"
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-44"
          >
            <option value="">All</option>
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <!-- Date From -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Date From</label>
          <input
            v-model="filters.dateFrom"
            type="date"
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-44"
          />
        </div>

        <!-- Date To -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Date To</label>
          <input
            v-model="filters.dateTo"
            type="date"
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-44"
          />
        </div>

        <!-- Apply -->
        <button
          @click="applyFilters"
          class="border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-bold uppercase tracking-widest px-6 py-2.5 transition"
        >
          Apply
        </button>

        <!-- Clear -->
        <button
          v-if="filtersActive"
          @click="clearFilters"
          class="text-sm text-slate-400 hover:text-slate-600 transition"
        >
          Clear
        </button>

      </div>
    </div>

    <!-- Table -->
    <div class="flex flex-col flex-1 bg-white border border-slate-800 min-h-0">
      <div class="flex-1 overflow-y-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-100 border-b border-slate-800 sticky top-0">
            <tr>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Request ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Requested By</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-40">Date Created</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-36">Status</th>
              <th v-if="canAction" class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="filteredRequests.length === 0">
              <td :colspan="canAction ? 5 : 4" class="text-center py-12 text-slate-400 text-sm">
                No purchase requests found.
              </td>
            </tr>
            <tr
              v-for="request in filteredRequests"
              :key="request.id"
              class="hover:bg-slate-50 transition"
            >
              <td class="px-6 py-4 text-slate-700 font-medium">{{ request.request_id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ request.requested_by }}</td>
              <td class="px-6 py-4 text-slate-500">{{ request.date_created }}</td>
              <td class="px-6 py-4">
                <span
                  class="border text-xs font-semibold uppercase tracking-wide px-3 py-1.5"
                  :class="statusClass(request.status)"
                >
                  {{ request.status }}
                </span>
              </td>
              <td v-if="canAction" class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <RouterLink
                    :to="`/purchase-requests/${request.id}`"
                    class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-50 transition"
                  >
                    View
                  </RouterLink>
                  <template v-if="request.status === 'submitted'">
                    <button
                      @click="handleApprove(request)"
                      class="bg-slate-800 text-white text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-700 transition"
                    >
                      Approve
                    </button>
                    <button
                      @click="handleReject(request)"
                      class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-50 transition"
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
import { ref, computed, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

// Only admin and approver can see the Actions column
const canAction = computed(() => {
  const role = (auth.user?.role || '').toLowerCase()
  return role === 'admin' || role === 'approver'
})

const filters = reactive({ status: '', dateFrom: '', dateTo: '' })
const filtersActive = computed(() => filters.status || filters.dateFrom || filters.dateTo)

// Dummy data — replace with real API calls later
const requests = ref([
  { id: 45, request_id: 'PR-00045', requested_by: 'John Doe',   date_created: '2026-03-04', status: 'submitted' },
  { id: 44, request_id: 'PR-00044', requested_by: 'Jane Smith', date_created: '2026-03-03', status: 'approved'  },
  { id: 43, request_id: 'PR-00043', requested_by: 'Bob Wilson', date_created: '2026-03-02', status: 'approved'  },
  { id: 42, request_id: 'PR-00042', requested_by: 'Alice Brown', date_created: '2026-03-01', status: 'rejected' },
  { id: 41, request_id: 'PR-00041', requested_by: 'John Doe',   date_created: '2026-02-28', status: 'submitted' },
  { id: 40, request_id: 'PR-00040', requested_by: 'Jane Smith', date_created: '2026-02-27', status: 'draft'     },
])

const filteredRequests = computed(() => {
  return requests.value.filter(r => {
    if (filters.status && r.status !== filters.status) return false
    if (filters.dateFrom && r.date_created < filters.dateFrom) return false
    if (filters.dateTo && r.date_created > filters.dateTo) return false
    return true
  })
})

function applyFilters() {
  // filters are reactive — filteredRequests updates automatically
  // when backend is connected, make the API call here instead
}

function clearFilters() {
  filters.status = ''
  filters.dateFrom = ''
  filters.dateTo = ''
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

function handleApprove(request) {
  // TODO: call API POST /api/purchase-requests/{id}/approve
  console.log('Approve', request.request_id)
}

function handleReject(request) {
  // TODO: call API POST /api/purchase-requests/{id}/reject
  console.log('Reject', request.request_id)
}
</script>