<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
      <div class="text-center mb-8">
        <img src="/placeholder.svg?height=48&width=123" alt="Porto Restaurant" class="mx-auto mb-4" />
        <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
        <p class="text-gray-600">Join us for amazing dining experiences</p>
      </div>

      <!-- Success Message -->
      <div v-if="registrationSuccess" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <div>
            <h3 class="text-sm font-medium text-green-800">Registration Successful!</h3>
            <p class="text-sm text-green-700 mt-1">
              A confirmation email has been sent to <strong>{{ form.email }}</strong>. 
              Please check your inbox and follow the instructions to verify your account.
            </p>
          </div>
        </div>
        <div class="mt-4">
          <router-link 
            to="/login" 
            class="text-sm text-green-600 hover:text-green-500 font-medium"
          >
            Go to Login →
          </router-link>
        </div>
      </div>

      <form v-if="!registrationSuccess" @submit.prevent="register">
        <!-- Name Field -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1 text-gray-700">Name</label>
          <input
            v-model="form.name"
            type="text"
            :disabled="isLoading"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
            placeholder="Enter your name"
          />
          <p v-if="errors.name" class="text-red-600 text-sm mt-1">
            {{ errors.name[0] }}
          </p>
        </div>

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
        <div class="mb-4">
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

        <!-- Confirm Password Field -->
        <div class="mb-6">
          <label class="block text-sm font-medium mb-1 text-gray-700">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            :disabled="isLoading"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
            placeholder="Confirm your password"
          />
          <p v-if="errors.password_confirmation" class="text-red-600 text-sm mt-1">
            {{ errors.password_confirmation[0] }}
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
          {{ isLoading ? 'Creating Account...' : 'Create Account' }}
        </button>

        <p v-if="generalError" class="text-red-600 text-center mt-4">
          {{ generalError }}
        </p>
      </form>

      <div v-if="!registrationSuccess" class="mt-6 text-center">
        <p class="text-gray-600">
          Already have an account? 
          <router-link to="/login" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</router-link>
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
  name: 'RegisterPage',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      errors: {},
      generalError: '',
      isLoading: false,
      registrationSuccess: false
    }
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  methods: {
    async register() {
      this.errors = {}
      this.generalError = ''
      this.isLoading = true

      try {
        await axios.post('/api/register', this.form)
        
        // Show success state
        this.registrationSuccess = true
        
        // Show toast notification
        this.toast.success('Registration successful! Please check your email for verification instructions.', {
          timeout: 8000,
          icon: '📧'
        })

        // Optional: Auto-redirect to login after a delay
        setTimeout(() => {
          this.$router.push('/login')
        }, 5000)

      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {}
          this.toast.error('Please fix the errors below and try again.')
        } else {
          this.generalError = error.response?.data?.message || 'Registration failed. Please try again.'
          this.toast.error(this.generalError)
        }
      } finally {
        this.isLoading = false
      }
    }
  }
}
</script>

