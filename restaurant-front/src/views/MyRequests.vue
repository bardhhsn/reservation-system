<template>
  <div class="max-w-5xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6 text-center text-indigo-700">{{ pageTitle }}</h2>

    <div v-if="requests.length === 0" class="text-gray-500 text-center">
      Nuk ke dërguar asnjë kërkesë.
    </div>

    <div
      v-for="req in requests"
      :key="req.id"
      class="bg-white rounded-xl shadow p-6 mb-6 border-l-4"
      :class="{
        'border-yellow-400': req.status === 'pending',
        'border-green-500': req.status === 'approved',
        'border-red-500': req.status === 'rejected'
      }"
    >
      <p><strong>Lloji:</strong> <span class="capitalize">{{ req.type }}</span></p>
      <p><strong>Statusi:</strong>
        <span
          class="inline-block px-2 py-1 rounded text-sm font-semibold"
          :class="{
            'bg-yellow-100 text-yellow-800': req.status === 'pending',
            'bg-green-100 text-green-800': req.status === 'approved',
            'bg-red-100 text-red-800': req.status === 'rejected'
          }"
        >
          {{ req.status }}
        </span>
      </p>
      <p><strong>Data:</strong> {{ formatDate(req.created_at) }}</p>
      
      <!-- Handle case where reservation might be null (deleted) -->
      <p v-if="req.reservation"><strong>Rezervimi:</strong> {{ req.reservation.customer_name }} në {{ req.reservation.reservation_time }}</p>
      <p v-else class="text-gray-500 italic"><strong>Rezervimi:</strong> (Rezervimi është fshirë)</p>

      <p v-if="req.type === 'edit'" class="mt-2"><strong>Të dhënat e reja:</strong>
        <pre class="bg-gray-100 p-2 mt-1 rounded text-sm overflow-auto">{{ req.new_data }}</pre>
      </p>

      <div v-if="req.status === 'pending'" class="mt-3">
        <button
          @click="cancelRequest(req.id)"
          class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition"
        >
          Anulo Kërkesën
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/axios'
import { useToast } from 'vue-toastification'

export default {
  name: "MyRequests",
  data() {
    return {
      requests: []
    }
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  mounted() {
    this.fetchRequests()
  },
  methods: {
    async fetchRequests() {
      try {
        const res = await api.get('/change-requests/my')
        this.requests = res.data
      } catch (error) {
        console.error("Gabim gjatë ngarkimit të kërkesave:", error)
        this.toast.error("Gabim gjatë ngarkimit të kërkesave")
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleString()
    },
    async cancelRequest(id) {
      if (confirm("A je i sigurt që dëshiron të anulosh këtë kërkesë?")) {
        try {
          await api.delete(`/change-requests/${id}`)
          this.toast.success("Kërkesa u anulua me sukses")
          this.fetchRequests() // Refresh the list
        } catch (error) {
          console.error("Gabim gjatë anulimit:", error)
          this.toast.error("Gabim gjatë anulimit të kërkesës")
        }
      }
    }
  },
  computed: {
    pageTitle() {
      const user = JSON.parse(localStorage.getItem('user') || '{}')
      return user.role === 'admin' ? 'Kërkesat' : 'Kërkesat e Mia'
    }
  }
}
</script>

