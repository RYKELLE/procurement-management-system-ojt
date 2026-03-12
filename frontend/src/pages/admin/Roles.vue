<template>
  <div class="flex flex-col gap-6 h-full">

    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">USER & ROLE MANAGEMENT</h1>
      <p class="text-sm text-slate-500 mt-1">Manage users and role permissions</p>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-0">
      <RouterLink
        to="/admin/users"
        class="px-8 py-3 text-sm font-bold uppercase tracking-widest border border-slate-800 text-slate-800 hover:bg-slate-50 transition"
      >
        Users
      </RouterLink>
      <button class="px-8 py-3 text-sm font-bold uppercase tracking-widest border border-slate-800 bg-slate-800 text-white">
        Roles
      </button>
    </div>

    <!-- Role Cards -->
    <div class="flex flex-col gap-4 overflow-y-auto pb-6">
      <div v-for="role in roles" :key="role.key" class="bg-white border border-slate-800">

        <!-- Role Header -->
        <div class="px-6 py-4 border-b border-slate-200">
          <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">{{ role.name }}</h2>
        </div>

        <!-- Permissions Grid -->
        <div class="px-6 py-5">
          <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Permissions</div>
          <div class="grid grid-cols-2 gap-3">
            <label
              v-for="permission in allPermissions"
              :key="permission.key"
              class="flex items-center gap-3 cursor-pointer"
            >
              <input
                type="checkbox"
                :checked="role.permissions.includes(permission.key)"
                @change="togglePermission(role, permission.key)"
                class="w-4 h-4 accent-slate-800"
              />
              <span class="text-sm text-slate-700">{{ permission.label }}</span>
            </label>
          </div>
        </div>

        <!-- Save -->
        <div class="px-6 pb-5 flex items-center gap-4">
          <button
            @click="handleSave(role)"
            :disabled="role.saving"
            class="bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white text-xs font-bold uppercase tracking-widest px-6 py-2.5 transition"
          >
            {{ role.saving ? 'Saving...' : 'Save Changes' }}
          </button>
          <span v-if="role.saved" class="text-xs text-green-600 font-semibold">Saved!</span>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '@/api/axios'

const allPermissions = [
  { key: 'create-purchase-request',   label: 'Create Purchase Requests' },
  { key: 'view-own-purchase-request', label: 'View Own Purchase Requests' },
  { key: 'view-all-purchase-requests',label: 'View All Purchase Requests' },
  { key: 'submit-purchase-request',   label: 'Submit Purchase Request' },
  { key: 'approve-purchase-request',  label: 'Approve Purchase Requests' },
  { key: 'reject-purchase-request',   label: 'Reject Purchase Requests' },
  { key: 'manage-purchase-orders',    label: 'Manage Purchase Orders' },
  { key: 'manage-invoices',           label: 'Manage Invoices' },
  { key: 'view-purchase-orders',      label: 'View Purchase Orders' },
  { key: 'view-invoices',             label: 'View Invoices' },
  { key: 'manage-users',              label: 'Manage Users' },
  { key: 'manage-roles',              label: 'Manage Roles' },  
  { key: 'view-suppliers',            label: 'View Suppliers' },
  { key: 'manage-suppliers',          label: 'Manage Suppliers' },
]

const roles = ref([])

async function fetchRoles() {
  try {
    const response = await api.get('/roles')
    roles.value = (response.data || []).map(role => ({
      ...role,
      saving: false,
      saved: false,
      permissions: role.permissions || [],
    }))
  } catch (error) {
    console.error('Failed to load roles', error)
    roles.value = []
  }
}

onMounted(fetchRoles)

function togglePermission(role, permissionKey) {
  const index = role.permissions.indexOf(permissionKey)
  if (index === -1) {
    role.permissions.push(permissionKey)
  } else {
    role.permissions.splice(index, 1)
  }
  role.saved = false
}

async function handleSave(role) {
  role.saving = true
  role.saved = false
  try {
    const plainPermissions = JSON.parse(JSON.stringify(role.permissions)) // deep clone
    console.log('Sending:', plainPermissions)
    await api.patch(`/roles/${role.key}/permissions`, {
      permissions: plainPermissions
    })
    role.saved = true
    setTimeout(() => { role.saved = false }, 3000)
  } catch (error) {
    console.error('Failed to save permissions', error)
  } finally {
    role.saving = false
  }
}
</script>
