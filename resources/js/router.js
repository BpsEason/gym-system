import { createRouter, createWebHistory } from 'vue-router'
import CourseCalendar from './pages/CourseCalendar.vue'

const routes = [
  { path: '/', component: CourseCalendar },
  // Add other routes here as needed, e.g., for login, member profiles etc.
  // { path: '/login', component: LoginPage },
  // { path: '/members', component: MembersPage },
]

export default createRouter({
  history: createWebHistory(),
  routes
})
