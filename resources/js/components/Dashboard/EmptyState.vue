<template>
  <div class="text-center py-12">
    <p class="text-gray-600 mb-4">{{ $t('empty') }}</p>
  </div>
</template>

<script setup>
defineProps({
  createRoute: {
    type: String,
    required: true
  }
})
</script>
