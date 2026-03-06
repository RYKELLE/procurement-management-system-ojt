<template>
  <div class="login-page">
    <div class="login-card">

      <h1 class="title">Procurement Management System</h1>
      <p class="subtitle">Sign in to your account</p>

      <form @submit.prevent="handleLogin" class="form">

        <div v-if="errorMessage" class="error-banner">
          {{ errorMessage }}
        </div>

        <div class="field">
          <label>Username</label>
          <input
            v-model="form.username"
            type="text"
            placeholder="Enter your username"
            autocomplete="username"
          />
          <span v-if="errors.username" class="field-error">{{ errors.username }}</span>
        </div>

        <div class="field">
          <label>Password</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Enter your password"
            autocomplete="current-password"
          />
          <span v-if="errors.password" class="field-error">{{ errors.password }}</span>
        </div>

        <button type="submit" :disabled="loading">
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

const form = reactive({ username: '', password: '' })
const loading = ref(false)
const errorMessage = ref('')
const errors = reactive({ username: '', password: '' })

function validate() {
  let valid = true
  errors.username = ''
  errors.password = ''

  if (!form.username) { errors.username = 'Username is required.'; valid = false }
  if (!form.password) { errors.password = 'Password is required.'; valid = false }

  return valid
}

async function handleLogin() {
  errorMessage.value = ''
  if (!validate()) return

  loading.value = true
  try {
    const response = await api.post('/login', {
      username: form.username,
      password: form.password
    })

    const { token, user, permissions } = response.data
    auth.setAuth(token, user, permissions)

    if (user.role === 'Admin') router.push('/dashboard')
    else if (user.role === 'Approver') router.push('/approvals')
    else router.push('/purchase-requests')

  } catch (error) {
    if (error.response?.status === 401) {
      errorMessage.value = 'Invalid username or password.'
    } else {
      errorMessage.value = 'Something went wrong. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Modern Neutral Palette (Zinc/Slate) */
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #fafafa; /* Very light neutral gray */
  font-family: 'Inter', -apple-system, sans-serif;
  padding: 20px;
}

.login-card {
  background: #ffffff;
  padding: 40px;
  border-radius: 12px;
  /* Multi-layered soft shadow for a "lifted" feel */
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  border: 1px solid #e4e4e7;
  width: 100%;
  max-width: 400px;
}

.title {
  font-size: 24px;
  font-weight: 600;
  color: #09090b; /* Near black */
  text-align: center;
  letter-spacing: -0.02em;
  margin-bottom: 8px;
}

.subtitle {
  font-size: 14px;
  color: #71717a;
  text-align: center;
  margin-bottom: 32px;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.error-banner {
  background: #fef2f2;
  border: 1px solid #fee2e2;
  color: #991b1b;
  padding: 12px;
  border-radius: 8px;
  font-size: 13px;
  text-align: center;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.field label {
  font-size: 14px;
  font-weight: 500;
  color: #27272a;
}

.field input {
  padding: 10px 12px;
  border: 1px solid #e4e4e7;
  border-radius: 6px;
  font-size: 14px;
  color: #18181b; /* Fixed: dark text on light bg */
  background-color: #ffffff;
  transition: all 0.2s ease;
}

/* Enhanced Focus State */
.field input:focus {
  outline: none;
  border-color: #18181b;
  box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.05);
}

.field-error {
  font-size: 12px;
  color: #ef4444;
  margin-top: 2px;
}

button[type="submit"] {
  margin-top: 4px;
  padding: 12px;
  background: #18181b; /* Neutral dark button */
  color: #ffffff;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

button[type="submit"]:hover:not(:disabled) {
  background: #27272a;
}

button[type="submit"]:active {
  transform: scale(0.98);
}

button[type="submit"]:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>