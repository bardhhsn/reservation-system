<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-center">Register</h2>
    <form @submit.prevent="register">
      <!-- Name Field -->
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border rounded px-3 py-2"
          placeholder="Enter your name"
        />
        <p v-if="errors.name" class="text-red-600 text-sm mt-1">
          {{ errors.name[0] }}
        </p>
      </div>

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
      <div class="mb-4">
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

      <!-- Confirm Password Field -->
      <div class="mb-6">
        <label class="block text-sm font-medium mb-1">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="w-full border rounded px-3 py-2"
          placeholder="Confirm your password"
        />
        <p v-if="errors.password_confirmation" class="text-red-600 text-sm mt-1">
          {{ errors.password_confirmation[0] }}
        </p>
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
      >
        Register
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
      generalError: ''
    };
  },
  methods: {
    async register() {
      this.errors = {};
      this.generalError = '';
      try {
        await axios.post(
          'http://localhost:8000/api/register',
          this.form
        );
        // Redirect to login page on success
        this.$router.push({ name: 'Login' });
      } catch (error) {
        if (error.response && error.response.status === 422) {
          console.log('Validation errors:', error.response.data);
          this.errors = error.response.data.errors || {};
        } else {
          this.generalError = 'An unexpected error occurred.';
          console.error(error);
        }
      }
    }
  }
};
</script>

<style scoped>
/* Add scoped styles here if needed */
</style>
