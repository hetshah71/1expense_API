<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2 class="auth-title">Register</h2>
      <form @submit.prevent="handleRegister" class="auth-form">
        <InputField
          id="name"
          label="Name"
          type="text"
          placeholder="Enter your name"
          v-model="formdata.name"
          required
        />
        <InputField
          id="email"
          label="Email"
          type="email"
          placeholder="Enter your email"
          v-model="formdata.email"
          required
        />
        <InputField
          id="password"
          label="Password"
          type="password"
          placeholder="Enter your password"
          v-model="formdata.password"
          required
        />
        <InputField
          id="password_confirmation"
          label="Confirm Password"
          type="password"
          placeholder="Confirm your password"
          v-model="formdata.password_confirmation"
          required
        />
        <div class="form-actions">
          <button type="submit" class="btn-primary">Register</button>
          <router-link to="/login" class="auth-link">Already have an account? Login</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import {useUserStore} from '@/stores/userStore'
import InputField from '../components/Shared/InputField.vue';

const router = useRouter();
const userstore = useUserStore()
const formdata= reactive({
  name: '',
  email: '',
  password: '', 
  password_confirmation: ''
})

const handleRegister = async () => {
  try{
    await userstore.registeruser(formdata)
    router.push('/login')    
  }
  catch(error){
    console.log(error) 
  }
};
</script>

<style scoped>
.auth-container {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background-color: #f3f4f6;
}

.auth-card {
  background-color: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  width: 100%;
  max-width: 400px;
}

.auth-title {
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.auth-link {
  text-align: center;
  color: #3b82f6;
  text-decoration: none;
  font-size: 0.875rem;
}

.auth-link:hover {
  text-decoration: underline;
}
</style>