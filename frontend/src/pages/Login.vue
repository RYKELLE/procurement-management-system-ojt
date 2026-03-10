<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100">
    <div class="bg-white w-full max-w-sm px-10 py-12 shadow-sm">

      <h1 class="text-xl font-bold text-slate-800 text-center mb-1">
        Procurement Management System
      </h1>
      <p class="text-sm text-slate-500 text-center mb-8">Sign in to your account</p>

      <form @submit.prevent="handleLogin" class="flex flex-col gap-5">

        <!-- Error Banner -->
        <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded">
          {{ errorMessage }}
        </div>

        <!-- Email -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="Enter your email"
            autocomplete="email"
            class="border border-slate-300 rounded px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-blue-500 transition"
            :class="{ 'border-red-400': errors.email }"
            @input="errors.email = ''"
          />
          <span v-if="errors.email" class="text-xs text-red-500">{{ errors.email }}</span>
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Password</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Enter your password"
            autocomplete="current-password"
            class="border border-slate-300 rounded px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-blue-500 transition"
            :class="{ 'border-red-400': errors.password }"
            @input="errors.password = ''"
          />
          <span v-if="errors.password" class="text-xs text-red-500">{{ errors.password }}</span>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="mt-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold text-sm py-2.5 rounded transition"
        >
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ email: '', password: '' })
const loading = ref(false)
const errorMessage = ref('')
const errors = reactive({ email: '', password: '' })

function validate() {
  let valid = true
  errors.email = ''
  errors.password = ''

  if (!form.email) {
    errors.email = 'Email is required.'
    valid = false
  } else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(form.email)) {
    errors.email = 'Email must be valid.'
    valid = false
  }

  if (!form.password) {
    errors.password = 'Password is required.'
    valid = false
  }

  return valid
}

async function handleLogin() {
  errorMessage.value = ''
  if (!validate()) return

  loading.value = true
  try {
    const response = await api.post('/login', {
      email: form.email,
      password: form.password
    })

    const { token, user, permissions } = response.data
    auth.setAuth(token, user, permissions)

    const role = (user.role || '').toLowerCase()
    if (role === 'admin') router.push('/dashboard')
    else if (role === 'approver') router.push('/approvals')
    else router.push('/purchase-requests')

  } catch (error) {
    if (error.response?.status === 401) {
      errorMessage.value = 'Invalid email or password.'
    } else {
      errorMessage.value = 'Something went wrong. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>