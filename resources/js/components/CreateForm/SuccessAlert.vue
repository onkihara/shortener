<template>
  <div v-if="message" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
    <p class="text-green-700 font-semibold mb-3">{{ message }}</p>
    <div class="flex gap-2">
      <input 
        type="text" 
        :value="shortLink" 
        readonly
        class="flex-1 px-3 py-2 border border-green-300 rounded-lg bg-white text-sm font-mono"
      >
      <button 
        @click="copyToClipboard"
        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold"
      >
        {{ copyButtonText }}
      </button>
    </div>
    <div v-if="showDashboardLink" class="mt-3">
      <a 
        :href="dashboardRoute"
        class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold"
      >
        Zum Dashboard
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  message: {
    type: String,
    default: ''
  },
  shortLink: {
    type: String,
    default: ''
  },
  showDashboardLink: {
    type: Boolean,
    default: false
  },
  dashboardRoute : {
    type: String,
    default: 'dashboard'
  }
})

defineEmits(['close'])

const copyButtonText = ref('Kopieren')

const copyToClipboard = () => {
  const input = document.querySelector('input[readonly]')
  if (!input) return

  navigator.clipboard.writeText(input.value).then(() => {
    copyButtonText.value = '✅ Kopiert!'
    setTimeout(() => {
      copyButtonText.value = 'Kopieren'
    }, 2000)
  }).catch(() => {
    copyButtonText.value = 'Fehler beim Kopieren'
  })
}
</script>
