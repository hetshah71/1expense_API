import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// Create the Pinia store
const pinia = createPinia()

// Create the Vue application
const app = createApp(App)

// Use plugins
app.use(pinia)
app.use(router)

// Mount the application
app.mount('#app')
