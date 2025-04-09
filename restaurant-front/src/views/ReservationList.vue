<template>
  <div class="max-w-4xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">Rezervimet e mia</h2>

    <div v-if="reservations.length === 0" class="text-gray-600">Nuk ka rezervime.</div>

    <div v-for="reservation in reservations" :key="reservation.id" class="p-4 bg-white rounded shadow mb-4">
      <p><strong>Klienti:</strong> {{ reservation.customer_name }}</p>
      <p><strong>Telefoni:</strong> {{ reservation.customer_phone }}</p>
      <p><strong>Tavolina:</strong> {{ reservation.table?.name }}</p>
      <p><strong>Koha:</strong> {{ reservation.reservation_time }}</p>
      <p><strong>Persona:</strong> {{ reservation.guest_count }}</p>

      <div v-if="editingId === reservation.id" class="mt-4 space-y-2">
        <input v-model="editData.customer_name" type="text" class="w-full border p-2 rounded" placeholder="Emri" />
        <input v-model="editData.customer_phone" type="text" class="w-full border p-2 rounded" placeholder="Telefoni" />
        <input v-model="editData.reservation_time" type="datetime-local" class="w-full border p-2 rounded" />
        <input v-model="editData.guest_count" type="number" class="w-full border p-2 rounded" min="1" placeholder="Persona" />

        <button @click="submitEditRequest(reservation.id)" class="bg-green-600 text-white px-4 py-1 rounded">Dërgo Ndryshim</button>
        <button @click="cancelEdit" class="bg-gray-500 text-white px-4 py-1 rounded">Anulo</button>
      </div>

      <div v-else class="mt-3 space-x-2">
        <button @click="startEdit(reservation)" class="bg-blue-600 text-white px-3 py-1 rounded">Kërko Ndryshim</button>
        <button @click="requestDelete(reservation.id)" class="bg-red-600 text-white px-3 py-1 rounded">Kërko Fshirje</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: "ReservationList",
  data() {
    return {
      reservations: [],
      editingId: null,
      editData: {
        customer_name: '',
        customer_phone: '',
        reservation_time: '',
        guest_count: 1
      },
      toast: null
    }
  },
  mounted() {
    this.toast = useToast()
    this.fetchReservations()
  },
  methods: {
    fetchReservations() {
      axios.get(`${process.env.VUE_APP_API_URL}/reservations`)
        .then(res => this.reservations = res.data)
        .catch(() => this.toast.error("Nuk u mund të ngarkohen rezervimet"))
    },
    startEdit(reservation) {
      this.editingId = reservation.id
      this.editData = {
        customer_name: reservation.customer_name,
        customer_phone: reservation.customer_phone,
        reservation_time: reservation.reservation_time,
        guest_count: reservation.guest_count
      }
    },
    cancelEdit() {
      this.editingId = null
      this.editData = {}
    },
    submitEditRequest(reservationId) {
      axios.post(`${process.env.VUE_APP_API_URL}/change-requests`, {
        reservation_id: reservationId,
        type: 'edit',
        new_data: this.editData
      })
        .then(() => {
          this.editingId = null
          this.toast.success("Kërkesa për ndryshim u dërgua me sukses!")
        })
        .catch(() => this.toast.error("Gabim gjatë dërgimit të kërkesës"))
    },
    requestDelete(reservationId) {
      axios.post(`${process.env.VUE_APP_API_URL}/change-requests`, {
        reservation_id: reservationId,
        type: 'delete'
      })
        .then(() => this.toast.success("Kërkesa për fshirje u dërgua!"))
        .catch(() => this.toast.error("Gabim gjatë dërgimit të kërkesës për fshirje"))
    }
  }
}
</script>
