<template>
  <div id="app">
    <!-- Header -->
    <header class="header">
      <div class="header-top">
        <div class="container">
          <div class="header-content">
            <div class="contact-info">
              <span class="tagline">The best place to eat in downtown Porto!</span>
              <a href="tel:1234567890" class="phone">(800) 123-4567</a>
            </div>
            <div class="hours">
              <i class="fas fa-clock"></i>
              <span>Mon - Sat 9:00am - 6:00pm</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="header-main">
        <div class="container">
          <div class="header-content">
            <div class="logo">
              <img src="/placeholder.svg?height=48&width=123" alt="Porto Restaurant" />
            </div>
            
            <nav class="navigation" :class="{ 'mobile-open': mobileMenuOpen }">
              <ul>
                <li><a href="#home" :class="{ active: activeSection === 'home' }" @click="scrollToSection('home')">Home</a></li>
                <li><a href="#menu" :class="{ active: activeSection === 'menu' }" @click="scrollToSection('menu')">Menu</a></li>
                <li><a href="#about" :class="{ active: activeSection === 'about' }" @click="scrollToSection('about')">About</a></li>
                <li><a href="#blog" :class="{ active: activeSection === 'blog' }" @click="scrollToSection('blog')">Blog</a></li>
                <li><a href="#contact" :class="{ active: activeSection === 'contact' }" @click="scrollToSection('contact')">Contact</a></li>
              </ul>
            </nav>
            
            <div class="header-actions">
              <button @click="bookTable" class="book-table-btn">Book a Table</button>
              <button class="search-btn">
                <i class="fas fa-search"></i>
              </button>
              <a href="https://www.instagram.com" target="_blank" class="social-link">
                <i class="fab fa-instagram"></i>
              </a>
              
              <!-- Auth buttons for non-authenticated users -->
              <div v-if="!isLoggedIn" class="auth-buttons">
                <router-link to="/login" class="login-btn">Login</router-link>
                <router-link to="/register" class="register-btn">Register</router-link>
              </div>
              
              <!-- User menu for authenticated users -->
              <div v-if="isLoggedIn" class="user-menu">
                <div class="dropdown" @click="toggleUserDropdown">
                  <span class="user-name">{{ currentUser?.name }}</span>
                  <i class="fas fa-chevron-down"></i>
                  <div class="dropdown-content" :class="{ show: userDropdownOpen }">
                    <router-link to="/reservations" @click="userDropdownOpen = false">My Reservations</router-link>
                    <router-link to="/my-requests" @click="userDropdownOpen = false">My Requests</router-link>
                    <router-link v-if="isAdmin" to="/admin" @click="userDropdownOpen = false">Admin Panel</router-link>
                    <a href="#" @click="logout">Logout</a>
                  </div>
                </div>
              </div>
              
              <button @click="toggleMobileMenu" class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
      <div class="hero-background">
        <img src="/placeholder.svg?height=800&width=1920" alt="Restaurant Background" />
        <div class="hero-overlay"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <div class="welcome-text">
            <div class="line"></div>
            <span>WELCOME TO</span>
            <div class="line"></div>
          </div>
          <h1>Porto Restaurant</h1>
          <p>The best place to eat in downtown Porto!</p>
          <button @click="scrollToSection('menu')" class="view-menu-btn">VIEW MENU</button>
        </div>
      </div>
      <button @click="scrollToSection('about')" class="scroll-down">
        <i class="fas fa-chevron-down"></i>
      </button>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
      <div class="container">
        <div class="about-content">
          <div class="about-images">
            <div class="image-large">
              <img src="/placeholder.svg?height=400&width=300" alt="Restaurant Interior" />
            </div>
            <div class="image-small-group">
              <img src="/placeholder.svg?height=180&width=200" alt="Restaurant Interior 2" />
              <img src="/placeholder.svg?height=180&width=200" alt="Restaurant Interior 3" />
            </div>
          </div>
          <div class="about-text">
            <h2>ABOUT US</h2>
            <h3>Using the very <span class="highlight">best ingredients</span> we have access to.</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque molestie vel turpis a sodales. In hac habitasse platea dictumst. Nulla sollicitudin dui vitae leo aliquet.</p>
            <button class="learn-more-btn">More About Us</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
      <div class="testimonials-background">
        <img src="/placeholder.svg?height=600&width=1920" alt="Background" />
        <div class="testimonials-overlay"></div>
      </div>
      <div class="container">
        <div class="testimonials-content">
          <div class="stars">
            <i v-for="i in 5" :key="i" class="fas fa-star"></i>
          </div>
          <div class="testimonial-card">
            <div class="testimonial-icon">
              <img src="/placeholder.svg?height=56&width=56" alt="TripAdvisor" />
            </div>
            <blockquote>
              <p>Cras a elit sit amet leo accumsan volutpat. Suspendisse hendreriast ehicula leo, vel efficitur felis ultrices non. Cras a elit sit amet leo acun volutpat.</p>
            </blockquote>
            <cite>TRIP ADVISOR - NOV 2020</cite>
            <div class="author">
              <strong>John Doe</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="menu">
      <div class="container">
        <div class="section-header">
          <h2>WHAT WE OFFER</h2>
          <h3>Our Menu</h3>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. <span class="highlight">Pellentesque molestie vel turpis a sodales.</span> In hac habitasse platea dictumst.</p>
        </div>
        
        <div class="menu-content">
          <div class="menu-column">
            <h4>APPETIZERS</h4>
            <hr />
            <div class="menu-items">
              <div v-for="item in appetizers" :key="item.id" class="menu-item">
                <div class="menu-item-header">
                  <h5>{{ item.name }}</h5>
                  <div class="menu-line"></div>
                  <span class="price">${{ item.price }}</span>
                </div>
                <p>{{ item.description }}</p>
              </div>
            </div>
          </div>
          
          <div class="menu-column">
            <h4>MAIN MENU</h4>
            <hr />
            <div class="menu-items">
              <div v-for="item in mains" :key="item.id" class="menu-item">
                <div class="menu-item-header">
                  <h5>{{ item.name }}</h5>
                  <div class="menu-line"></div>
                  <span class="price">${{ item.price }}</span>
                </div>
                <p>{{ item.description }}</p>
              </div>
            </div>
            
            <h4 class="dessert-title">DESSERT</h4>
            <hr />
            <div class="menu-items">
              <div v-for="item in desserts" :key="item.id" class="menu-item">
                <div class="menu-item-header">
                  <h5>{{ item.name }}</h5>
                  <div class="menu-line"></div>
                  <span class="price">${{ item.price }}</span>
                </div>
                <p>{{ item.description }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="menu-footer">
          <button @click="bookTable" class="view-full-menu-btn">Book a Table</button>
        </div>
      </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery">
      <div class="container">
        <div class="section-header">
          <h2>INSIDE PORTO</h2>
          <h3>Our Gallery</h3>
        </div>
        <div class="gallery-grid">
          <div v-for="i in 5" :key="i" class="gallery-item">
            <img :src="`/placeholder.svg?height=250&width=300`" :alt="`Gallery Image ${i}`" />
          </div>
        </div>
      </div>
    </section>

    <!-- Chef Section -->
    <section class="chef">
      <div class="chef-background">
        <img src="/placeholder.svg?height=200&width=200" alt="Food Background Left" class="bg-left" />
        <img src="/placeholder.svg?height=200&width=200" alt="Food Background Right" class="bg-right" />
      </div>
      <div class="container">
        <div class="chef-content">
          <div class="chef-image">
            <img src="/placeholder.svg?height=300&width=300" alt="Chef" />
          </div>
          <div class="chef-quote">
            <blockquote>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque molestie vel turpis a sodales. In hac habitasse platea dictumst. Nulla <span class="highlight">sollicitudin dui vitae leo aliquet, vel feugiat</span> augue pellentesque. In nec augue.</p>
            </blockquote>
            <div class="chef-signature">
              <strong>JOHN DOE - CHEF</strong>
              <img src="/placeholder.svg?height=40&width=120" alt="Signature" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog Section -->
    <section id="blog" class="blog">
      <div class="container">
        <div class="section-header">
          <h2>RECENT NEWS</h2>
          <h3>Our Blog</h3>
        </div>
        <div class="blog-grid">
          <article v-for="post in blogPosts" :key="post.id" class="blog-card">
            <div class="blog-image">
              <img :src="post.image" :alt="post.title" />
              <div class="blog-date">
                <span class="day">{{ post.day }}</span>
                <span class="month">{{ post.month }}</span>
              </div>
            </div>
            <div class="blog-content">
              <span class="blog-category">{{ post.category }}</span>
              <h4>{{ post.title }}</h4>
              <a href="#" class="read-more">View More</a>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
      <div class="container">
        <div class="contact-content">
          <div class="contact-info">
            <h2>A Great Place to Eat!</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque molestie vel turpis a sodales. In hac habitasse platea dictumst.</p>
            
            <div class="contact-details">
              <h3>123 Street Name, Los Angeles, CA</h3>
              <a href="#" class="directions">GET DIRECTIONS</a>
              
              <div class="contact-list">
                <div class="contact-item">
                  <i class="fas fa-phone"></i>
                  <a href="tel:+1234567890">(800) 123-4567</a>
                </div>
                <div class="contact-item">
                  <i class="fas fa-envelope"></i>
                  <a href="mailto:porto@domain.com">porto@domain.com</a>
                </div>
              </div>
              
              <p>We are here to help you with your dining needs. Come in, give us a call or send us an email. We will get back to you as soon as we can during regular business hours.</p>
            </div>
          </div>
          
          <div class="contact-images">
            <div class="contact-image-large">
              <img src="/placeholder.svg?height=400&width=350" alt="Restaurant Interior" />
            </div>
            <div class="contact-image-small">
              <img src="/placeholder.svg?height=180&width=200" alt="Restaurant Interior 2" />
              <img src="/placeholder.svg?height=180&width=200" alt="Restaurant Interior 3" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-center">
            <img src="/placeholder.svg?height=48&width=123" alt="Porto Restaurant" />
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque molestie vel turpis a sodales.</p>
          </div>
          
          <div class="footer-columns">
            <div class="footer-contact">
              <div class="footer-contact-item">
                <i class="fas fa-phone"></i>
                <a href="tel:+1234567890">(800) 123-4567</a>
              </div>
              <div class="footer-contact-item">
                <i class="fas fa-envelope"></i>
                <a href="mailto:porto@domain.com">porto@domain.com</a>
              </div>
              <div class="footer-contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <p>123 Street Name, New York, NY 10000, USA</p>
              </div>
            </div>
            
            <div class="footer-map">
              <div class="map-placeholder">
                <img src="/placeholder.svg?height=190&width=400" alt="Map" />
              </div>
              <a href="#" class="directions-link">Get Directions</a>
            </div>
            
            <div class="footer-links">
              <h4>Useful Links</h4>
              <ul>
                <li><a href="#contact">Contact Us</a></li>
                <li><a href="#menu">Our Menu</a></li>
                <li><a href="#">Payment Methods</a></li>
                <li><a href="#blog">Our Blog</a></li>
                <li><a href="#">FAQs</a></li>
              </ul>
            </div>
          </div>
        </div>
        
        <div class="footer-bottom">
          <p>Porto Restaurant © 2025. All Rights Reserved.</p>
        </div>
      </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button @click="scrollToTop" class="scroll-to-top" :class="{ visible: showScrollTop }">
      <i class="fas fa-chevron-up"></i>
    </button>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'RestaurantHome',
  data() {
    return {
      mobileMenuOpen: false,
      activeSection: 'home',
      showScrollTop: false,
      userDropdownOpen: false,
      
      // Reactive auth state
      authState: {
        isLoggedIn: !!localStorage.getItem('token'),
        user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
      },
      
      appetizers: [
        { id: 1, name: 'Whitefish Or Lox & Cream Cheese', price: '16', description: 'Includes bread and butter' },
        { id: 2, name: 'Canned Tuna', price: '10', description: 'Includes bread and butter' },
        { id: 3, name: 'Creamed Herring', price: '8.50', description: 'Includes bread and butter' },
        { id: 4, name: 'Whitefish Or Lox + Cream Cheese', price: '16', description: 'Includes bread and butter' },
        { id: 5, name: 'Canned Tuna', price: '10', description: 'Includes bread and butter' }
      ],
      
      mains: [
        { id: 1, name: 'Platter Of Soft Tender Hot Pastrami', price: '32', description: 'Served with chopped liver, swiss cheese, french fries, baked beans, cole slaw, tomato and pickle' },
        { id: 2, name: 'Hot Pastrami', price: '15', description: 'Sauerkraut and nippy cheese grilled on rye' },
        { id: 3, name: 'Chicken In The Pot', price: '42', description: 'Boiled chicken with matzo ball, noodles, vegetable and delicious broth - all in the pot' }
      ],
      
      desserts: [
        { id: 1, name: 'Apple Pie', price: '9', description: 'With Brandy Sauce' },
        { id: 2, name: 'Cheesecake', price: '7', description: 'Strawberry Shortcake With Ice Cream' },
        { id: 3, name: 'Apple Strudel', price: '8', description: 'Special Apple Strudel' }
      ],
      
      blogPosts: [
        {
          id: 1,
          title: 'Lorem ipsum dolor sit a met, consectetur',
          category: 'FOOD',
          day: '18',
          month: 'FEB',
          image: '/placeholder.svg?height=250&width=350'
        },
        {
          id: 2,
          title: 'Lorem ipsum dolor sit a met, consectetur',
          category: 'FOOD',
          day: '15',
          month: 'FEB',
          image: '/placeholder.svg?height=250&width=350'
        },
        {
          id: 3,
          title: 'Lorem ipsum dolor sit a met, consectetur',
          category: 'FOOD',
          day: '12',
          month: 'FEB',
          image: '/placeholder.svg?height=250&width=350'
        }
      ]
    }
  },
  
  computed: {
    isLoggedIn() {
      return this.authState.isLoggedIn
    },
    isAdmin() {
      return this.authState.user?.role === 'admin'
    },
    currentUser() {
      return this.authState.user
    }
  },
  
  mounted() {
    window.addEventListener('scroll', this.handleScroll)
    window.addEventListener('click', this.handleClickOutside)
    window.addEventListener('storage', this.handleStorageChange)
    window.addEventListener('authStateChanged', this.updateAuthState)
    this.handleScroll()
    
    // Add Font Awesome to the document
    if (!document.getElementById('font-awesome-css')) {
      const link = document.createElement('link')
      link.id = 'font-awesome-css'
      link.rel = 'stylesheet'
      link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
      document.head.appendChild(link)
    }
  },
  
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll)
    window.removeEventListener('click', this.handleClickOutside)
    window.removeEventListener('storage', this.handleStorageChange)
    window.removeEventListener('authStateChanged', this.updateAuthState)
  },
  
  methods: {
    bookTable() {
      console.log('Book table clicked, isLoggedIn:', this.isLoggedIn) // Debug log
      
      if (this.isLoggedIn) {
        // If user is logged in, go directly to booking page
        this.$router.push('/booking')
      } else {
        // If not logged in, store intended destination and go to login
        sessionStorage.setItem('redirectAfterLogin', '/booking')
        this.$router.push('/login')
      }
    },
    
    logout() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      this.userDropdownOpen = false
      this.updateAuthState()
      this.$router.push('/')
      
      // Emit custom event
      window.dispatchEvent(new CustomEvent('authStateChanged'))
    },
    
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen
    },
    
    toggleUserDropdown() {
      this.userDropdownOpen = !this.userDropdownOpen
    },
    
    handleClickOutside(event) {
      if (!event.target.closest('.dropdown')) {
        this.userDropdownOpen = false
      }
    },
    
    scrollToSection(sectionId) {
      const element = document.getElementById(sectionId)
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' })
        this.activeSection = sectionId
        this.mobileMenuOpen = false
      }
    },
    
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    
    handleScroll() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop
      this.showScrollTop = scrollTop > 300
      
      // Update active section based on scroll position
      const sections = ['home', 'about', 'menu', 'blog', 'contact']
      for (const section of sections) {
        const element = document.getElementById(section)
        if (element) {
          const rect = element.getBoundingClientRect()
          if (rect.top <= 100 && rect.bottom >= 100) {
            this.activeSection = section
            break
          }
        }
      }
    },
    updateAuthState() {
      this.authState.isLoggedIn = !!localStorage.getItem('token')
      this.authState.user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
      console.log('Auth state updated:', this.authState) // Debug log
    },
  }
}
</script>

