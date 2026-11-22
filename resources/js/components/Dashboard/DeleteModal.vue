<template>
  <div v-if="item" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $t('delete_title') }}</h2>
      <p class="text-gray-700 mb-6">
        {{ $t('delete_warning') }}
      </p>

      <div class="flex gap-3">
        <button 
          @click="$emit('close')"
          class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
        >
          {{ $t('cancel') }}
        </button>
        <button 
          @click="handleDelete"
          :disabled="isDeleting"
          class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold disabled:opacity-50"
        >
          {{ isDeleting ? $t('deleting') : $t('delete') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  item: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['confirm', 'close'])

const isDeleting = ref(false)

const handleDelete = async () => {
  isDeleting.value = true
  try {
    emit('confirm')
  } finally {
    isDeleting.value = false
  }
}
</script>
