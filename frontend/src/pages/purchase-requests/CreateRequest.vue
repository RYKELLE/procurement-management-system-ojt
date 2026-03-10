<template>
  <div class="flex flex-col gap-6">

    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-wide">NEW PURCHASE REQUEST</h1>
      <p class="text-sm text-slate-500 mt-1">Create a new purchase request</p>
    </div>

    <!-- Request Details -->
    <div class="bg-white border border-slate-800 px-6 py-5">
      <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Request Details</h2>

      <div class="flex flex-col gap-5">

        <!-- Reason -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
            Reason <span class="text-red-500">*</span>
          </label>
          <textarea
            v-model="form.reason"
            placeholder="Enter reason for purchase"
            rows="4"
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-slate-500 resize-y w-full transition"
            :class="{ 'border-red-400': errors.reason }"
            @input="errors.reason = ''"
          />
          <span v-if="errors.reason" class="text-xs text-red-500">{{ errors.reason }}</span>
        </div>

        <!-- Date -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
            Date <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.date"
            type="date"
            disabled
            class="border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-500 w-48 transition bg-slate-50 cursor-not-allowed"
            :class="{ 'border-red-400': errors.date }"
            @input="errors.date = ''"
          />
          <span v-if="errors.date" class="text-xs text-red-500">{{ errors.date }}</span>
        </div>

      </div>
    </div>

    <!-- Line Items -->
    <div class="bg-white border border-slate-800 px-6 py-5">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Line Items</h2>
        <button
          @click="addItem"
          class="border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold uppercase tracking-widest px-4 py-2 transition"
        >
          + Add Item
        </button>
      </div>

      <div class="border border-slate-200">
        <!-- Table Header -->
        <div class="grid grid-cols-12 bg-slate-100 border-b border-slate-200">
          <div class="col-span-5 px-4 py-3 text-xs font-bold text-slate-700 uppercase tracking-wide">Item</div>
          <div class="col-span-3 px-4 py-3 text-xs font-bold text-slate-700 uppercase tracking-wide">Quantity</div>
          <div class="col-span-3 px-4 py-3 text-xs font-bold text-slate-700 uppercase tracking-wide">Unit Price</div>
          <div class="col-span-1 px-4 py-3 text-xs font-bold text-slate-700 uppercase tracking-wide">Actions</div>
        </div>

        <!-- Item Rows -->
        <div
          v-for="(item, index) in form.items"
          :key="item.key"
          class="grid grid-cols-12 border-b border-slate-100 items-center"
        >
          <!-- Item Select -->
          <div class="col-span-5 px-3 py-3">
            <select
              v-model="item.item_id"
              class="w-full border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-500"
              :class="{ 'border-red-400': item.error_item }"
              @change="onItemChange(item)"
            >
              <option value="">Select item...</option>
              <option v-for="opt in itemOptions" :key="opt.id" :value="opt.id">
                {{ opt.item_name }}
              </option>
            </select>
          </div>

          <!-- Quantity -->
          <div class="col-span-3 px-3 py-3">
            <input
              v-model.number="item.quantity"
              type="number"
              min="1"
              class="w-full border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-500"
              :class="{ 'border-red-400': item.error_qty }"
              @input="item.error_qty = ''"
            />
          </div>

          <!-- Unit Price -->
          <div class="col-span-3 px-3 py-3">
            <input
              v-model.number="item.unit_price"
              type="number"
              readonly
              class="w-full border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-500 bg-slate-50 cursor-not-allowed"
              :class="{ 'border-red-400': item.error_price }"
              @input="item.error_price = ''"
            />
          </div>

          <!-- Remove -->
          <div class="col-span-1 px-3 py-3 flex justify-center">
            <button
              @click="removeItem(index)"
              :disabled="form.items.length === 1"
              class="text-slate-400 hover:text-red-500 disabled:opacity-30 disabled:cursor-not-allowed transition text-lg leading-none"
              title="Remove row"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Total Row -->
        <div class="grid grid-cols-12 bg-slate-100">
          <div class="col-span-8 px-4 py-4 text-right text-sm font-bold text-slate-700 uppercase tracking-wide">
            Total:
          </div>
          <div class="col-span-4 px-4 py-4 text-sm font-bold text-slate-800">
            ${{ total.toFixed(2) }}
          </div>
        </div>
      </div>

      <span v-if="errors.items" class="text-xs text-red-500 mt-2 block">{{ errors.items }}</span>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-4 pb-6">
      <button
        @click="handleSubmit"
        :disabled="loading"
        class="bg-slate-800 hover:bg-slate-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-bold uppercase tracking-widest px-10 py-4 transition"
      >
        {{ loading ? 'Submitting...' : 'Submit Request' }}
      </button>
      <RouterLink
        to="/purchase-requests"
        class="border border-slate-800 text-slate-800 hover:bg-slate-100 text-sm font-bold uppercase tracking-widest px-10 py-4 transition"
      >
        Cancel
      </RouterLink>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'

const router = useRouter()
const loading = ref(false)

const today = new Date().toISOString().split('T')[0]

const form = reactive({
  reason: '',
  date: today,
  items: [newItem()],
})

const errors = reactive({
  reason: '',
  date: '',
  items: '',
})

const itemOptions = ref([])

async function fetchItems() {
  try {
    const response = await api.get('/items')
    itemOptions.value = response.data
  } catch (e) {
    errors.items = 'Failed to load items. Please refresh.'
  }
}

function onItemChange(item) {
  item.error_item = ''
  if (item.item_id) {
    const selectedItem = itemOptions.value.find(opt => opt.id == item.item_id)
    if (selectedItem) {
      item.unit_price = selectedItem.item_price
    }
  } else {
    item.unit_price = ''
  }
}

function newItem() {
  return {
    key: Date.now() + Math.random(),
    item_id: '',
    quantity: 1,
    unit_price: '',
    error_item: '',
    error_qty: '',
    error_price: '',
  }
}

function addItem() {
  form.items.push(newItem())
}

function removeItem(index) {
  if (form.items.length > 1) {
    form.items.splice(index, 1)
  }
}

const total = computed(() => {
  return form.items.reduce((sum, item) => {
    const qty = Number(item.quantity) || 0
    const price = Number(item.unit_price) || 0
    return sum + qty * price
  }, 0)
})

function validate() {
  let valid = true
  errors.reason = ''
  errors.date = ''
  errors.items = ''

  if (!form.reason.trim()) {
    errors.reason = 'Reason is required.'
    valid = false
  }

  if (!form.date) {
    errors.date = 'Date is required.'
    valid = false
  }

  let itemValid = true
  form.items.forEach(item => {
    item.error_item = ''
    item.error_qty = ''
    item.error_price = ''

    if (!item.item_id) {
      item.error_item = 'Required'
      itemValid = false
    }
    if (!item.quantity || item.quantity < 1) {
      item.error_qty = 'Required'
      itemValid = false
    }
    if (item.unit_price === '' || item.unit_price === null) {
      item.error_price = 'Required'
      itemValid = false
    }
  })

  if (!itemValid) {
    errors.items = 'Please fill in all line items.'
    valid = false
  }

  return valid
}

async function handleSubmit() {
  if (!validate()) return

  loading.value = true
  try {
    await api.post('/purchase-requests', {
      reason: form.reason,
      items: form.items.map(i => ({
        item_id: i.item_id,
        item_quantity: i.quantity,
        unit_price: i.unit_price,
      }))
    })
    router.push('/purchase-requests')
  } catch (error) {
    errors.items = 'Failed to submit request. Please try again.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchItems)
</script>