<style scoped>
/* All the existing styles from your original App.vue */
/* Global Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', sans-serif;
  line-height: 1.6;
  color: #333;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Header Styles */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: white;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header-top {
  background: #2c3e50;
  color: white;
  padding: 10px 0;
  font-size: 14px;
}

.header-top .header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.contact-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.phone {
  color: white;
  text-decoration: none;
  font-weight: bold;
}

.hours {
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-main {
  padding: 15px 0;
}

.header-main .header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo img {
  height: 48px;
}

.navigation ul {
  display: flex;
  list-style: none;
  gap: 30px;
}

.navigation a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  text-transform: capitalize;
  transition: color 0.3s;
}

.navigation a:hover,
.navigation a.active {
  color: #e74c3c;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.book-table-btn {
  background: #2c3e50;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.book-table-btn:hover {
  background: #34495e;
}

.search-btn,
.social-link,
.mobile-menu-btn {
  background: none;
  border: none;
  color: #333;
  cursor: pointer;
  padding: 8px;
  transition: color 0.3s;
  font-size: 18px;
}

.search-btn:hover,
.social-link:hover,
.mobile-menu-btn:hover {
  color: #e74c3c;
}

.mobile-menu-btn {
  display: none;
}

/* Auth buttons */
.auth-buttons {
  display: flex;
  gap: 10px;
  align-items: center;
}

