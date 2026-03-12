<template>
  <div class="flex flex-col gap-6 h-full">

    <!-- Header -->
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-wide">SUPPLIERS</h1>
        <p class="text-sm text-slate-500 mt-1">Manage supplier information</p>
      </div>
      <button
        @click="openAddModal"
        class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold tracking-widest uppercase px-6 py-4 transition whitespace-nowrap"
      >
        + Add Supplier
      </button>
    </div>

    <!-- Table -->
    <div class="flex flex-col flex-1 bg-white border border-slate-800 min-h-0">
      <div class="flex-1 overflow-y-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-100 border-b border-slate-800 sticky top-0">
            <tr>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-32">Supplier ID</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Name</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs w-44">Contact Number</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Email</th>
              <th class="text-left px-6 py-4 font-bold text-slate-700 uppercase tracking-wide text-xs">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="suppliers.length === 0">
              <td colspan="5" class="text-center py-12 text-slate-400 text-sm">No suppliers found.</td>
            </tr>
            <tr v-for="supplier in suppliers" :key="supplier.id" class="hover:bg-slate-50 transition">
              <td class="px-6 py-4 text-slate-700 font-medium">{{ supplier.supplier_id }}</td>
              <td class="px-6 py-4 text-slate-700">{{ supplier.supplier_name }}</td>
              <td class="px-6 py-4 text-slate-500">{{ supplier.contact }}</td>
              <td class="px-6 py-4 text-slate-500">{{ supplier.email }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button
                    @click="openEditModal(supplier)"
                    class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-50 transition"
                  >
                    Edit
                  </button>
                  <RouterLink
                    :to="`/purchase-orders?supplier_id=${supplier.id}`"
                    class="border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wide px-4 py-1.5 hover:bg-slate-50 transition"
                  >
                    View Orders
                  </RouterLink>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Supplier Modal -->
    <div v-if="addModal.open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" @click.self="closeAddModal">
      <div class="bg-white border border-slate-800 w-full max-w-md px-8 py-7">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-6">Add New Supplier</h2>
        <div class="flex flex-col gap-4">

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Supplier Name <span class="text-red-500">*</span></label>
            <input v-model="addModal.form.supplier_name" type="text" placeholder="Company name"
              class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition"
              :class="{ 'border-red-400': addModal.errors.supplier_name }"
              @input="addModal.errors.supplier_name = ''" />
            <span v-if="addModal.errors.supplier_name" class="text-xs text-red-500">{{ addModal.errors.supplier_name }}</span>
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Contact Number</label>
            <input v-model="addModal.form.contact" type="text" placeholder="e.g. +1 (555) 123-4567"
              class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</label>
            <input v-model="addModal.form.email" type="email" placeholder="supplier@email.com"
              class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition"
              :class="{ 'border-red-400': addModal.errors.email }"
              @input="addModal.errors.email = ''" />
            <span v-if="addModal.errors.email" class="text-xs text-red-500">{{ addModal.errors.email }}</span>
          </div>

          <div v-if="addModal.errors.general" class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
            {{ addModal.errors.general }}
          </div>

        </div>
        <div class="flex gap-3 mt-6">
          <button @click="handleAddSupplier" :disabled="addModal.loading"
            class="flex-1 bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white text-sm font-bold uppercase tracking-widest py-3 transition">
            {{ addModal.loading ? 'Adding...' : 'Add Supplier' }}
          </button>
          <button @click="closeAddModal"
            class="flex-1 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-bold uppercase tracking-widest py-3 transition">
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div v-if="editModal.open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" @click.self="closeEditModal">
      <div class="bg-white border border-slate-800 w-full max-w-md px-8 py-7">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-1">Edit Supplier</h2>
        <p class="text-xs text-slate-500 mb-6">{{ editModal.form.supplier_name }}</p>
        <div class="flex flex-col gap-4">

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Contact Number</label>
            <input v-model="editModal.form.contact" type="text" placeholder="e.g. +1 (555) 123-4567"
              class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</label>
            <input v-model="editModal.form.email" type="email" placeholder="supplier@email.com"
              class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 transition"
              :class="{ 'border-red-400': editModal.errors.email }"
              @input="editModal.errors.email = ''" />
            <span v-if="editModal.errors.email" class="text-xs text-red-500">{{ editModal.errors.email }}</span>
          </div>

          <div v-if="editModal.errors.general" class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
            {{ editModal.errors.general }}
          </div>

        </div>
        <div class="flex gap-3 mt-6">
          <button @click="handleEditSupplier" :disabled="editModal.loading"
            class="flex-1 bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white text-sm font-bold uppercase tracking-widest py-3 transition">
            {{ editModal.loading ? 'Saving...' : 'Save Changes' }}
          </button>
          <button @click="closeEditModal"
            class="flex-1 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-bold uppercase tracking-widest py-3 transition">
            Cancel
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'

// Dummy data — replace with real API call later
const suppliers = ref([
  { id: 1, supplier_id: 'SUP-001', supplier_name: 'ABC Office Supplies', contact: '+1 (555) 123-4567', email: 'john@abcoffice.com' },
  { id: 2, supplier_id: 'SUP-002', supplier_name: 'TechCorp Inc',         contact: '+1 (555) 234-5678', email: 'sarah@techcorp.com' },
  { id: 3, supplier_id: 'SUP-003', supplier_name: 'Global Traders',       contact: '+1 (555) 345-6789', email: 'mike@globaltraders.com' },
  { id: 4, supplier_id: 'SUP-004', supplier_name: 'Office Depot Pro',     contact: '+1 (555) 456-7890', email: 'lisa@officedepot.com' },
  { id: 5, supplier_id: 'SUP-005', supplier_name: 'Stationery World',     contact: '+1 (555) 567-8901', email: 'david@stationeryworld.com' },
  { id: 6, supplier_id: 'SUP-006', supplier_name: 'Premium Supplies Co',  contact: '+1 (555) 678-9012', email: 'emma@premiumsupplies.com' },
])

// ── Add Modal ────────────────────────────────────────────
const addModal = reactive({
  open: false, loading: false,
  form: { supplier_name: '', contact: '', email: '' },
  errors: { supplier_name: '', email: '', general: '' },
})

function openAddModal() {
  addModal.form = { supplier_name: '', contact: '', email: '' }
  addModal.errors = { supplier_name: '', email: '', general: '' }
  addModal.open = true
}
function closeAddModal() { if (!addModal.loading) addModal.open = false }

function validateAdd() {
  let valid = true
  addModal.errors = { supplier_name: '', email: '', general: '' }
  if (!addModal.form.supplier_name.trim()) { addModal.errors.supplier_name = 'Supplier name is required.'; valid = false }
  if (addModal.form.email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(addModal.form.email)) {
    addModal.errors.email = 'Must be a valid email.'; valid = false
  }
  return valid
}

async function handleAddSupplier() {
  if (!validateAdd()) return
  addModal.loading = true
  try {
    // TODO: const response = await api.post('/suppliers', addModal.form)
    await new Promise(r => setTimeout(r, 600))
    const newId = suppliers.value.length + 1
    suppliers.value.push({
      id: newId,
      supplier_id: `SUP-${String(newId).padStart(3, '0')}`,
      supplier_name: addModal.form.supplier_name,
      contact: addModal.form.contact,
      email: addModal.form.email,
    })
    addModal.open = false
  } catch (error) {
    addModal.errors.general = 'Failed to add supplier. Please try again.'
  } finally {
    addModal.loading = false
  }
}

// ── Edit Modal ───────────────────────────────────────────
const editModal = reactive({
  open: false, loading: false,
  supplierId: null,
  form: { supplier_name: '', contact: '', email: '' },
  errors: { email: '', general: '' },
})

function openEditModal(supplier) {
  editModal.supplierId = supplier.id
  editModal.form = { supplier_name: supplier.supplier_name, contact: supplier.contact, email: supplier.email }
  editModal.errors = { email: '', general: '' }
  editModal.open = true
}
function closeEditModal() { if (!editModal.loading) editModal.open = false }

function validateEdit() {
  editModal.errors = { email: '', general: '' }
  if (editModal.form.email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(editModal.form.email)) {
    editModal.errors.email = 'Must be a valid email.'
    return false
  }
  return true
}

async function handleEditSupplier() {
  if (!validateEdit()) return
  editModal.loading = true
  try {
    // TODO: await api.patch(`/suppliers/${editModal.supplierId}`, { contact: editModal.form.contact, email: editModal.form.email })
    await new Promise(r => setTimeout(r, 600))
    const target = suppliers.value.find(s => s.id === editModal.supplierId)
    if (target) {
      target.contact = editModal.form.contact
      target.email = editModal.form.email
    }
    editModal.open = false
  } catch (error) {
    editModal.errors.general = 'Failed to save changes. Please try again.'
  } finally {
    editModal.loading = false
  }
}
</script>