<template>
  <div>
    <!-- Navbar -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <!-- Logo/Brand -->
          <div class="flex items-center">
            <router-link to="/" class="flex items-center space-x-2">
              <img src="@/assets/logo.png" alt="Logo" class="h-8 w-8" />
              <span class="text-xl font-bold text-gray-800">Restaurant</span>
            </router-link>
          </div>

          <!-- Navigation Links (only show when logged in) -->
          <div v-if="isLoggedIn" class="hidden md:flex space-x-6">
            <router-link 
              to="/booking" 
              class="nav-link"
              :class="{ 'active': $route.name === 'Booking' }"
            >
              Rezervo
            </router-link>
            <router-link 
              to="/reservations" 
              class="nav-link"
              :class="{ 'active': $route.name === 'Reservations' }"
            >
              Rezervimet
            </router-link>
            <router-link 
              to="/my-requests" 
              class="nav-link"
              :class="{ 'active': $route.name === 'MyRequests' }"
            >
              Kërkesat e mia
            </router-link>
            <router-link 
              v-if="isAdmin" 
              to="/admin" 
              class="nav-link"
              :class="{ 'active': $route.name === 'Admin' }"
            >
              Admin
            </router-link>
            <router-link 
              v-if="isAdmin" 
              to="/admin/statistics" 
              class="nav-link"
              :class="{ 'active': $route.name === 'Statistics' }"
            >
              Statistika
            </router-link>
          </div>

          <!-- User Actions -->
          <div class="flex items-center space-x-4">
            <!-- Guest Actions -->
            <div v-if="!isLoggedIn" class="flex space-x-3">
              <router-link 
                to="/login" 
                class="text-blue-600 hover:text-blue-800 font-medium transition-colors"
              >
                Kyçu
              </router-link>
              <router-link 
                to="/register" 
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
              >
                Regjistrohu
              </router-link>
            </div>

            <!-- Logged in user actions -->
            <div v-if="isLoggedIn" class="flex items-center space-x-3">
              <span class="text-gray-600 text-sm">
                Mirë se erdhe, {{ currentUser?.name }}
              </span>
              <button 
                @click="logout" 
                :disabled="isLoggingOut"
                class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors disabled:bg-red-400 disabled:cursor-not-allowed flex items-center"
              >
                <svg v-if="isLoggingOut" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoggingOut ? 'Logging out...' : 'Shkyçu' }}
              </button>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div v-if="isLoggedIn" class="md:hidden">
            <button 
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="text-gray-600 hover:text-gray-800"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div v-if="isLoggedIn && mobileMenuOpen" class="md:hidden border-t border-gray-200 py-2">
          <router-link to="/booking" class="mobile-nav-link" @click="mobileMenuOpen = false">
            Rezervo
          </router-link>
          <router-link to="/reservations" class="mobile-nav-link" @click="mobileMenuOpen = false">
            Rezervimet
          </router-link>
          <router-link to="/my-requests" class="mobile-nav-link" @click="mobileMenuOpen = false">
            Kërkesat e mia
          </router-link>
          <router-link v-if="isAdmin" to="/admin" class="mobile-nav-link" @click="mobileMenuOpen = false">
            Admin
          </router-link>
          <router-link v-if="isAdmin" to="/admin/statistics" class="mobile-nav-link" @click="mobileMenuOpen = false">
            Statistika
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main>
      <router-view />
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'App',
  data() {
    return {
      mobileMenuOpen: false,
      isLoggingOut: false,
      // Reactive auth state
      authState: {
        isLoggedIn: !!localStorage.getItem('token'),
        user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
      }
    }
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  computed: {
    isLoggedIn() {
      return this.authState.isLoggedIn
    },
    isAdmin() {
      return this.authState.user?.role === 'admin'
    },
    currentUser() {
      return this.authState.user
    }
  },
  mounted() {
    // Initialize auth state properly
    this.updateAuthState()
    
    // Listen for storage changes (for multi-tab sync)
    window.addEventListener('storage', this.handleStorageChange)
    
    // Listen for custom auth events
    window.addEventListener('authStateChanged', this.updateAuthState)
    
    // Listen for route changes to update auth state
    this.$router.afterEach(() => {
      this.updateAuthState()
    })
  },
  beforeUnmount() {
    window.removeEventListener('storage', this.handleStorageChange)
    window.removeEventListener('authStateChanged', this.updateAuthState)
  },
  methods: {
    updateAuthState() {
      this.authState.isLoggedIn = !!localStorage.getItem('token')
      this.authState.user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
      
      // Force reactivity update
      this.$forceUpdate()
    },
    
    handleStorageChange(event) {
      if (event.key === 'token' || event.key === 'user') {
        this.updateAuthState()
      }
    },
    
    async logout() {
      this.isLoggingOut = true
      
      try {
        // Optional: Call logout API endpoint
        try {
          await axios.post('/api/logout')
        } catch (error) {
          // Continue with logout even if API call fails
          console.warn('Logout API call failed:', error)
        }
        
        // Clear local storage
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        
        // Clear axios default headers
        delete axios.defaults.headers.common['Authorization']
        
        // Update auth state immediately
        this.authState.isLoggedIn = false
        this.authState.user = null
        
        // Close mobile menu
        this.mobileMenuOpen = false
        
        // Show success message
        this.toast.success('You have been logged out successfully.', {
          timeout: 3000
        })
        
        // Redirect to home page
        await this.$router.push('/')
        
        // Force component update
        this.$forceUpdate()
        
        // Emit custom event for other components
        window.dispatchEvent(new CustomEvent('authStateChanged'))
        
      } catch (error) {
        this.toast.error('Error during logout. Please try again.')
        console.error('Logout error:', error)
      } finally {
        this.isLoggingOut = false
      }
    }
  },
  watch: {
    $route() {
      // Close mobile menu when route changes
      this.mobileMenuOpen = false
    }
  }
}
</script>

<style>
body {
  margin: 0;
  font-family: 'Inter', sans-serif;
}

.nav-link {
  @apply text-gray-700 hover:text-blue-600 font-medium transition-colors px-3 py-2 rounded-md;
}

.nav-link.active {
  @apply text-blue-600 bg-blue-50;
}

.mobile-nav-link {
  @apply block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 transition-colors;
}
</style>
