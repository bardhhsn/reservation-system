<template>
    <div class="max-w-4xl mx-auto mt-10">
      <h2 class="text-2xl font-bold mb-4">Kërkesat për ndryshim/fshirje</h2>
  
      <div v-if="requests.length === 0" class="text-gray-600">Nuk ka kërkesa në pritje.</div>
  
      <div v-for="req in requests" :key="req.id" class="p-4 bg-white rounded shadow mb-4">
        <p><strong>Lloji:</strong> {{ req.type }}</p>
        <p><strong>Përdoruesi:</strong> {{ req.user.name }} ({{ req.user.email }})</p>
        <p><strong>Rezervimi:</strong> {{ req.reservation.customer_name }} - {{ req.reservation.reservation_time }}</p>
        <p v-if="req.type === 'edit' && req.new_data"><strong>Të dhënat e reja:</strong> {{ req.new_data }}</p>
        <p><strong>Statusi:</strong> {{ req.status }}</p>
  
        <div class="mt-3 space-x-2" v-if="req.status === 'pending'">
          <button @click="handleApprove(req.id)" class="bg-green-600 text-white px-4 py-1 rounded">Aprovo</button>
          <button @click="handleReject(req.id)" class="bg-red-600 text-white px-4 py-1 rounded">Refuzo</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    name: "AdminDashboard",
    data() {
      return {
        requests: []
      }
    },
    mounted() {
      this.fetchRequests()
    },
    methods: {
      fetchRequests() {
        axios.get(`${process.env.VUE_APP_API_URL}/change-requests`)
          .then(res => this.requests = res.data)
          .catch(err => console.error(err))
      },
      handleApprove(id) {
        axios.post(`${process.env.VUE_APP_API_URL}/change-requests/${id}/approve`)
          .then(() => this.fetchRequests())
          .catch(err => console.error(err))
      },
      handleReject(id) {
        axios.post(`${process.env.VUE_APP_API_URL}/change-requests/${id}/reject`)
          .then(() => this.fetchRequests())
          .catch(err => console.error(err))
      }
    }
  }
  </script>
  