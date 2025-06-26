import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import '@fullcalendar/core/main.css'
import '@fullcalendar/daygrid/main.css'

const app = createApp(App)
app.use(router)
app.mount('#app')
