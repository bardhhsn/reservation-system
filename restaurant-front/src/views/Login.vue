<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
      <div class="text-center mb-8">
        <img src="/placeholder.svg?height=48&width=123" alt="Porto Restaurant" class="mx-auto mb-4" />
        <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
        <p class="text-gray-600">Sign in to your account</p>
      </div>

      <form @submit.prevent="login">
        <!-- Email Field -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1 text-gray-700">Email</label>
          <input
            v-model="form.email"
            type="email"
            :disabled="isLoading"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
            placeholder="Enter your email"
          />
          <p v-if="errors.email" class="text-red-600 text-sm mt-1">
            {{ errors.email[0] }}
          </p>
        </div>

        <!-- Password Field -->
        <div class="mb-6">
          <label class="block text-sm font-medium mb-1 text-gray-700">Password</label>
          <input
            v-model="form.password"
            type="password"
            :disabled="isLoading"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
            placeholder="Enter your password"
          />
          <p v-if="errors.password" class="text-red-600 text-sm mt-1">
            {{ errors.password[0] }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center"
        >
          <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isLoading ? 'Signing In...' : 'Sign In' }}
        </button>

        <p v-if="generalError" class="text-red-600 text-center mt-4">
          {{ generalError }}
        </p>
      </form>

      <div class="mt-6 text-center">
        <p class="text-gray-600">
          Don't have an account? 
          <router-link to="/register" class="text-blue-600 hover:text-blue-700 font-medium">Sign up</router-link>
        </p>
        <router-link to="/" class="text-gray-500 hover:text-gray-700 text-sm">← Back to Home</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'LoginPage',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      errors: {},
      generalError: '',
      isLoading: false
    }
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  methods: {
    async login() {
      this.errors = {}
      this.generalError = ''
      this.isLoading = true

      try {
        const { data } = await axios.post('/api/login', this.form)

        // Save token & user
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))

        // Set default auth header for axios
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`

        // Emit custom event to update all components
        window.dispatchEvent(new CustomEvent('authStateChanged'))

        // Show success message
        this.toast.success(`Welcome back, ${data.user.name}!`, {
          timeout: 3000
        })

        // Check if there's a redirect destination
        const redirectPath = sessionStorage.getItem('redirectAfterLogin')
        sessionStorage.removeItem('redirectAfterLogin')
        
        // Small delay to show the success message before redirect
        setTimeout(() => {
          this.$router.push(redirectPath || '/booking')
        }, 500)

      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {}
        } else {
          this.generalError = error.response?.data?.message || 'Login failed. Please try again.'
          this.toast.error(this.generalError)
        }
      } finally {
        this.isLoading = false
      }
    }
  }
}
</script>

