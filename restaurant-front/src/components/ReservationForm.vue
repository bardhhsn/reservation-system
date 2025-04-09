<template>
  <div class="max-w-md mx-auto mt-10 p-4 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Krijo Rezervim</h2>

    <form @submit.prevent="submitReservation">
      <!-- Emri -->
      <div class="mb-4">
        <label class="block mb-1">Emri i klientit</label>
        <input v-model="form.customer_name" type="text" class="w-full border p-2 rounded" />
        <p v-if="errors.customer_name" class="text-red-600 text-sm">{{ errors.customer_name }}</p>
      </div>

      <!-- Telefoni -->
      <div class="mb-4">
        <label class="block mb-1">Telefoni</label>
        <input v-model="form.customer_phone" type="text" class="w-full border p-2 rounded" />
        <p v-if="errors.customer_phone" class="text-red-600 text-sm">{{ errors.customer_phone }}</p>
      </div>

      <!-- Tavolina -->
      <div class="mb-4">
        <label class="block mb-1">Tavolina</label>
        <select v-model="form.table_id" class="w-full border p-2 rounded">
          <option disabled value="">Zgjidh tavolinën</option>
          <option v-for="table in tables" :key="table.id" :value="table.id">
            {{ table.name }} ({{ table.seats }} vende)
          </option>
        </select>
        <p v-if="errors.table_id" class="text-red-600 text-sm">{{ errors.table_id }}</p>
      </div>

      <!-- Koha e rezervimit -->
      <div class="mb-4">
        <label class="block mb-1">Koha e rezervimit</label>
        <input v-model="form.reservation_time" type="datetime-local" class="w-full border p-2 rounded" />
        <p v-if="errors.reservation_time" class="text-red-600 text-sm">{{ errors.reservation_time }}</p>
      </div>

      <!-- Numri i personave -->
      <div class="mb-4">
        <label class="block mb-1">Numri i personave</label>
        <input v-model="form.guest_count" type="number" class="w-full border p-2 rounded" min="1" />
        <p v-if="errors.guest_count" class="text-red-600 text-sm">{{ errors.guest_count }}</p>
      </div>

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Rezervo
      </button>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  data() {
    return {
      tables: [],
      form: {
        customer_name: '',
        customer_phone: '',
        table_id: '',
        reservation_time: '',
        guest_count: 1
      },
      errors: {}
    }
  },
  mounted() {
    const toast = useToast()
    axios.get(`${process.env.VUE_APP_API_URL}/tables`)
      .then(res => this.tables = res.data)
      .catch(() => toast.error("Nuk u mund të merret lista e tavolinave"));
  },
  methods: {
    validateForm() {
      this.errors = {}

      if (!this.form.customer_name) {
        this.errors.customer_name = "Emri është i detyrueshëm"
      }

      if (!this.form.customer_phone) {
        this.errors.customer_phone = "Telefoni është i detyrueshëm"
      } else if (!/^\d+$/.test(this.form.customer_phone)) {
        this.errors.customer_phone = "Telefoni duhet të përmbajë vetëm numra"
      }

      if (!this.form.table_id) {
        this.errors.table_id = "Zgjidh një tavolinë"
      }

      if (!this.form.reservation_time) {
        this.errors.reservation_time = "Zgjidh kohën e rezervimit"
      }

      if (!this.form.guest_count || this.form.guest_count < 1) {
        this.errors.guest_count = "Së paku 1 person"
      }

      return Object.keys(this.errors).length === 0
    },

    submitReservation() {
      const toast = useToast()

      if (!this.validateForm()) {
        toast.error("Plotëso të gjitha fushat saktë.")
        return
      }

      axios.post(`${process.env.VUE_APP_API_URL}/reservations`, this.form)
        .then(() => {
          toast.success("Rezervimi u shtua me sukses!")
          this.form = {
            customer_name: '',
            customer_phone: '',
            table_id: '',
            reservation_time: '',
            guest_count: 1
          }
        })
        .catch(error => {
          const msg = error.response?.data?.message || "Gabim gjatë krijimit të rezervimit"
          toast.error(msg)
        });
    }
  }
}
</script>
