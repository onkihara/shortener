<template>
  <div v-if="message" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
    <p class="text-red-700">{{ message }}</p>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: ''
  }
})

defineEmits(['close'])
</script>
