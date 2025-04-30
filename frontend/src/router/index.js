import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../components/Dashboard.vue'
import Expenses from '../views/Expenses.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import { useUserStore } from '../stores/userStore'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    beforeEnter: async (to, from, next) => {
      const userStore = (await import('@/stores/userStore')).useUserStore()
      // console.log(userStore.isLoggedIn)
      if (userStore.isLoggedIn) {
        next()
      } else {
        next('/login')
      }
    },
    component: Dashboard,
  },
  {
    path: '/expenses',
    name: 'Expenses',
    beforeEnter: async (to, from, next) => {
      const userStore = (await import('@/stores/userStore')).useUserStore()
      if (userStore.isLoggedIn) {
        next()
      } else {
        next('/login')
      }
    },
    component: Expenses,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/logout',
    name: 'Logout',
    beforeEnter: async (to, from, next) => {
      const userStore = (await import('@/stores/userStore')).useUserStore()
      await userStore.logoutUser()
      next('/login')
    },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  linkActiveClass: 'router-link-active',
})

export default router
