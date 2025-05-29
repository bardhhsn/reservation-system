// src/router.js
import { createRouter, createWebHistory } from 'vue-router'
import Login from './views/Login.vue'
import RegisterPage from './views/RegisterPage.vue'
import CreateReservation from './views/CreateReservation.vue'
import ReservationList from './views/ReservationList.vue'
import AdminDashboard from './views/AdminDashboard.vue'
import MyRequests from './views/MyRequests.vue'
import AdminStatistics from './views/AdminStatistics.vue'

const routes = [
  { path: '/', redirect: '/create' },

  // give these routes names
  { path: '/login',              name: 'Login',      component: Login },
  { path: '/register',           name: 'Register',   component: RegisterPage },

  { path: '/create',             name: 'Create',     component: CreateReservation },
  { path: '/list',               name: 'List',       component: ReservationList },
  { path: '/my-requests',        name: 'MyRequests', component: MyRequests },

  // admin
  { path: '/admin',              name: 'Admin',      component: AdminDashboard },
  { path: '/admin/statistics',   name: 'Statistics', component: AdminStatistics },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
