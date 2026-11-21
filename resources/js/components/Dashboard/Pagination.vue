<template>
  <div class="mt-6 flex justify-center gap-2">
    <button 
      v-if="pagination.current_page > 1"
      @click="$emit('go-to-page', 1)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      « Erste
    </button>
    <button 
      v-if="pagination.current_page > 1"
      @click="$emit('go-to-page', pagination.current_page - 1)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      ‹ Zurück
    </button>

    <span class="px-3 py-2 text-gray-700">
      Seite {{ pagination.current_page }} von {{ pagination.last_page }}
    </span>

    <button 
      v-if="pagination.current_page < pagination.last_page"
      @click="$emit('go-to-page', pagination.current_page + 1)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      Weiter ›
    </button>
    <button 
      v-if="pagination.current_page < pagination.last_page"
      @click="$emit('go-to-page', pagination.last_page)"
      class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100"
    >
      Letzte »
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
