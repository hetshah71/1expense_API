<template>
  <div class="app-container">
    <header class="app-header">
      <div class="header-content">
        <router-link to="/" class="app-logo">
          <h1>💰 Expense Tracker</h1>
        </router-link>
        
        <nav class="app-nav">
          <router-link v-if="isLoggedIn" to="/" class="nav-link">Dashboard</router-link>
          <router-link v-if="isLoggedIn" to="/expenses" class="nav-link">Expenses</router-link>
          <router-link v-if="isLoggedIn===false" to="/login" class="nav-link">Login</router-link>
          <router-link v-if="isLoggedIn===false" to="/register" class="nav-link">Register</router-link>
          <router-link v-if="isLoggedIn" to="/logout" class="nav-link">Logout</router-link>
        </nav>
      </div>
    </header>
    
    <main class="app-main">
      <router-view />
    </main>
    
    <footer class="app-footer">
      <div class="footer-content">
        <p>© {{ currentYear }} Expense Tracker. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useUserStore } from '@/stores/userStore';

const userStore = useUserStore();
const isLoggedIn = computed(() => userStore.isLoggedIn);
console.log('isLoggedIn:', isLoggedIn.value); // Debugging line to check the value of isLoggedIn
const currentYear = computed(() => new Date().getFullYear());
</script>

<style scoped>
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.app-header {
  background-color: #1f2937;
  color: white;
  padding: 1rem 0;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.app-logo {
  text-decoration: none;
  color: white;
  font-weight: 700;
}

.app-nav {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  color: #e5e7eb;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 0;
  position: relative;
}

.nav-link:hover {
  color: white;
}

.nav-link.router-link-active {
  color: white;
}

.nav-link.router-link-active:after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 2px;
  background-color: #3b82f6;
}

.app-main {
  flex: 1;
  background-color: #f3f4f6;
}

.app-footer {
  background-color: #1f2937;
  color: #9ca3af;
  padding: 1.5rem 0;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  text-align: center;
}
</style>