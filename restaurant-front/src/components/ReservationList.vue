<template>
  <div class="max-w-md mx-auto mt-10 p-4 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Krijo Rezervim</h2>
    <form @submit.prevent="submitReservation">
      <div class="mb-4">
        <label class="block mb-1">Emri i klientit</label>
        <input v-model="form.customer_name" type="text" class="w-full border p-2 rounded" required />
      </div>

      <div class="mb-4">
        <label class="block mb-1">Telefoni</label>
        <input v-model="form.customer_phone" type="text" class="w-full border p-2 rounded" required />
      </div>

      <div class="mb-4">
        <label class="block mb-1">Tavolina</label>
        <select v-model="form.table_id" class="w-full border p-2 rounded" required>
          <option disabled value="">Zgjidh tavolinën</option>
          <option v-for="table in tables" :key="table.id" :value="table.id">
            {{ table.name }} ({{ table.seats }} vende)
          </option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Koha e rezervimit</label>
        <input v-model="form.reservation_time" type="datetime-local" class="w-full border p-2 rounded" required />
      </div>

      <div class="mb-4">
        <label class="block mb-1">Numri i personave</label>
        <input v-model="form.guest_count" type="number" class="w-full border p-2 rounded" min="1" required />
      </div>

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Rezervo
      </button>
    </form>

    <p v-if="message" class="mt-4 text-red-600">{{ message }}</p>
  </div>
</template>

<script>
import axios from 'axios'

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
      message: ''
    }
  },
  mounted() {
    axios.get(`${process.env.VUE_APP_API_URL}/tables`)
      .then(res => this.tables = res.data)
      .catch(err => this.message = "Nuk u mund të merret lista e tavolinave");
  },
  methods: {
    submitReservation() {
      axios.post(`${process.env.VUE_APP_API_URL}/reservations`, this.form)
        .then(() => {
          this.message = "Rezervimi u shtua me sukses!";
          this.form = {
            customer_name: '',
            customer_phone: '',
            table_id: '',
            reservation_time: '',
            guest_count: 1
          }
        })
        .catch(error => {
          if (error.response && error.response.data.message) {
            this.message = error.response.data.message
          } else {
            this.message = "Gabim gjatë krijimit të rezervimit"
          }
        });
    }
  }
}
</script>