.login-btn {
  color: #2c3e50;
  text-decoration: none;
  font-weight: 600;
  padding: 8px 16px;
  transition: color 0.3s;
}

.login-btn:hover {
  color: #e74c3c;
}

.register-btn {
  background: #e74c3c;
  color: white;
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 4px;
  font-weight: 600;
  transition: background 0.3s;
}

.register-btn:hover {
  background: #c0392b;
}

/* User dropdown */
.user-menu {
  position: relative;
}

.dropdown {
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 4px;
  transition: background 0.3s;
}

.dropdown:hover {
  background: #f8f9fa;
}

.user-name {
  font-weight: 600;
  color: #2c3e50;
}

.dropdown-content {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  min-width: 180px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  border-radius: 4px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s;
  z-index: 1000;
}

.dropdown-content.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-content a {
  display: block;
  padding: 12px 16px;
  color: #333;
  text-decoration: none;
  transition: background 0.3s;
}

.dropdown-content a:hover {
  background: #f8f9fa;
  color: #e74c3c;
}

/* Hero Section */
.hero {
  position: relative;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
  overflow: hidden;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: -2;
}

.hero-background img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: -1;
}

.hero-content h1 {
  font-size: 4rem;
  font-weight: bold;
  margin: 20px 0;
  letter-spacing: 2px;
}

