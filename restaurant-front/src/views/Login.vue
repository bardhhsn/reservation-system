<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>
    <form @submit.prevent="login">
      <!-- Email Field -->
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded px-3 py-2"
          placeholder="Enter your email"
        />
        <p v-if="errors.email" class="text-red-600 text-sm mt-1">
          {{ errors.email[0] }}
        </p>
      </div>

      <!-- Password Field -->
      <div class="mb-6">
        <label class="block text-sm font-medium mb-1">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border rounded px-3 py-2"
          placeholder="Enter your password"
        />
        <p v-if="errors.password" class="text-red-600 text-sm mt-1">
          {{ errors.password[0] }}
        </p>
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
      >
        Login
      </button>

      <p v-if="generalError" class="text-red-600 text-center mt-4">
        {{ generalError }}
      </p>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginPage',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      errors: {},
      generalError: ''
    };
  },
  methods: {
    async login() {
      this.errors = {};
      this.generalError = '';

      try {
        const { data } = await axios.post(
          'http://localhost:8000/api/login',
          this.form
        );

        // Save token & user
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        // Set default auth header for axios
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;

        // Redirect to create-reservation page
        this.$router.push('/create');
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.generalError =
            error.response?.data?.message || 'An unexpected error occurred.';
        }
      }
    }
  }
};
</script>

<style scoped>
/* Add any scoped styles here if needed */
</style>
