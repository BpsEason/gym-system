<template>
  <div class="space-y-4">
    <h2 class="text-2xl font-semibold text-gray-800 text-center">課程日曆</h2>
    <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
      <FullCalendar :options="calendarOptions" />
    </div>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  components: { FullCalendar },
  setup() {
    const calendarOptions = ref({
      plugins: [dayGridPlugin],
      initialView: 'dayGridMonth',
      locale: 'zh-tw', // Set locale to Traditional Chinese
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek,dayGridDay'
      },
      events: async (info, successCallback, failureCallback) => {
        try {
          // Fetch course schedules from your Laravel API
          const response = await axios.get('/api/course/schedules', {
            params: {
              start: info.startStr,
              end: info.endStr
            }
          })
          const events = response.data.data.map(schedule => ({ // Assuming data.data if using Laravel API Resources
            title: `${schedule.course_name} (${schedule.booked_count}/${schedule.max_capacity}) - ${schedule.trainer_name}`,
            start: schedule.start_at,
            end: schedule.end_at,
            id: schedule.id, // Use schedule ID as event ID
            // Optional: add color based on capacity or status
            color: schedule.booked_count >= schedule.max_capacity ? '#ef4444' : '#22c55e' // Red if full, Green if available
          }))
          successCallback(events)
        } catch (error) {
          console.error("Error fetching course schedules:", error);
          failureCallback(error)
        }
      },
      eventClick: (info) => {
        // Handle event click, e.g., show booking details modal
        alert(`課程: ${info.event.title}\n時間: ${new Date(info.event.start).toLocaleString()} - ${new Date(info.event.end).toLocaleString()}\n點擊事件ID: ${info.event.id}`);
        // In a real app, you would open a modal for booking or viewing details
      }
    })

    // Optional: Fetch initial data or perform actions on component mount
    onMounted(() => {
      console.log('CourseCalendar component mounted.')
    })

    return { calendarOptions }
  }
}
</script>

<style>
/* Tailwind CSS is loaded globally via app.blade.php */
/* You can add custom styles for FullCalendar here if needed, beyond Tailwind */
.fc .fc-toolbar-title {
  font-size: 1.5rem; /* Larger title for calendar */
  font-weight: 700;
}
.fc-event {
  border-radius: 0.5rem; /* Rounded corners for events */
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>
