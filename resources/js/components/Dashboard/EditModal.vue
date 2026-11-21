<template>
  <div v-if="item" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-full overflow-y-auto p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Shortener bearbeiten</h2>

      <div v-if="validationError || errorMessage" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert">
        <p class="text-red-700">{{ validationError || errorMessage }}</p>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Hash</label>
        <input 
          type="text" 
          :value="item.Hash" 
          readonly 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
        />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
        <input 
          type="text" 
          :value="item.Url" 
          readonly 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm break-all"
        />
      </div>

      <div class="mb-4">
        <label for="editDescription" class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
        <textarea
          id="editDescription"
          v-model="formData.description"
          @input="updateCharCount"
          rows="4"
          maxlength="1024"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none"
        ></textarea>
        <p class="text-xs text-gray-500 mt-1">
          {{ charCount }} / 1024
        </p>
      </div>

      <div class="mb-4">
        <label for="editPublishDate" class="block text-sm font-medium text-gray-700 mb-2">
          Veröffentlichungsdatum <span class="text-gray-500 text-xs">(optional)</span>
        </label>
        <input 
          id="editPublishDate"
          v-model="formData.publish_date"
          type="date" 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
        >
        <p class="text-xs text-gray-500 mt-1">Link wird ab diesem Datum um 00:00 Uhr aktiv</p>
      </div>

      <div class="mb-6">
        <label for="editExpirationDate" class="block text-sm font-medium text-gray-700 mb-2">
          Ablaufdatum <span class="text-gray-500 text-xs">(optional)</span>
        </label>
        <input 
          id="editExpirationDate"
          v-model="formData.expiration_date"
          type="date" 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
        >
        <p class="text-xs text-gray-500 mt-1">Link läuft an diesem Datum um 23:59:59 Uhr ab</p>
      </div>

      <div class="flex gap-3">
        <button 
          @click="$emit('close')"
          :disabled="isSaving"
          class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold disabled:opacity-50"
        >
          Abbrechen
        </button>
        <button 
          @click="handleSave"
          :disabled="isSaving"
          class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold disabled:opacity-50"
        >
          {{ isSaving ? 'Speichert...' : 'Speichern' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    default: null
  },
  timezone: {
    type: String,
    default: 'UTC'
  },
  errorMessage: {
    type: String,
    default: ''
  },
  isSaving: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['save', 'close'])

const formData = ref({
  description: '',
  publish_date: '',
  expiration_date: ''
})

const charCount = ref(0)
const validationError = ref('')
const originalPublishDate = ref('')
const originalExpirationDate = ref('')

const formatEditDate = (value) => {
  if (!value) return ''
  const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/)
  return match ? `${match[1]}-${match[2]}-${match[3]}` : ''
}

watch(() => props.item, (newItem) => {
  if (newItem) {
    formData.value.description = newItem.Description || ''
    formData.value.publish_date = formatEditDate(newItem.PublishDate)
    formData.value.expiration_date = formatEditDate(newItem.ExpirationDate)
    originalPublishDate.value = formData.value.publish_date
    originalExpirationDate.value = formData.value.expiration_date
    charCount.value = formData.value.description.length
    validationError.value = ''
  }
}, { immediate: true })

const updateCharCount = () => {
  charCount.value = formData.value.description.length
}

const handleSave = () => {
  validationError.value = ''

  const publishDateChanged = formData.value.publish_date !== originalPublishDate.value
  const expirationDateChanged = formData.value.expiration_date !== originalExpirationDate.value

  if (formData.value.publish_date && formData.value.expiration_date) {
    if (formData.value.expiration_date < formData.value.publish_date) {
      validationError.value = 'Das Ablaufdatum darf nicht vor dem Veröffentlichungsdatum liegen.'
      return
    }
  }

  const payload = { description: formData.value.description }
  if (publishDateChanged) {
    payload.publish_date = formData.value.publish_date ? `${formData.value.publish_date} 00:00` : null
  }
  if (expirationDateChanged) {
    payload.expiration_date = formData.value.expiration_date ? `${formData.value.expiration_date} 23:59:59` : null
  }

  emit('save', payload)
}
</script>
