import { createRouter, createWebHistory } from 'vue-router'
import Login from './views/Login.vue'
import Register from './views/Register.vue'
import CreateReservation from './views/CreateReservation.vue'
import ReservationList from './views/ReservationList.vue'
import AdminDashboard from './views/AdminDashboard.vue'

const routes = [
  { path: '/', redirect: '/create' },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/create', component: CreateReservation },
  { path: '/list', component: ReservationList },
  { path: '/admin', component: AdminDashboard }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Middleware për mbrojtje të ruterave
router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/register']
  const authRequired = !publicPages.includes(to.path)
  const token = localStorage.getItem('token')

  if (authRequired && !token) {
    return next('/login')
  }

  // Lejo vetëm admin në /admin
  if (to.path === '/admin') {
    const user = JSON.parse(localStorage.getItem('user'))
    if (!user || user.role !== 'admin') {
      return next('/create')
    }
  }

  next()
})

export default router
