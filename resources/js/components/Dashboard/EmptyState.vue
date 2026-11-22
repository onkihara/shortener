<template>
  <div class="text-center py-12 bg-white shadow-sm">
    <p class="text-gray-600 mb-4">{{ $t('empty') }}</p>
    <a 
      :href="createRoute"
      class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
    >
      {{ $t('create_now') }}
    </a>
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
