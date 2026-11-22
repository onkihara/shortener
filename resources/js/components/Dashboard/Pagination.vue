<template>
  <div class="mt-6 flex justify-center gap-2">
    <button 
      v-if="pagination.current_page > 1"
      @click="$emit('go-to-page', 1)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      {{ $t('first') }}
    </button>
    <button 
      v-if="pagination.current_page > 1"
      @click="$emit('go-to-page', pagination.current_page - 1)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      {{ $t('previous') }}
    </button>

    <span class="px-3 py-2 text-gray-700">
      {{ $t('page', { current: pagination.current_page, last: pagination.last_page }) }}
    </span>

    <button 
      v-if="pagination.current_page < pagination.last_page"
      @click="$emit('go-to-page', pagination.current_page + 1)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      {{ $t('next') }}
    </button>
    <button 
      v-if="pagination.current_page < pagination.last_page"
      @click="$emit('go-to-page', pagination.last_page)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      {{ $t('last') }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  pagination: {
    type: Object,
    required: true
  }
})

defineEmits(['go-to-page'])
</script>
