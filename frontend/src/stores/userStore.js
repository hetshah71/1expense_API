import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api.js'
import router from '@/router/index.js'
export const useUserStore = defineStore('user', () => {
  const user = ref(null)
  const token = localStorage.getItem('token') || null
  const isLoggedIn = ref(!!token)

  async function registeruser(userData) {
    isLoggedIn.value = true
    const response = await api.post('/register', { ...userData })
    user.value = response.data.data.user
    localStorage.setItem('token', response.data.data.token)
  }
  async function loginuser(userData) {
    try {
      const response = await api.post('/login', { ...userData })
      if (response.data.data && response.data.data.token) {
        localStorage.setItem('token', response.data.data.token)
        user.value = response.data.data.user
        isLoggedIn.value = true
        return true
      }
      return false
    } catch (error) {
      console.error('Login error:', error)
      throw error
    }
  }
  async function logoutUser() {
    try {
      const response = await api.post('/logout')
      // console.log(response.data);
      if (response.data.data) {
        localStorage.removeItem('token')
        isLoggedIn.value = false
        user.value = null
        await router.push('/login')
      }
    } catch (error) {
      console.error('Error during logout:', error)
      throw error
    }
  }
  return {
    user,
    token,
    isLoggedIn,
    registeruser,
    loginuser,
    logoutUser,
  }
})
