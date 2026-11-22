<template>
  <button
    type="submit"
    :disabled="isLoading"
    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold mt-6 disabled:opacity-50 disabled:cursor-not-allowed"
  >
    {{ isLoading ? $t('creating') : $t('create_link') }}
  </button>
</template>

<script setup>
defineProps({
  isLoading: {
    type: Boolean,
    default: false
  }
})
</script>
