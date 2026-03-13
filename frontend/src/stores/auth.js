import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'

function safeParse(key, fallback) {
  try {
    const val = localStorage.getItem(key)
    if (!val || val === 'undefined' || val === 'null') return fallback
    return JSON.parse(val)
  } catch {
    return fallback
  }
}

export const useAuthStore = defineStore('auth', () => {

  const token = ref(localStorage.getItem('token') || null)
  const user = ref(safeParse('user', null))
  const permissions = ref(safeParse('permissions', []))

  const isLoggedIn = computed(() => !!token.value)

  function setAuth(newToken, newUser, newPermissions) {
    token.value = newToken
    user.value = newUser
    permissions.value = newPermissions

    localStorage.setItem('token', newToken)
    localStorage.setItem('user', JSON.stringify(newUser))
    localStorage.setItem('permissions', JSON.stringify(newPermissions))
  }

  function clearAuth() {
    token.value = null
    user.value = null
    permissions.value = []

    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('permissions')
  }

  function hasPermission(permission) {
    return Array.isArray(permissions.value) && permissions.value.includes(permission)
  }

  async function refreshMe() {
    if (!token.value) return

    const response = await api.get('/me')
    user.value = response.data
    permissions.value = Array.isArray(response.data?.permissions) ? response.data.permissions : []

    localStorage.setItem('user', JSON.stringify(user.value))
    localStorage.setItem('permissions', JSON.stringify(permissions.value))
  }

  return { token, user, permissions, isLoggedIn, setAuth, clearAuth, hasPermission, refreshMe }
})
