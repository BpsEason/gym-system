<template>
  <div id="app" class="p-6 max-w-4xl mx-auto bg-white rounded-xl shadow-md space-y-4">
    <h1 class="text-3xl font-bold text-gray-900 text-center">健身房管理系統</h1>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>
<style>
/* Tailwind CSS is loaded globally via app.blade.php */
body {
  font-family: 'Inter', sans-serif;
  background-color: #f3f4f6;
}
</style>
