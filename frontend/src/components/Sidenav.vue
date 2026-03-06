<template>
  <div class="sidebar">

    <!-- Logo / Title -->
    <div class="sidebar-header">
      <div class="brand-title">PROCUREMENT</div>
      <div class="brand-sub">Management System</div>
    </div>

    <!-- Nav Links -->
    <nav class="sidebar-nav">
      <RouterLink to="/dashboard" class="nav-item" active-class="nav-item--active">
        Dashboard
      </RouterLink>
      <RouterLink to="/purchase-requests" class="nav-item" active-class="nav-item--active">
        Purchase Requests
      </RouterLink>
      <RouterLink to="/purchase-orders" class="nav-item" active-class="nav-item--active">
        Purchase Orders
      </RouterLink>
      <RouterLink to="/invoices" class="nav-item" active-class="nav-item--active">
        Invoices
      </RouterLink>
      <RouterLink to="/suppliers" class="nav-item" active-class="nav-item--active">
        Suppliers
      </RouterLink>
      <RouterLink to="/admin/users" class="nav-item" active-class="nav-item--active">
        Users & Roles
      </RouterLink>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
      <div class="logged-in-box">
        <div class="logged-in-label">LOGGED IN AS</div>
        <div class="logged-in-name">{{ auth.user?.name || 'User' }}</div>
      </div>
      <button class="logout-btn" @click="handleLogout">LOGOUT</button>
    </div>

  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const router = useRouter()
const auth = useAuthStore()

async function handleLogout() {
  try {
    await api.post('/logout')
  } catch (e) {
    // Continue logout even if API call fails
  } finally {
    auth.clearAuth()
    router.push('/login')
  }
}
</script>

<style scoped>
.sidebar {
  width: 260px;
  min-height: 100vh;
  background: #ffffff;
  border-right: 1px solid #d1d5db;
  display: flex;
  flex-direction: column;
  font-family: 'Segoe UI', sans-serif;
}

/* Header */
.sidebar-header {
  padding: 24px 20px;
  border-bottom: 1px solid #d1d5db;
}

.brand-title {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  letter-spacing: 0.02em;
}

.brand-sub {
  font-size: 13px;
  color: #64748b;
  margin-top: 2px;
}

/* Nav */
.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.nav-item {
  display: block;
  padding: 12px 16px;
  font-size: 14px;
  color: #374151;
  text-decoration: none;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  transition: background 0.15s;
}

.nav-item:hover {
  background: #f1f5f9;
}

.nav-item--active {
  background: #e2e8f0;
  font-weight: 600;
  color: #1e293b;
}

/* Footer */
.sidebar-footer {
  padding: 16px 12px;
  border-top: 1px solid #d1d5db;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.logged-in-box {
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 10px 14px;
}

.logged-in-label {
  font-size: 10px;
  color: #94a3b8;
  letter-spacing: 0.1em;
  font-weight: 600;
  margin-bottom: 4px;
}

.logged-in-name {
  font-size: 14px;
  color: #1e293b;
  font-weight: 500;
}

.logout-btn {
  width: 100%;
  padding: 11px;
  background: #ffffff;
  color: #1e293b;
  border: 1px solid #1e293b;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.08em;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}

.logout-btn:hover {
  background: #1e293b;
  color: #ffffff;
}
</style>