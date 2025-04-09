<template>
  <div>
    <!-- Navbar -->
    <nav class="bg-white shadow p-4 flex justify-between items-center">
      <div class="space-x-4">
        <router-link to="/create" class="text-gray-700 hover:text-blue-600 font-medium">Rezervo</router-link>
        <router-link to="/list" class="text-gray-700 hover:text-blue-600 font-medium">Rezervimet</router-link>
        <router-link v-if="isAdmin" to="/admin" class="text-gray-700 hover:text-blue-600 font-medium">Admin</router-link>
      </div>

      <div class="space-x-4">
        <router-link v-if="!isLoggedIn" to="/login" class="text-blue-600 font-semibold">Kyçu</router-link>
        <router-link v-if="!isLoggedIn" to="/register" class="text-green-600 font-semibold">Regjistrohu</router-link>
        <button v-if="isLoggedIn" @click="logout" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Shkyçu</button>
      </div>
    </nav>

    <!-- Content -->
    <main class="p-4 bg-gray-50 min-h-screen">
      <router-view />
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'App',
  computed: {
    isLoggedIn() {
      return !!localStorage.getItem('token')
    },
    isAdmin() {
      const user = JSON.parse(localStorage.getItem('user'))
      return user?.role === 'admin'
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      this.$router.push('/login')
    }
  }
}
</script>

<style>
body {
  margin: 0;
  font-family: 'Inter', sans-serif;
}
</style>
