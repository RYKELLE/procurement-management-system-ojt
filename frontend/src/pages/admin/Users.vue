<template>
    <div class="flex flex-col gap-6 h-full">

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-wide">USER & ROLE MANAGEMENT</h1>
            <p class="text-sm text-slate-500 mt-1">Manage users and role permissions</p>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-0">
            <button
                class="px-8 py-3 text-sm font-bold uppercase tracking-widest border border-slate-800 bg-slate-800 text-white">
                Users
            </button>
            <RouterLink to="/admin/roles"
                class="px-8 py-3 text-sm font-bold uppercase tracking-widest border border-slate-800 text-slate-800 hover:bg-slate-50 transition">
                Roles
            </RouterLink>
        </div>

        <!-- Users Table -->
        <div class="flex flex-col flex-1 bg-white border border-slate-800 min-h-0">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 shrink-0">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Users</h2>
                <button @click="openAddModal"
                    class="border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold uppercase tracking-widest px-4 py-2 transition">
                    + Add User
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200 sticky top-0">
                        <tr>
                            <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">
                                Name</th>
                            <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">
                                Email</th>
                            <th
                                class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-48">
                                Assigned Role</th>
                            <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="users.length === 0">
                            <td colspan="4" class="text-center py-12 text-slate-400 text-sm">No users found.</td>
                        </tr>
                        <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ user.name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ user.email }}</td>
                            <td class="px-6 py-4">
                                <select v-model="user.role" @change="handleRoleChange(user)"
                                    class="border border-slate-300 px-3 py-1.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-full">
                                    <option value="admin">Admin</option>
                                    <option value="approver">Approver</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="openEditModal(user)"
                                        class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-50 transition">
                                        Edit
                                    </button>
                                    <button @click="openDeleteConfirm(user)"
                                        class="border border-slate-200 text-slate-400 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:border-red-300 hover:text-red-500 transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add User Modal -->
        <div v-if="addModal.open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
            @click.self="closeAddModal">
            <div class="bg-white border border-slate-800 w-full max-w-md px-8 py-7">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-6">Add New User</h2>
                <div class="flex flex-col gap-4">

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Name <span
                                class="text-red-500">*</span></label>
                        <input v-model="addModal.form.name" type="text" placeholder="Full name"
                            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition"
                            :class="{ 'border-red-400': addModal.errors.name }" @input="addModal.errors.name = ''" />
                        <span v-if="addModal.errors.name" class="text-xs text-red-500">{{ addModal.errors.name }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Email <span
                                class="text-red-500">*</span></label>
                        <input v-model="addModal.form.email" type="email" placeholder="Email address"
                            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition"
                            :class="{ 'border-red-400': addModal.errors.email }" @input="addModal.errors.email = ''" />
                        <span v-if="addModal.errors.email" class="text-xs text-red-500">{{ addModal.errors.email
                            }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Password <span
                                class="text-red-500">*</span></label>
                        <input v-model="addModal.form.password" type="password" placeholder="Min. 8 characters"
                            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition"
                            :class="{ 'border-red-400': addModal.errors.password }"
                            @input="addModal.errors.password = ''" />
                        <span v-if="addModal.errors.password" class="text-xs text-red-500">{{ addModal.errors.password
                            }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Role <span
                                class="text-red-500">*</span></label>
                        <select v-model="addModal.form.role"
                            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition"
                            :class="{ 'border-red-400': addModal.errors.role }" @change="addModal.errors.role = ''">
                            <option value="">Select role...</option>
                            <option value="admin">Admin</option>
                            <option value="approver">Approver</option>
                            <option value="staff">Staff</option>
                        </select>
                        <span v-if="addModal.errors.role" class="text-xs text-red-500">{{ addModal.errors.role }}</span>
                    </div>

                    <div v-if="addModal.errors.general"
                        class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                        {{ addModal.errors.general }}
                    </div>

                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="handleAddUser" :disabled="addModal.loading"
                        class="flex-1 bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white text-sm font-bold uppercase tracking-widest py-3 transition">
                        {{ addModal.loading ? 'Adding...' : 'Add User' }}
                    </button>
                    <button @click="closeAddModal"
                        class="flex-1 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-bold uppercase tracking-widest py-3 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Permissions Modal -->
        <div v-if="editModal.open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
            @click.self="closeEditModal">
            <div class="bg-white border border-slate-800 w-full max-w-3xl px-8 py-7">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-1">Edit Permissions</h2>
                <p class="text-xs text-slate-500 mb-6">{{ editModal.user?.name }} — {{ editModal.user?.email }}</p>

                <div class="grid grid-cols-2 gap-2">
                    <label v-for="permission in allPermissions" :key="permission"
                        class="flex items-center gap-3 px-4 py-3 border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" :value="permission" v-model="editModal.selectedPermissions"
                            class="w-4 h-4 accent-slate-800" />
                        <span class="text-sm text-slate-700">{{ permission }}</span>
                    </label>
                </div>

                <div v-if="editModal.errors.general"
                    class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                    {{ editModal.errors.general }}
                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="handleSavePermissions" :disabled="editModal.loading"
                        class="flex-1 bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white text-sm font-bold uppercase tracking-widest py-3 transition">
                        {{ editModal.loading ? 'Saving...' : 'Save Permissions' }}
                    </button>
                    <button @click="closeEditModal"
                        class="flex-1 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-bold uppercase tracking-widest py-3 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="deleteModal.open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
            @click.self="closeDeleteModal">
            <div class="bg-white border border-slate-800 w-full max-w-sm px-8 py-7">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-2">Delete User</h2>
                <p class="text-sm text-slate-600 mb-6">
                    Are you sure you want to delete <strong>{{ deleteModal.user?.name }}</strong>? This action cannot be
                    undone.
                </p>
                <div v-if="deleteModal.error"
                    class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                    {{ deleteModal.error }}
                </div>
                <div class="flex gap-3">
                    <button @click="handleDeleteUser" :disabled="deleteModal.loading"
                        class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white text-sm font-bold uppercase tracking-widest py-3 transition">
                        {{ deleteModal.loading ? 'Deleting...' : 'Delete' }}
                    </button>
                    <button @click="closeDeleteModal"
                        class="flex-1 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-bold uppercase tracking-widest py-3 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import api from '@/api/axios'

// Dummy data — replace with real API call later
const users = ref([])

async function fetchUsers() {
    try {
        const response = await api.get('/users')
        users.value = response.data
    } catch (error) {
        console.error('Failed to load users', error)
        users.value = []
    }
}

onMounted(fetchUsers)

// Full permissions list — update when you finalize them
const allPermissions = [
    'create-purchase-request',
    'view-own-purchase-request',
    'view-all-purchase-requests',
    'submit-purchase-request',
    'approve-purchase-request',
    'reject-purchase-request',
    'manage-purchase-orders',
    'manage-invoices',
    'view-purchase-orders',
    'view-invoices',
    'manage-users',
    'manage-roles',
    'view-suppliers',
    'manage-suppliers',
]

// ── Role Change ──────────────────────────────────────────
async function handleRoleChange(user) {
    try {
        const response = await api.patch(`/users/${user.id}/role`, { role: user.role })
        Object.assign(user, response.data)
        console.log(`Role updated: ${user.name} → ${user.role}`)
    } catch (error) {
        console.error('Failed to update role', error)
        await fetchUsers()
    }
}

// ── Add User Modal ───────────────────────────────────────
const addModal = reactive({
    open: false, loading: false,
    form: { name: '', email: '', password: '', role: '' },
    errors: { name: '', email: '', password: '', role: '', general: '' },
})

function openAddModal() {
    addModal.form = { name: '', email: '', password: '', role: '' }
    addModal.errors = { name: '', email: '', password: '', role: '', general: '' }
    addModal.open = true
}
function closeAddModal() { if (!addModal.loading) addModal.open = false }

function validateAdd() {
    let valid = true
    addModal.errors = { name: '', email: '', password: '', role: '', general: '' }
    if (!addModal.form.name.trim()) { addModal.errors.name = 'Name is required.'; valid = false }
    if (!addModal.form.email.trim()) { addModal.errors.email = 'Email is required.'; valid = false }
    else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(addModal.form.email)) { addModal.errors.email = 'Must be a valid email.'; valid = false }
    if (!addModal.form.password) { addModal.errors.password = 'Password is required.'; valid = false }
    else if (addModal.form.password.length < 8) { addModal.errors.password = 'Min. 8 characters.'; valid = false }
    if (!addModal.form.role) { addModal.errors.role = 'Role is required.'; valid = false }
    return valid
}

async function handleAddUser() {
    if (!validateAdd()) return
    addModal.loading = true
    try {
        const response = await api.post('/users', addModal.form)
        users.value.push(response.data)
        users.value.sort((a, b) => (a?.id ?? 0) - (b?.id ?? 0))
        addModal.open = false
    } catch (error) {
        addModal.errors.general = 'Failed to add user. Please try again.'
    } finally {
        addModal.loading = false
    }
}

// ── Edit Permissions Modal ───────────────────────────────
const editModal = reactive({
    open: false, loading: false,
    user: null, selectedPermissions: [],
    errors: { general: '' },
})

function openEditModal(user) {
    editModal.user = user
    editModal.selectedPermissions = [...user.permissions]
    editModal.errors.general = ''
    editModal.open = true
}
function closeEditModal() { if (!editModal.loading) editModal.open = false }

async function handleSavePermissions() {
    editModal.loading = true
    editModal.errors.general = ''
    try {
        const plainPermissions = [...editModal.selectedPermissions] // convert Proxy to plain array
        const response = await api.patch(`/users/${editModal.user.id}/permissions`, { 
            permissions: plainPermissions 
        })
        const target = users.value.find(u => u.id === editModal.user.id)
        if (target) target.permissions = response.data.permissions || plainPermissions
        editModal.open = false
    } catch (error) {
        editModal.errors.general = 'Failed to save permissions. Please try again.'
    } finally {
        editModal.loading = false
    }
}

// ── Delete Modal ─────────────────────────────────────────
const deleteModal = reactive({
    open: false, loading: false,
    user: null, error: '',
})

function openDeleteConfirm(user) { deleteModal.user = user; deleteModal.error = ''; deleteModal.open = true }
function closeDeleteModal() { if (!deleteModal.loading) deleteModal.open = false }

async function handleDeleteUser() {
    deleteModal.loading = true
    deleteModal.error = ''
    try {
        await api.delete(`/users/${deleteModal.user.id}`)
        users.value = users.value.filter(u => u.id !== deleteModal.user.id)
        deleteModal.open = false
    } catch (error) {
        deleteModal.error = 'Failed to delete user. Please try again.'
    } finally {
        deleteModal.loading = false
    }
}
</script>