.hero-content p {
  font-size: 1.2rem;
  margin-bottom: 30px;
  opacity: 0.9;
}

.welcome-text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  color: #e74c3c;
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 2px;
  margin-bottom: 20px;
}

.line {
  width: 50px;
  height: 2px;
  background: #e74c3c;
}

.view-menu-btn {
  background: transparent;
  color: white;
  border: 2px solid white;
  padding: 15px 30px;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 2px;
  cursor: pointer;
  transition: all 0.3s;
}

.view-menu-btn:hover {
  background: white;
  color: #333;
}

.scroll-down {
  position: absolute;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  animation: bounce 2s infinite;
  font-size: 24px;
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
  40% { transform: translateX(-50%) translateY(-10px); }
  60% { transform: translateX(-50%) translateY(-5px); }
}

/* About Section */
.about {
  padding: 100px 0;
}

.about-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}

.about-images {
  display: flex;
  gap: 20px;
}

.image-large img {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.image-small-group {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.image-small-group img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.about-text h2 {
  color: #e74c3c;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 3px;
  margin-bottom: 10px;
}

.about-text h3 {
  font-size: 2.5rem;
  font-weight: 500;
  line-height: 1.2;
  margin-bottom: 20px;
  color: #2c3e50;
}

.highlight {
  background: rgba(231, 76, 60, 0.1);
  color: #e74c3c;
  padding: 2px 8px;
  border-radius: 4px;
}

.about-text p {
  font-size: 1.1rem;
  line-height: 1.8;
  margin-bottom: 30px;
  color: #666;
}

.learn-more-btn {
  background: #2c3e50;
  color: white;
  border: none;
  padding: 15px 30px;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s;
}

.learn-more-btn:hover {
  background: #34495e;
}

/* Testimonials Section */
.testimonials {
  position: relative;
  padding: 100px 0;
  color: white;
  text-align: center;
}

.testimonials-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: -2;
}

.testimonials-background img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.testimonials-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.7);
  z-index: -1;
}

