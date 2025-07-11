import { createApp } from "vue"
import App from "./App.vue"
import router from "./router"
import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"
import axios from "axios"
import "./assets/tailwind.css"

// ✅ Vendos bazën për API pa /api
axios.defaults.baseURL = "http://localhost:8000"
axios.defaults.withCredentials = true

// ✅ Vendos token automatikisht në header nëse ekziston
const token = localStorage.getItem("token")
if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`
}

// Enhanced toast options
const toastOptions = {
  position: "top-right",
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: "button",
  icon: true,
  rtl: false,
}

const app = createApp(App)

app.use(router)
app.use(Toast, toastOptions)

// Global state management for auth
app.config.globalProperties.$authState = {
  isLoggedIn: !!localStorage.getItem("token"),
  user: localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : null,

  updateAuthState() {
    this.isLoggedIn = !!localStorage.getItem("token")
    this.user = localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : null
  },

  clearAuthState() {
    this.isLoggedIn = false
    this.user = null
  },
}

app.mount("#app")
