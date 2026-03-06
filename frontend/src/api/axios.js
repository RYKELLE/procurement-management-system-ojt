//
// This file sets up Axios — the tool Vue uses to talk to your Laravel backend.
//
// Without this file, you'd have to manually add your login token
// to EVERY single API request. That's tedious and easy to forget.
//
// With this file, the token is attached AUTOMATICALLY to every request.
// Think of it like a stamp machine — every letter that goes out
// gets your "I am logged in" stamp without you having to do it manually.

import axios from 'axios'

// Create a custom Axios instance pointed at your backend
const api = axios.create({
  baseURL: '/api', // All requests will start with /api (Nginx routes this to Laravel)
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
})

// --- REQUEST INTERCEPTOR ---
// This runs before EVERY request you send.
// It reads the token from localStorage and attaches it.
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')

  if (token) {
    // This is how Laravel Sanctum expects the token
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

// --- RESPONSE INTERCEPTOR ---
// This runs after EVERY response comes back from the backend.
// If the backend says 401 (Unauthorized = "you're not logged in"),
// we clear the token and redirect to login.
api.interceptors.response.use(
  (response) => response, // If successful, just return the response normally

  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid — clear everything and go to login
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('permissions')
      window.location.href = '/login'
    }

    return Promise.reject(error)
  }
)

export default api