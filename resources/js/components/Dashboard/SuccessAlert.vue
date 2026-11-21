<template>
  <div v-if="message" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
    <p class="text-green-700">{{ message }}</p>
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
