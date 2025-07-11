<template>
  <div class="max-w-5xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6 text-center text-indigo-700">{{ pageTitle }}</h2>

    <!-- Auto-refresh indicator -->
    <div class="mb-4 flex justify-center items-center space-x-2 text-sm text-gray-500">
      <div class="flex items-center space-x-1">
        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
        <span>Auto-refresh aktiv</span>
      </div>
      <span>•</span>
      <span>Përditësim i fundit: {{ lastUpdateTime }}</span>
      <button 
        @click="fetchReservations(true)" 
        :disabled="loading"
        class="ml-2 px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition-colors disabled:opacity-50"
      >
        🔄 Rifresko
      </button>
    </div>

    <!-- Advanced Filters -->
    <div class="mb-6 bg-white rounded-lg shadow-md p-6 border">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">🔍 Filtrat</h3>
        <button 
          @click="clearAllFilters" 
          class="text-sm text-gray-500 hover:text-red-600 transition-colors"
          v-if="hasActiveFilters"
        >
          Pastro të gjitha
        </button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Name Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kërko sipas emrit</label>
          <div class="relative">
            <input
              v-model="filters.nameSearch"
              type="text"
              placeholder="Shkruaj emrin e klientit..."
              class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <svg class="absolute left-2 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>

        <!-- Table Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tavolina</label>
          <select
            v-model="filters.selectedTable"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
            <option value="">Të gjitha tavolinat</option>
            <option v-for="table in availableTables" :key="table.id" :value="table.id">
              {{ table.name }} ({{ table.seats }} vende)
            </option>
          </select>
        </div>
      </div>

      <!-- Active Filters Display -->
      <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
        <span class="text-sm text-gray-600">Filtrat aktiv:</span>
        
        <span v-if="filters.nameSearch" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          Emri: "{{ filters.nameSearch }}"
          <button @click="filters.nameSearch = ''" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
        </span>
        
        <span v-if="filters.selectedTable" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
          Tavolina: {{ getTableName(filters.selectedTable) }}
          <button @click="filters.selectedTable = ''" class="ml-1 text-green-600 hover:text-green-800">×</button>
        </span>
      </div>

      <!-- Results Count -->
      <div v-if="hasActiveFilters" class="mt-3 text-sm text-gray-600">
        {{ filteredReservations.length }} rezervim(e) u gjetën nga {{ reservations.length }} totale
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      <p class="mt-2 text-gray-600">Duke ngarkuar rezervimet...</p>
    </div>

    <div v-else>
      <!-- Status Filter Buttons -->
      <div class="mb-6 flex justify-center space-x-4">
        <button
          v-for="filter in statusFilters"
          :key="filter.key"
          @click="activeFilter = filter.key"
          :class="[
            'px-4 py-2 rounded font-semibold border transition-colors',
            activeFilter === filter.key
              ? 'bg-indigo-600 text-white border-indigo-600'
              : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
          ]"
        >
          {{ filter.label }}
          <span class="ml-2 text-sm opacity-75">({{ filter.count }})</span>
        </button>
      </div>

      <!-- Active Reservations -->
      <div v-if="activeFilter === 'all' || activeFilter === 'active'" class="mb-8">
        <div class="flex items-center mb-4">
          <div class="flex-1 h-px bg-green-200"></div>
          <h3 class="px-4 text-lg font-semibold text-green-700 bg-white">
            🟢 Rezervime Aktive ({{ filteredActiveReservations.length }})
          </h3>
          <div class="flex-1 h-px bg-green-200"></div>
        </div>
        
        <div v-if="filteredActiveReservations.length === 0" class="text-center text-gray-500 py-4">
          {{ hasActiveFilters ? 'Nuk u gjetën rezervime aktive me këto filtra.' : 'Nuk ka rezervime aktive.' }}
        </div>
        
        <div v-for="reservation in filteredActiveReservations" :key="reservation.id" class="mb-4">
          <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 bg-green-50 transition-all duration-500" 
               :class="{ 'animate-pulse': isNewlyActive(reservation) }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <p class="text-sm text-gray-600">Klienti</p>
                <p class="font-semibold">{{ reservation.customer_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Telefoni</p>
                <p class="font-semibold">{{ reservation.customer_phone }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Tavolina</p>
                <p class="font-semibold">{{ reservation.table?.name || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Persona</p>
                <p class="font-semibold">{{ reservation.guest_count }}</p>
              </div>
            </div>
            
            <div class="mb-4">
              <p class="text-sm text-gray-600">Koha e Rezervimit</p>
              <p class="font-semibold text-lg">{{ formatDateTime(reservation.reservation_time) }}</p>
              <p class="text-sm text-green-600 font-semibold">{{ getTimeStatus(reservation.reservation_time) }}</p>
            </div>

            <!-- Edit Form -->
            <div v-if="editingId === reservation.id" class="mt-4 space-y-3 p-4 bg-gray-50 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input 
                  v-model="editData.customer_name" 
                  type="text" 
                  placeholder="Emri i klientit"
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
                <input 
                  v-model="editData.customer_phone" 
                  type="text" 
                  placeholder="Telefoni"
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input 
                  v-model="editData.reservation_time" 
                  type="datetime-local" 
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
                <input 
                  v-model.number="editData.guest_count" 
                  type="number" 
                  min="1" 
                  placeholder="Numri i personave"
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
              </div>

              <div class="flex space-x-2 pt-2">
                <button 
                  @click="submitEditRequest(reservation.id)" 
                  class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
                >
                  Dërgo Ndryshim
                </button>
                <button 
                  @click="cancelEdit" 
                  class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
                >
                  Anulo
                </button>
              </div>
            </div>

            <!-- Action Buttons -->
            <div v-else class="mt-4 flex flex-wrap gap-2">
              <button 
                @click="startEdit(reservation)" 
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
              >
                Kërko Ndryshim
              </button>
              <button 
                @click="requestDelete(reservation.id)" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors"
              >
                Kërko Fshirje
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Reservations -->
      <div v-if="activeFilter === 'all' || activeFilter === 'upcoming'" class="mb-8">
        <div class="flex items-center mb-4">
          <div class="flex-1 h-px bg-blue-200"></div>
          <h3 class="px-4 text-lg font-semibold text-blue-700 bg-white">
            🔵 Rezervime të Ardhshme ({{ filteredUpcomingReservations.length }})
          </h3>
          <div class="flex-1 h-px bg-blue-200"></div>
        </div>
        
        <div v-if="filteredUpcomingReservations.length === 0" class="text-center text-gray-500 py-4">
          {{ hasActiveFilters ? 'Nuk u gjetën rezervime të ardhshme me këto filtra.' : 'Nuk ka rezervime të ardhshme.' }}
        </div>
        
        <div v-for="reservation in filteredUpcomingReservations" :key="reservation.id" class="mb-4">
          <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 bg-blue-50 transition-all duration-500">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <p class="text-sm text-gray-600">Klienti</p>
                <p class="font-semibold">{{ reservation.customer_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Telefoni</p>
                <p class="font-semibold">{{ reservation.customer_phone }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Tavolina</p>
                <p class="font-semibold">{{ reservation.table?.name || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Persona</p>
                <p class="font-semibold">{{ reservation.guest_count }}</p>
              </div>
            </div>
            
            <div class="mb-4">
              <p class="text-sm text-gray-600">Koha e Rezervimit</p>
              <p class="font-semibold text-lg">{{ formatDateTime(reservation.reservation_time) }}</p>
              <p class="text-sm text-blue-600">{{ getTimeStatus(reservation.reservation_time) }}</p>
              
              <!-- Countdown for reservations starting soon -->
              <div v-if="getMinutesUntilReservation(reservation.reservation_time) <= 60 && getMinutesUntilReservation(reservation.reservation_time) > 0" 
                   class="mt-2 p-2 bg-orange-100 border border-orange-200 rounded">
                <p class="text-sm text-orange-700 font-semibold">
                  ⏰ Fillon për {{ getMinutesUntilReservation(reservation.reservation_time) }} minuta
                </p>
              </div>
            </div>

            <!-- Edit Form -->
            <div v-if="editingId === reservation.id" class="mt-4 space-y-3 p-4 bg-gray-50 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input 
                  v-model="editData.customer_name" 
                  type="text" 
                  placeholder="Emri i klientit"
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
                <input 
                  v-model="editData.customer_phone" 
                  type="text" 
                  placeholder="Telefoni"
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input 
                  v-model="editData.reservation_time" 
                  type="datetime-local" 
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
                <input 
                  v-model.number="editData.guest_count" 
                  type="number" 
                  min="1" 
                  placeholder="Numri i personave"
                  class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                />
              </div>

              <div class="flex space-x-2 pt-2">
                <button 
                  @click="submitEditRequest(reservation.id)" 
                  class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
                >
                  Dërgo Ndryshim
                </button>
                <button 
                  @click="cancelEdit" 
                  class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
                >
                  Anulo
                </button>
              </div>
            </div>

            <!-- Action Buttons -->
            <div v-else class="mt-4 flex flex-wrap gap-2">
              <button 
                @click="startEdit(reservation)" 
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
              >
                Kërko Ndryshim
              </button>
              <button 
                @click="requestDelete(reservation.id)" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors"
              >
                Kërko Fshirje
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Archived (Finished) Reservations -->
      <div v-if="activeFilter === 'all' || activeFilter === 'archived'" class="mb-8">
        <div class="flex items-center mb-4">
          <div class="flex-1 h-px bg-gray-300"></div>
          <h3 class="px-4 text-lg font-semibold text-gray-600 bg-white">
            ⚫ Arkiv - Rezervime të Përfunduara ({{ filteredArchivedReservations.length }})
          </h3>
          <div class="flex-1 h-px bg-gray-300"></div>
        </div>
        
        <div v-if="filteredArchivedReservations.length === 0" class="text-center text-gray-500 py-4">
          {{ hasActiveFilters ? 'Nuk u gjetën rezervime të përfunduara me këto filtra.' : 'Nuk ka rezervime të përfunduara.' }}
        </div>
        
        <div v-for="reservation in filteredArchivedReservations" :key="reservation.id" class="mb-4">
          <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-gray-400 bg-gray-50 opacity-75 transition-all duration-500">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <p class="text-sm text-gray-600">Klienti</p>
                <p class="font-semibold">{{ reservation.customer_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Telefoni</p>
                <p class="font-semibold">{{ reservation.customer_phone }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Tavolina</p>
                <p class="font-semibold">{{ reservation.table?.name || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Persona</p>
                <p class="font-semibold">{{ reservation.guest_count }}</p>
              </div>
            </div>
            
            <div class="mb-4">
              <p class="text-sm text-gray-600">Koha e Rezervimit</p>
              <p class="font-semibold text-lg">{{ formatDateTime(reservation.reservation_time) }}</p>
              <p class="text-sm text-gray-500">Përfunduar</p>
            </div>

            <!-- Archived Notice -->
            <div class="mt-4 p-3 bg-gray-100 rounded-lg">
              <p class="text-sm text-gray-600 italic">
                ℹ️ Ky rezervim është përfunduar dhe nuk mund të ndryshohet më.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredReservations.length === 0 && reservations.length > 0" class="text-center text-gray-500 py-12">
        <div class="text-6xl mb-4">🔍</div>
        <h3 class="text-xl font-semibold mb-2">Nuk u gjetën rezervime</h3>
        <p class="text-gray-400 mb-4">Nuk ka rezervime që përputhen me filtrat e zgjedhur.</p>
        <button 
          @click="clearAllFilters"
          class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Pastro Filtrat
        </button>
      </div>

      <div v-else-if="reservations.length === 0" class="text-center text-gray-500 py-12">
        <div class="text-6xl mb-4">📅</div>
        <h3 class="text-xl font-semibold mb-2">Nuk ka rezervime</h3>
        <p class="text-gray-400 mb-4">Nuk keni bërë asnjë rezervim ende.</p>
        <router-link 
          to="/booking" 
          class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Bëj një Rezervim
        </router-link>
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
      availableTables: [],
      loading: false,
      editingId: null,
      editData: {
        customer_name: '',
        customer_phone: '',
        reservation_time: '',
        guest_count: 1
      },
      activeFilter: 'all',
      lastUpdateTime: '',
      refreshInterval: null,
      timeUpdateInterval: null,
      newlyActiveReservations: new Set(),
      filters: {
        nameSearch: '',
        selectedTable: ''
      }
    }
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  computed: {
    pageTitle() {
      const user = JSON.parse(localStorage.getItem('user') || '{}')
      return user.role === 'admin' ? 'Rezervimet' : 'Rezervimet e Mia'
    },
    // Apply all filters to reservations
    filteredReservations() {
      let filtered = [...this.reservations]
      
      // Name search filter
      if (this.filters.nameSearch) {
        const searchTerm = this.filters.nameSearch.toLowerCase()
        filtered = filtered.filter(reservation => 
          reservation.customer_name.toLowerCase().includes(searchTerm)
        )
      }
      
      // Table filter
      if (this.filters.selectedTable) {
        filtered = filtered.filter(reservation => 
          reservation.table_id == this.filters.selectedTable
        )
      }
      
      return filtered.sort((a, b) => new Date(a.reservation_time) - new Date(b.reservation_time))
    },
    
    // Apply status filters to filtered reservations
    filteredActiveReservations() {
      const now = new Date()
      return this.filteredReservations.filter(reservation => {
        const reservationTime = new Date(reservation.reservation_time)
        const diffInHours = (now - reservationTime) / (1000 * 60 * 60)
        return diffInHours >= 0 && diffInHours <= 2
      })
    },
    
    filteredUpcomingReservations() {
      const now = new Date()
      return this.filteredReservations.filter(reservation => {
        const reservationTime = new Date(reservation.reservation_time)
        return reservationTime > now
      })
    },
    
    filteredArchivedReservations() {
      const now = new Date()
      return this.filteredReservations.filter(reservation => {
        const reservationTime = new Date(reservation.reservation_time)
        const diffInHours = (now - reservationTime) / (1000 * 60 * 60)
        return diffInHours > 2
      })
    },
    
    // Status filter buttons with filtered counts
    statusFilters() {
      return [
        { key: 'all', label: 'Të Gjitha', count: this.filteredReservations.length },
        { key: 'active', label: 'Aktive', count: this.filteredActiveReservations.length },
        { key: 'upcoming', label: 'Të Ardhshme', count: this.filteredUpcomingReservations.length },
        { key: 'archived', label: 'Arkiv', count: this.filteredArchivedReservations.length }
      ]
    },
    
    // Check if any filters are active
    hasActiveFilters() {
      return this.filters.nameSearch || this.filters.selectedTable
    }
  },
  mounted() {
    this.fetchReservations(true)
    this.fetchTables()
    this.startAutoRefresh()
    this.startTimeUpdates()
  },
  beforeUnmount() {
    this.stopAutoRefresh()
    this.stopTimeUpdates()
  },
  methods: {
    async fetchTables() {
      try {
        const res = await api.get('/tables')
        this.availableTables = res.data
      } catch (error) {
        console.error('Error fetching tables:', error)
      }
    },
    
    async fetchReservations(showLoading = true) {
      if (showLoading) {
        this.loading = true
      }
      
      try {
        const res = await api.get('/reservations')
        
        const previousActiveIds = new Set(this.filteredActiveReservations.map(r => r.id))
        this.reservations = res.data
        const currentActiveIds = new Set(this.filteredActiveReservations.map(r => r.id))
        
        const newlyActive = [...currentActiveIds].filter(id => !previousActiveIds.has(id))
        
        if (newlyActive.length > 0) {
          newlyActive.forEach(id => this.newlyActiveReservations.add(id))
          
          this.toast.success(`🎉 ${newlyActive.length} rezervim(e) u aktivizua!`, {
            timeout: 5000
          })
          
          setTimeout(() => {
            newlyActive.forEach(id => this.newlyActiveReservations.delete(id))
          }, 3000)
        }
        
        this.lastUpdateTime = new Date().toLocaleTimeString('sq-AL')
      } catch (error) {
        console.error('Error fetching reservations:', error)
        
        if (showLoading) {
          if (error.response) {
            const status = error.response.status
            const message = error.response.data?.message || 'Unknown server error'
            
            if (status === 500) {
              this.toast.error(`Server error: ${message}`)
            } else if (status === 401) {
              this.toast.error('Authentication failed. Please login again.')
              this.$router.push('/login')
            } else if (status === 403) {
              this.toast.error('Access denied.')
            } else {
              this.toast.error(`Error ${status}: ${message}`)
            }
          } else if (error.request) {
            this.toast.error('Cannot connect to server.')
          } else {
            this.toast.error('An unexpected error occurred.')
          }
        }
      } finally {
        if (showLoading) {
          this.loading = false
        }
      }
    },
    
    clearAllFilters() {
      this.filters = {
        nameSearch: '',
        selectedTable: ''
      }
    },
    
    getTableName(tableId) {
      const table = this.availableTables.find(t => t.id == tableId)
      return table ? table.name : 'N/A'
    },
    
    startAutoRefresh() {
      this.refreshInterval = setInterval(() => {
        this.fetchReservations(false)
      }, 30000)
    },
    
    stopAutoRefresh() {
      if (this.refreshInterval) {
        clearInterval(this.refreshInterval)
        this.refreshInterval = null
      }
    },
    
    startTimeUpdates() {
      this.timeUpdateInterval = setInterval(() => {
        this.$forceUpdate()
      }, 60000)
    },
    
    stopTimeUpdates() {
      if (this.timeUpdateInterval) {
        clearInterval(this.timeUpdateInterval)
        this.timeUpdateInterval = null
      }
    },
    
    isNewlyActive(reservation) {
      return this.newlyActiveReservations.has(reservation.id)
    },
    
    getMinutesUntilReservation(dateTime) {
      const now = new Date()
      const reservationTime = new Date(dateTime)
      const diffInMinutes = Math.ceil((reservationTime - now) / (1000 * 60))
      return Math.max(0, diffInMinutes)
    },
    
    formatDateTime(dateTime) {
      const date = new Date(dateTime)
      return date.toLocaleString('sq-AL', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    
    getTimeStatus(dateTime) {
      const now = new Date()
      const reservationTime = new Date(dateTime)
      const diffInHours = (reservationTime - now) / (1000 * 60 * 60)
      
      if (diffInHours < -2) {
        return 'Përfunduar'
      } else if (diffInHours < 0) {
        return 'Aktive (në vazhdim)'
      } else if (diffInHours < 24) {
        return `${Math.ceil(diffInHours)} orë të mbetura`
      } else {
        const days = Math.ceil(diffInHours / 24)
        return `${days} ditë të mbetura`
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
      } catch (error) {
        console.error('Error submitting edit request:', error)
        this.toast.error('Gabim gjatë dërgimit të kërkesës')
      }
    },
    
    async requestDelete(reservationId) {
      if (confirm('A je i sigurt që dëshiron të kërkosh fshirjen e këtij rezervimi?')) {
        try {
          await api.post('/change-requests', {
            reservation_id: reservationId,
            type: 'delete'
          })
          this.toast.success('Kërkesa për fshirje u dërgua!')
        } catch (error) {
          console.error('Error requesting delete:', error)
          this.toast.error('Gabim gjatë dërgimit të kërkesës për fshirje')
        }
      }
    }
  }
}
</script>

<style scoped>
.reservation-card {
  transition: all 0.3s ease;
}

.reservation-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

@keyframes slideIn {
  from {
    transform: translateX(-100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slide-in {
  animation: slideIn 0.5s ease-out;
}
</style>