.stars {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 30px;
  opacity: 0.6;
}

.stars i {
  font-size: 20px;
}

.testimonial-card {
  background: white;
  color: #333;
  padding: 50px;
  border-radius: 8px;
  max-width: 800px;
  margin: 0 auto;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.testimonial-icon {
  margin-bottom: 20px;
}

.testimonial-card blockquote p {
  font-size: 1.2rem;
  line-height: 1.8;
  margin-bottom: 20px;
  font-style: italic;
}

.testimonial-card cite {
  color: #999;
  font-size: 0.9rem;
  font-style: normal;
  letter-spacing: 1px;
}

.author {
  margin-top: 20px;
}

.author strong {
  font-size: 1.1rem;
  color: #2c3e50;
}

/* Menu Section */
.menu {
  padding: 100px 0;
  background: #f8f9fa;
}

.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.section-header h2 {
  color: #e74c3c;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 3px;
  margin-bottom: 10px;
}

.section-header h3 {
  font-size: 2.5rem;
  font-weight: 500;
  margin-bottom: 20px;
  color: #2c3e50;
}

.section-header p {
  font-size: 1.1rem;
  color: #666;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.8;
}

.menu-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  margin-bottom: 50px;
}

.menu-column h4 {
  color: #2c3e50;
  font-size: 1.2rem;
  font-weight: bold;
  letter-spacing: 3px;
  margin-bottom: 15px;
}

