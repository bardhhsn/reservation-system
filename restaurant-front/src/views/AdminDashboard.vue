<template>
  <LogoutButton />
  <div class="max-w-6xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6 text-center text-blue-700">Paneli i Adminit</h2>

    <div class="mb-4 flex items-center justify-center space-x-2">
      <input type="checkbox" v-model="showArchived" id="archiveToggle" />
      <label for="archiveToggle" class="text-gray-700 font-medium">
        Shfaq të arkivuara (approved/rejected)
      </label>
    </div>

    <div class="mb-6 flex justify-center space-x-4">
      <button
        v-for="s in statuses"
        :key="s"
        @click="activeFilter = s"
        :class="[ 'px-4 py-2 rounded font-semibold border',
          activeFilter === s
            ? 'bg-blue-600 text-white border-blue-600'
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
        ]"
      >
        {{ s === 'all' ? 'Të gjitha' : s.charAt(0).toUpperCase() + s.slice(1) }}
      </button>
    </div>

    <div v-if="filteredRequests.length === 0" class="text-center text-gray-500">
      Nuk ka kërkesa për këtë kategori.
    </div>

    <div
      v-for="req in filteredRequests"
      :key="req.id"
      class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4"
      :class="{
        'border-yellow-400': req.status === 'pending',
        'border-green-500': req.status === 'approved',
        'border-red-500': req.status === 'rejected'
      }"
    >
      <p><strong>Lloji:</strong> <span class="capitalize">{{ req.type }}</span></p>
      <p v-if="req.user"><strong>Përdoruesi:</strong> {{ req.user.name }} ({{ req.user.email }})</p>
      <p v-if="req.reservation"><strong>Rezervimi:</strong> {{ req.reservation.customer_name }} – {{ req.reservation.reservation_time }}</p>
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

      <p v-if="req.type === 'edit'" class="mt-2"><strong>Të dhënat e reja:</strong>
        <pre class="bg-gray-100 p-2 mt-1 rounded text-sm overflow-auto">{{ req.new_data }}</pre>
      </p>

      <p v-if="req.status !== 'pending' && req.approved_by">
        <strong>Vepruar nga:</strong> {{ req.approved_by }}
      </p>

      <div v-if="req.status === 'pending'" class="mt-4 flex space-x-3">
        <button
          @click="handleApprove(req.id)"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
        >
          Aprovo
        </button>
        <button
          @click="handleReject(req.id)"
          class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
        >
          Refuzo
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/axios'
import LogoutButton from '@/components/LogoutButton.vue'

export default {
  name: "AdminDashboard",
  components: { LogoutButton },
  data() {
    return {
      requests: [],
      showArchived: false,
      activeFilter: 'all',
      statuses: ['all', 'pending', 'approved', 'rejected']
    }
  },
  computed: {
    filteredRequests() {
      if (this.activeFilter === 'all') {
        return this.showArchived
          ? this.requests
          : this.requests.filter(req => req.status === 'pending')
      }
      return this.requests.filter(req => req.status === this.activeFilter)
    }
  },
  mounted() {
    this.fetchRequests()
  },
  methods: {
    async fetchRequests() {
      try {
        const res = await api.get('/change-requests')
        this.requests = res.data
      } catch (err) {
        console.error(err)
        alert("Gabim gjatë ngarkimit të kërkesave. Ju lutem kyçuni sërish.")
      }
    },
    async handleApprove(id) {
      try {
        await api.post(`/change-requests/${id}/approve`)
        this.fetchRequests()
      } catch {
        alert("Gabim gjatë aprovimit")
      }
    },
    async handleReject(id) {
      try {
        await api.post(`/change-requests/${id}/reject`)
        this.fetchRequests()
      } catch {
        alert("Gabim gjatë refuzimit")
      }
    }
  }
}
</script>
