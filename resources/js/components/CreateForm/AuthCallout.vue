<template>
  <div>
    <!-- Guest Sign-in Callout -->
    <div v-if="!isAuthenticated" class="mt-6 p-4 bg-indigo-50 rounded-lg text-center">
      <p class="text-sm text-gray-700 mb-2">
        {{ $t('login_prompt') }}
      </p>
      <a 
        :href="loginRoute" 
        class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold"
      >
        {{ $t('login') }}
      </a>
    </div>

    <!-- Authenticated Dashboard Link -->
    <div v-else class="mt-6 p-4 bg-indigo-50 rounded-lg text-center">
      <a 
        :href="dashboardRoute" 
        class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold"
      >
        {{ $t('manage_links') }}
      </a>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  isAuthenticated: {
    type: Boolean,
    default: false
  },
  loginRoute: {
    type: String,
    default: 'login'
  },
  dashboardRoute: {
    type: String,
    default: 'dashboard'
  }
})
</script>
