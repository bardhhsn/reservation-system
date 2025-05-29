<!-- src/views/ReservationList.vue -->

<template>
  <div class="max-w-5xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6 text-center text-indigo-700">Rezervimet e Mia</h2>

    <div v-if="reservations.length === 0" class="text-center text-gray-500">
      Nuk ka rezervime aktive.
    </div>

    <div
      v-for="reservation in reservations"
      :key="reservation.id"
      class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200"
    >
      <p><strong>Klienti:</strong> {{ reservation.customer_name }}</p>
      <p><strong>Telefoni:</strong> {{ reservation.customer_phone }}</p>
      <p><strong>Tavolina:</strong> {{ reservation.table?.name }}</p>
      <p><strong>Koha:</strong> {{ reservation.reservation_time }}</p>
      <p><strong>Persona:</strong> {{ reservation.guest_count }}</p>

      <div v-if="editingId === reservation.id" class="mt-4 space-y-2">
        <input v-model="editData.customer_name" type="text" class="w-full border px-3 py-2 rounded" />
        <input v-model="editData.customer_phone" type="text" class="w-full border px-3 py-2 rounded" />
        <input v-model="editData.reservation_time" type="datetime-local" class="w-full border px-3 py-2 rounded" />
        <input v-model.number="editData.guest_count" type="number" min="1" class="w-full border px-3 py-2 rounded" />

        <div class="flex space-x-2 mt-3">
          <button
            @click="submitEditRequest(reservation.id)"
            class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700"
          >
            Dërgo Ndryshim
          </button>
          <button
            @click="cancelEdit"
            class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600"
          >
            Anulo
          </button>
        </div>
      </div>

      <div v-else class="mt-4 flex space-x-3">
        <button
          @click="startEdit(reservation)"
          class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
        >
          Kërko Ndryshim
        </button>
        <button
          @click="requestDelete(reservation.id)"
          class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
        >
          Kërko Fshirje
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'ReservationList',
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
      toast: useToast()
    }
  },
  mounted() {
    this.fetchReservations()
  },
  methods: {
    async fetchReservations() {
      try {
        const res = await api.get('/reservations')
        this.reservations = res.data
      } catch {
        this.toast.error('Gabim gjatë ngarkimit të rezervimeve')
      }
    },
    startEdit(reservation) {
      this.editingId = reservation.id
      this.editData = { ...reservation }
    },
    cancelEdit() {
      this.editingId = null
      this.editData = {
        customer_name: '',
        customer_phone: '',
        reservation_time: '',
        guest_count: 1
      }
    },
    async submitEditRequest(reservationId) {
      try {
        await api.post('/change-requests', {
          reservation_id: reservationId,
          type: 'edit',
          new_data: this.editData
        })
        this.toast.success('Kërkesa për ndryshim u dërgua me sukses!')
        this.editingId = null
      } catch {
        this.toast.error('Gabim gjatë dërgimit të kërkesës')
      }
    },
    async requestDelete(reservationId) {
      try {
        await api.post('/change-requests', {
          reservation_id: reservationId,
          type: 'delete'
        })
        this.toast.success('Kërkesa për fshirje u dërgua!')
      } catch {
        this.toast.error('Gabim gjatë dërgimit të kërkesës për fshirje')
      }
    }
  }
}
</script>

<style scoped>
/* any additional styles */
</style>