.menu-column hr {
  border: none;
  height: 2px;
  background: #e0e0e0;
  margin-bottom: 30px;
}

.dessert-title {
  margin-top: 50px !important;
}

.menu-item {
  margin-bottom: 25px;
}

.menu-item-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.menu-item-header h5 {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
  white-space: nowrap;
}

.menu-line {
  flex: 1;
  height: 1px;
  background: repeating-linear-gradient(
    to right,
    #ccc 0,
    #ccc 4px,
    transparent 4px,
    transparent 8px
  );
  margin: 0 15px;
}

.price {
  font-size: 1rem;
  font-weight: bold;
  color: #2c3e50;
  white-space: nowrap;
}

.menu-item p {
  font-size: 0.9rem;
  color: #666;
  line-height: 1.6;
}

.menu-footer {
  text-align: center;
}

.view-full-menu-btn {
  background: #2c3e50;
  color: white;
  border: none;
  padding: 15px 40px;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s;
}

.view-full-menu-btn:hover {
  background: #34495e;
}

/* Gallery Section */
.gallery {
  padding: 100px 0;
  background: #f0f0f0;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.gallery-item {
  overflow: hidden;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.gallery-item:hover {
  transform: translateY(-5px);
}

.gallery-item img {
  width: 100%;
  height: 250px;
  object-fit: cover;
  transition: transform 0.3s;
}

.gallery-item:hover img {
  transform: scale(1.05);
}

/* Chef Section */
.chef {
  position: relative;
  padding: 100px 0;
  border-top: 1px solid #e0e0e0;
  border-bottom: 1px solid #e0e0e0;
}

.chef-background .bg-left {
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0.1;
}

.chef-background .bg-right {
  position: absolute;
  top: 0;
  right: 0;
  opacity: 0.1;
}

.chef-content {
  display: flex;
  align-items: center;
  gap: 60px;
}

.chef-image img {
  width: 300px;
  height: 300px;
  object-fit: cover;
  border-radius: 50%;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.chef-quote {
  flex: 1;
}

.chef-quote blockquote p {
  font-size: 1.2rem;
  line-height: 1.8;
  color: #2c3e50;
  margin-bottom: 30px;
  font-style: italic;
}

.chef-signature strong {
  display: block;
  font-size: 0.9rem;
  font-weight: bold;
  letter-spacing: 2px;
  color: #2c3e50;
  margin-bottom: 15px;
}

.chef-signature img {
  height: 40px;
}

/* Blog Section */
.blog {
  padding: 100px 0;
}

.blog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 40px;
}

.blog-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.blog-card:hover {
  transform: translateY(-5px);
}

.blog-image {
  position: relative;
  overflow: hidden;
}

.blog-image img {
  width: 100%;
  height: 250px;
  object-fit: cover;
  transition: transform 0.3s;
}

.blog-card:hover .blog-image img {
  transform: scale(1.05);
}

.blog-date {
  position: absolute;
  bottom: 20px;
  right: 20px;
  background: #e74c3c;
  color: white;
  padding: 10px 15px;
  border-radius: 4px;
  text-align: center;
  font-weight: bold;
}

.blog-date .day {
  display: block;
  font-size: 1.5rem;
  line-height: 1;
}

.blog-date .month {
  display: block;
  font-size: 0.8rem;
  letter-spacing: 1px;
}

.blog-content {
  padding: 30px;
}

.blog-category {
  color: #999;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 2px;
  margin-bottom: 10px;
}

.blog-content h4 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 15px;
  line-height: 1.4;
}

