<!-- src/views/CreateReservation.vue -->

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 p-6">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-4xl">
      <h2 class="text-2xl font-bold mb-6 text-center">Rezervo Tavolinë</h2>

      <!-- Table Cards -->
      <div v-if="!selectedTable">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="table in tables"
            :key="table.id"
            class="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-lg"
            @click="selectTable(table)"
          >
            <h3 class="text-lg font-semibold mb-2">Tavolina {{ table.name }}</h3>
            <p class="text-gray-500">{{ table.seats }} vende</p>
            <p
              class="text-sm mt-2"
              :class="table.status === 'free' ? 'text-green-600' : 'text-red-600'"
            >
              {{ table.status === 'free' ? 'E lirë' : 'Zënë' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Reservation Form Modal -->
      <div v-else>
        <button
          class="mb-4 text-blue-600 hover:underline"
          @click="selectedTable = null"
        >
          ← Kthehu te tavolinat
        </button>
        <h3 class="text-xl font-semibold mb-4">
          Rezervo Tavolinën {{ selectedTable.name }}
        </h3>

        <form @submit.prevent="submitReservation">
          <div v-if="error" class="mb-4 text-red-600">{{ error }}</div>

          <div class="mb-4">
            <label class="block mb-1">Emri i klientit</label>
            <input
              v-model="form.customer_name"
              type="text"
              required
              class="w-full border rounded px-3 py-2"
            />
          </div>

          <div class="mb-4">
            <label class="block mb-1">Telefoni</label>
            <input
              v-model="form.customer_phone"
              type="text"
              required
              class="w-full border rounded px-3 py-2"
            />
          </div>

          <div class="mb-4">
            <label class="block mb-1">Numri i mysafirëve</label>
            <input
              v-model.number="form.guest_count"
              type="number"
              :max="selectedTable?.seats"
              min="1"
              required
              class="w-full border rounded px-3 py-2"
            />
          </div>

          <div class="mb-4">
            <label class="block mb-1">Data & Ora</label>
            <input
              v-model="form.reservation_time"
              type="datetime-local"
              required
              class="w-full border rounded px-3 py-2"
            />
          </div>

          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Rezervo
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'CreateReservation',
  data() {
    return {
      tables: [],
      selectedTable: null,
      form: {
        customer_name: '',
        customer_phone: '',
        table_id: null,
        guest_count: 1,
        reservation_time: ''
      },
      error: ''
    }
  },
  created() {
    this.loadTables()
  },
  methods: {
    async loadTables() {
      const toast = useToast()
      try {
        const res = await api.get('/tables')
        this.tables = res.data
      } catch (err) {
        console.error('Failed to load tables:', err)
        toast.error('Nuk u ngarkuan tavolinat.')
      }
    },
    selectTable(table) {
      this.selectedTable = table
      this.form = {
        customer_name: '',
        customer_phone: '',
        table_id: table.id,
        guest_count: 1,
        reservation_time: ''
      }
      this.error = ''
    },
    async submitReservation() {
      const toast = useToast()
      try {
        await api.post('/reservations', this.form)
        toast.success('Rezervim i suksesshëm!')
        this.selectedTable = null
        this.loadTables()
      } catch (err) {
        console.error('Reservation failed:', err)
        let msg = 'Dështoi rezervimi.'
        if (err.response?.data?.errors) {
          msg = Object.values(err.response.data.errors)[0][0]
        } else if (err.response?.data?.message) {
          msg = err.response.data.message
        }
        this.error = msg
        toast.error(msg)
      }
    }
  }
}
</script>

<style scoped>
/* Add any custom styles here */
</style>
