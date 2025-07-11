<template>
  <div>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-6">
      <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-4xl">
        <h2 class="text-2xl font-bold mb-6 text-center">Rezervo Tavolinë</h2>

        <!-- Success Message -->
        <div v-if="bookingSuccess" class="mb-6 p-6 bg-green-50 border border-green-200 rounded-lg text-center">
          <div class="flex justify-center mb-4">
            <svg class="w-16 h-16 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-green-800 mb-2">Reservation Confirmed!</h3>
          <p class="text-green-700 mb-4">
            Your table has been successfully reserved. You will receive a confirmation email shortly.
          </p>
          <div class="space-y-2 space-x-4">
            <button 
              @click="makeAnotherReservation" 
              class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors"
            >
              Make Another Reservation
            </button>
            <router-link 
              to="/reservations" 
              class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
              View My Reservations
            </router-link>
          </div>
        </div>

        <!-- Table Cards -->
        <div v-if="!selectedTable && !bookingSuccess">
          <div class="mb-4 text-center">
            <h3 class="text-lg font-semibold text-gray-700">Zgjidh një tavolinë për rezervim</h3>
            <p class="text-gray-500">Kliko mbi një tavolinë të lirë për të vazhduar</p>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="table in tables"
              :key="table.id"
              class="bg-white rounded-lg shadow-md p-4 border-2 transition-all duration-200"
              :class="{
                'cursor-pointer hover:shadow-lg hover:border-blue-300 hover:bg-blue-50': isTableAvailable(table),
                'opacity-50 cursor-not-allowed border-gray-200 bg-gray-50': !isTableAvailable(table)
              }"
              @click="handleTableClick(table)"
            >
              <div class="text-center">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">Tavolina {{ table.name }}</h3>
                <p class="text-gray-600 mb-2">{{ table.seats }} vende</p>
                <div class="flex items-center justify-center">
                  <span
                    class="inline-block px-3 py-1 rounded-full text-sm font-medium"
                    :class="{
                      'bg-green-100 text-green-800': isTableAvailable(table),
                      'bg-red-100 text-red-800': !isTableAvailable(table)
                    }"
                  >
                    {{ isTableAvailable(table) ? '✓ E lirë' : '✗ Zënë' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="tables.length === 0" class="text-center py-8">
            <p class="text-gray-500">Duke ngarkuar tavolinat...</p>
          </div>
        </div>

        <!-- Reservation Form Modal -->
        <div v-if="selectedTable && !bookingSuccess">
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
            <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-600">
              {{ error }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="mb-4">
                <label class="block mb-1 font-medium">Emri i klientit</label>
                <input
                  v-model="form.customer_name"
                  type="text"
                  required
                  :disabled="isSubmitting"
                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                />
              </div>

              <div class="mb-4">
                <label class="block mb-1 font-medium">Telefoni</label>
                <input
                  v-model="form.customer_phone"
                  type="text"
                  required
                  :disabled="isSubmitting"
                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                />
              </div>

              <div class="mb-4">
                <label class="block mb-1 font-medium">Numri i mysafirëve</label>
                <input
                  v-model.number="form.guest_count"
                  type="number"
                  :max="selectedTable?.seats"
                  min="1"
                  required
                  :disabled="isSubmitting"
                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                />
              </div>

              <div class="mb-4">
                <label class="block mb-1 font-medium">Data & Ora</label>
                <input
                  v-model="form.reservation_time"
                  type="datetime-local"
                  required
                  :disabled="isSubmitting"
                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                />
              </div>
            </div>

            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full bg-blue-600 text-white px-4 py-3 rounded hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isSubmitting ? 'Rezervon...' : 'Rezervo' }}
            </button>
          </form>
        </div>
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
      bookingSuccess: false,
      isSubmitting: false,
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
  setup() {
    const toast = useToast()
    return { toast }
  },
  created() {
    this.loadTables()
  },
  methods: {
    async loadTables() {
      try {
        const res = await api.get('/tables')
        this.tables = res.data
      } catch (err) {
        console.error('Failed to load tables:', err)
        this.toast.error('Nuk u ngarkuan tavolinat. Ju lutem provoni përsëri.')
      }
    },
    
    // Enhanced method to check if table is available
    isTableAvailable(table) {
      if (!table) return false
      
      // Check for various possible status values that might indicate availability
      const status = table.status
      
      // Common status values that indicate availability
      const availableStatuses = [
        'free', 'available', 'open', 'vacant', 
        'Free', 'Available', 'Open', 'Vacant',
        'FREE', 'AVAILABLE', 'OPEN', 'VACANT',
        null, undefined, '', '1', 1, true
      ]
      
      // Check if status indicates availability
      return availableStatuses.includes(status)
    },
    
    handleTableClick(table) {
      if (!this.isTableAvailable(table)) {
        this.toast.warning('Kjo tavolinë nuk është e lirë. Ju lutem zgjidhni një tjetër.')
        return
      }
      
      this.selectTable(table)
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
      this.bookingSuccess = false
      
      // Scroll to form
      this.$nextTick(() => {
        const formElement = document.querySelector('form')
        if (formElement) {
          formElement.scrollIntoView({ behavior: 'smooth', block: 'start' })
        }
      })
    },
    
    async submitReservation() {
      this.isSubmitting = true
      this.error = ''
      
      try {
        await api.post('/reservations', this.form)
        
        // Show success state
        this.bookingSuccess = true
        this.selectedTable = null
        
        // Show toast notification
        this.toast.success('🎉 Reservation confirmed! Check your email for details.', {
          timeout: 5000
        })
        
        // Reload tables to update availability
        await this.loadTables()
        
      } catch (err) {
        console.error('Reservation failed:', err)
        let message = 'Dështoi rezervimi.'
        
        if (err.response?.data?.errors) {
          const errors = err.response.data.errors
          message = Object.values(errors).flat().join(', ')
        } else if (err.response?.data?.message) {
          message = err.response.data.message
        }
        
        this.error = message
        this.toast.error(message)
      } finally {
        this.isSubmitting = false
      }
    },
    
    makeAnotherReservation() {
      this.bookingSuccess = false
      this.selectedTable = null
      this.form = {
        customer_name: '',
        customer_phone: '',
        table_id: null,
        guest_count: 1,
        reservation_time: ''
      }
      this.error = ''
      
      // Scroll back to table selection
      this.$nextTick(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' })
      })
    }
  }
}
</script>