.read-more {
  color: #e74c3c;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
}

.read-more:hover {
  text-decoration: underline;
}

/* Contact Section */
.contact {
  padding: 100px 0;
  background: #f8f9fa;
}

.contact-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: start;
}

.contact-info h2 {
  font-size: 2.5rem;
  font-weight: 500;
  color: #2c3e50;
  margin-bottom: 20px;
}

.contact-info > p {
  font-size: 1.1rem;
  color: #666;
  line-height: 1.8;
  margin-bottom: 40px;
}

.contact-details h3 {
  font-size: 1.5rem;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 10px;
}

.directions {
  color: #e74c3c;
  text-decoration: underline;
  font-weight: bold;
  margin-bottom: 30px;
  display: inline-block;
}

.contact-list {
  margin: 30px 0;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}

.contact-item i {
  font-size: 20px;
  color: #2c3e50;
}

.contact-item a {
  color: #2c3e50;
  text-decoration: none;
  font-weight: bold;
}

.contact-item a:hover {
  color: #e74c3c;
}

.contact-images {
  display: flex;
  gap: 20px;
}

.contact-image-large img {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.contact-image-small {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.contact-image-small img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* Footer */
.footer {
  background: #2c3e50;
  color: white;
  padding: 60px 0 20px;
}

.footer-content {
  margin-bottom: 40px;
}

.footer-center {
  text-align: center;
  margin-bottom: 50px;
}

.footer-center img {
  height: 48px;
  margin-bottom: 20px;
}

.footer-center p {
  max-width: 500px;
  margin: 0 auto;
  color: #bdc3c7;
  line-height: 1.8;
}

.footer-columns {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 40px;
}

.footer-contact-item {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}

.footer-contact-item i {
  font-size: 20px;
}

.footer-contact-item a {
  color: white;
  text-decoration: none;
  font-weight: bold;
}

.footer-contact-item a:hover {
  color: #e74c3c;
}

.footer-contact-item p {
  margin: 0;
  font-weight: bold;
}

.map-placeholder {
  margin-bottom: 15px;
}

.map-placeholder img {
  width: 100%;
  height: 190px;
  object-fit: cover;
  border-radius: 4px;
}

.directions-link {
  color: #e74c3c;
  text-decoration: none;
  font-weight: bold;
}

.footer-links h4 {
  font-size: 1.1rem;
  font-weight: bold;
  margin-bottom: 20px;
  text-align: right;
}

.footer-links ul {
  list-style: none;
  text-align: right;
}

.footer-links li {
  margin-bottom: 10px;
}

.footer-links a {
  color: #bdc3c7;
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: #e74c3c;
}

.footer-bottom {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #34495e;
  color: #bdc3c7;
}

/* Scroll to Top Button */
.scroll-to-top {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: #e74c3c;
  color: white;
  border: none;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  cursor: pointer;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s;
  z-index: 1000;
  font-size: 20px;
}

.scroll-to-top.visible {
  opacity: 1;
  visibility: visible;
}

.scroll-to-top:hover {
  background: #c0392b;
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .mobile-menu-btn {
    display: block;
  }
  
  .navigation {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s;
  }
  
  .navigation.mobile-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }
  
  .navigation ul {
    flex-direction: column;
    padding: 20px;
    gap: 15px;
  }
  
  .book-table-btn,
  .auth-buttons,
  .user-menu {
    display: none;
  }
  
  .hero-content h1 {
    font-size: 2.5rem;
  }
  
  .about-content,
  .menu-content,
  .chef-content,
  .contact-content {
    grid-template-columns: 1fr;
    gap: 40px;
  }
  
  .about-images {
    flex-direction: column;
  }
  
  .footer-columns {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .footer-links h4,
  .footer-links ul {
    text-align: center;
  }
  
  .blog-grid {
    grid-template-columns: 1fr;
  }
  
  .gallery-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
  
  .contact-images {
    flex-direction: column;
  }
  
  .header-top .contact-info {
    flex-direction: column;
    gap: 10px;
  }
  
  .tagline {
    display: none;
  }
}

@media (max-width: 480px) {
  .hero-content h1 {
    font-size: 2.5rem;
  }
  
  .section-header h3 {
    font-size: 2rem;
  }
  
  .about-text h3 {
    font-size: 2rem;
  }
  
  .contact-info h2 {
    font-size: 2rem;
  }
  
  .welcome-text {
    flex-direction: column;
    gap: 10px;
  }
  
  .line {
    width: 30px;
  }
}
</style>
