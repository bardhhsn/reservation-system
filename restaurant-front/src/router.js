import { createRouter, createWebHistory } from "vue-router"
import RestaurantHome from "./views/RestaurantHome.vue"
import Login from "./views/Login.vue"
import RegisterPage from "./views/RegisterPage.vue"
import CreateReservation from "./views/CreateReservation.vue"
import ReservationList from "./views/ReservationList.vue"
import AdminDashboard from "./views/AdminDashboard.vue"
import MyRequests from "./views/MyRequests.vue"
import AdminStatistics from "./views/AdminStatistics.vue"

const routes = [
  // Landing page route
  {
    path: "/",
    name: "Home",
    component: RestaurantHome,
  },

  // Authentication routes
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { requiresGuest: true },
  },
  {
    path: "/register",
    name: "Register",
    component: RegisterPage,
    meta: { requiresGuest: true },
  },

  // Reservation system routes (protected)
  {
    path: "/booking",
    name: "Booking",
    component: CreateReservation,
    meta: { requiresAuth: true },
  },
  {
    path: "/reservations",
    name: "Reservations",
    component: ReservationList,
    meta: { requiresAuth: true },
  },
  {
    path: "/my-requests",
    name: "MyRequests",
    component: MyRequests,
    meta: { requiresAuth: true },
  },

  // Admin routes
  {
    path: "/admin",
    name: "Admin",
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/statistics",
    name: "Statistics",
    component: AdminStatistics,
    meta: { requiresAuth: true, requiresAdmin: true },
  },

  // Legacy routes (redirect to new structure)
  {
    path: "/create",
    redirect: "/booking",
  },
  {
    path: "/list",
    redirect: "/reservations",
  },

  // Catch all route - redirect to home
  {
    path: "/:pathMatch(.*)*",
    redirect: "/",
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token")
  const user = JSON.parse(localStorage.getItem("user") || "{}")
  const isAuthenticated = !!token
  const isAdmin = user.role === "admin"

  // Check if route requires authentication
  if (to.meta.requiresAuth && !isAuthenticated) {
    // Store the intended destination
    sessionStorage.setItem("redirectAfterLogin", to.fullPath)
    next("/login")
    return
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && isAuthenticated) {
    next("/booking")
    return
  }

  // Check if route requires admin
  if (to.meta.requiresAdmin && !isAdmin) {
    next("/booking")
    return
  }

  next()
})

export default router
