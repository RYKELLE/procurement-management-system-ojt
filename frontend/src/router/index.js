// frontend/src/router/index.js

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/layouts/AppLayout.vue'

const routes = [

  // PUBLIC — no sidebar
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/pages/Login.vue'),
    meta: { public: true }
  },

  // AUTHENTICATED — all wrapped in AppLayout (sidebar included)
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [

      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('@/pages/Dashboard.vue')
      },

      // ADMIN
      {
        path: 'admin/users',
        name: 'Users',
        component: () => import('@/pages/admin/Users.vue'),
        meta: { permission: 'manage-users' }
      },
      {
        path: 'admin/roles',
        name: 'Roles',
        component: () => import('@/pages/admin/Roles.vue'),
        meta: { permission: 'manage-roles' }
      },

      // REQUESTER
      {
        path: 'purchase-requests',
        name: 'MyRequests',
        component: () => import('@/pages/purchase-requests/MyRequests.vue'),
        meta: { permission: 'view-own-purchase-request' }
      },
      {
        path: 'purchase-requests/create',
        name: 'CreateRequest',
        component: () => import('@/pages/purchase-requests/CreateRequest.vue'),
        meta: { permission: 'create-purchase-request' }
      },
      {
        path: 'purchase-requests/:id',
        name: 'ViewRequest',
        component: () => import('@/pages/purchase-requests/ViewRequest.vue'),
        meta: { permission: 'view-own-purchase-request' }
      },

      // APPROVER
      {
        path: 'approvals',
        name: 'PendingApprovals',
        component: () => import('@/pages/approvals/PendingApprovals.vue'),
        meta: { permission: 'approve-purchase-request' }
      },

      // PURCHASE ORDERS
      {
        path: 'purchase-orders',
        name: 'PurchaseOrders',
        component: () => import('@/pages/purchase-orders/PurchaseOrders.vue'),
        meta: { permission: 'view-purchase-orders' }
      },

      {
        path: 'purchase-orders/:id',
        name: 'OrderDetail',
        component: () => import('@/pages/purchase-orders/OrderDetail.vue')
      },

      // INVOICES
      {
        path: 'invoices',
        name: 'Invoices',
        component: () => import('@/pages/invoices/Invoices.vue'),
        meta: { permission: 'view-invoices' }
      },
      {
        path: 'invoices/:id',
        name: 'InvoiceDetail',
        component: () => import('@/pages/invoices/InvoiceDetail.vue')
      },

      // SUPPLIERS
      {
        path: 'suppliers',
        name: 'Suppliers',
        component: () => import('@/pages/suppliers/Suppliers.vue'),
        meta: { keepAlive: true, permission: 'view-suppliers' }
      },
    ]
  },

  // Redirects
  { path: '/', redirect: '/login' },
  { path: '/:pathMatch(.*)*', redirect: '/login' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isLoggedIn) return next('/login')
  if (to.meta.public && auth.isLoggedIn) return next('/dashboard')
  if (to.meta.permission && !auth.hasPermission(to.meta.permission)) {
    const payload = { type: 'error', message: 'Access Denied' }
    sessionStorage.setItem('flash', JSON.stringify(payload))
    window.dispatchEvent(new CustomEvent('app:flash', { detail: payload }))

    // If user is already somewhere in the app, keep them on the same page.
    // If this is the initial load (no "from" route yet), fall back to dashboard.
    if (from?.name) return next(false)
    return next('/dashboard')
  }

  next()
})

export default router
