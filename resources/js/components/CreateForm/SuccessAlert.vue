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
    <div class="mt-3 flex gap-2">
      <button 
        @click="showQrModal = true"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-semibold"
      >
        🎯 QR-Code
      </button>
      <a 
        v-if="showDashboardLink"
        :href="dashboardRoute"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold"
      >
        {{ $t('dashboard_link') }}
      </a>
    </div>

    <QrCodeModal
      :is-open="showQrModal"
      :initial-text="shortLink"
      :csrf-token="csrfToken"
      @close="showQrModal = false"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { t } from '../../translations'
import QrCodeModal from '../Dashboard/QrCodeModal.vue'

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
  },
  csrfToken: {
    type: String,
    default: ''
  }
})

defineEmits(['close'])

const copyButtonText = ref(t('copy'))
const showQrModal = ref(false)

const copyToClipboard = () => {
  const input = document.querySelector('input[readonly]')
  if (!input) return

  navigator.clipboard.writeText(input.value).then(() => {
    copyButtonText.value = t('copied')
    setTimeout(() => {
      copyButtonText.value = t('copy')
    }, 2000)
  }).catch(() => {
    copyButtonText.value = t('copy_error')
  })
}
</script>
