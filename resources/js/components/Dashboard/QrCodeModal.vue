<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-full overflow-y-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">{{ $t('qr_generator') }}</h2>
        <button 
          @click="close"
          class="text-gray-400 hover:text-gray-600"
        >
          ✕
        </button>
      </div>

      <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert">
        <p class="text-red-700">{{ errorMessage }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Input Section -->
        <div class="space-y-4">
          <div>
            <label for="qrText" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('text_or_url') }}
            </label>
            <input 
              id="qrText"
              v-model="formData.text"
              type="text" 
              :placeholder="$t('enter_text_or_url')"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
            />
          </div>

          <div>
            <label for="qrFormat" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('format') }}
            </label>
            <select 
              id="qrFormat"
              v-model="formData.format"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
            >
              <option value="png">PNG</option>
              <option value="gif">GIF</option>
              <option value="svg">SVG</option>
              <option value="webp">WebP</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('foreground_color') }}
            </label>
            <div class="flex items-center gap-2">
              <input 
                v-model="formData.foregroundColor"
                type="color"
                class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
              />
              <input 
                v-model="formData.foregroundColor"
                type="text"
                placeholder="#000000"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none font-mono text-sm"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('background_color') }}
            </label>
            <div class="flex items-center gap-2">
              <input 
                v-model="formData.backgroundColor"
                type="color"
                class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
              />
              <input 
                v-model="formData.backgroundColor"
                type="text"
                placeholder="#FFFFFF"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none font-mono text-sm"
              />
            </div>
          </div>

          <div>
            <label for="qrSize" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('size') }}: {{ formData.size }}px
            </label>
            <input 
              id="qrSize"
              v-model.number="formData.size"
              type="range"
              min="100"
              max="1000"
              step="50"
              class="w-full"
            />
            <p class="text-xs text-gray-500 mt-1">{{ $t('pixels') }}</p>
          </div>

          <button 
            @click="generateQrCode"
            :disabled="isLoading || !formData.text"
            class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoading ? $t('generating') : $t('generate_qr') }}
          </button>
        </div>

        <!-- Preview Section -->
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
          <div v-if="isLoading" class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
            <p class="text-gray-600">{{ $t('generating') }}</p>
          </div>

          <div v-else-if="qrCodeImage" class="text-center">
            <img 
              :src="qrCodeImage" 
              :alt="formData.text"
              class="max-w-full h-auto mb-4"
            />
            <div class="space-y-2 w-full">
              <button 
                @click="downloadQrCode"
                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold"
              >
                {{ $t('download') }}
              </button>
              <button 
                @click="copyImageToClipboard"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold"
              >
                {{ $t('copy_icon') }}
              </button>
            </div>
          </div>

          <div v-else class="text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <p class="text-gray-600">{{ $t('qr_preview') }}</p>
          </div>
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button 
          @click="close"
          class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
        >
          {{ $t('close') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { t } from '../../translations'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  csrfToken: {
    type: String,
    required: true
  },
  initialText: {
    type: String,
    default: ''
  },
  hash: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close'])

const formData = ref({
  text: '',
  format: 'png',
  foregroundColor: '#000000',
  backgroundColor: '#FFFFFF',
  size: 300
})

const qrCodeImage = ref('')
const qrCodeFormat = ref('png')
const isLoading = ref(false)
const errorMessage = ref('')

const generateQrCode = async () => {
  if (!formData.value.text.trim()) {
    errorMessage.value = t('qr_text_required')
    return
  }

  errorMessage.value = ''
  isLoading.value = true

  try {
    const response = await fetch('/api/qr-code', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken
      },
      body: JSON.stringify({
        text: formData.value.text,
        format: formData.value.format,
        foreground_color: formData.value.foregroundColor,
        background_color: formData.value.backgroundColor,
        size: formData.value.size
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || t('qr_error'))
    }

    qrCodeImage.value = data.data.image
    qrCodeFormat.value = data.data.format
  } catch (error) {
    errorMessage.value = error.message || t('error')
  } finally {
    isLoading.value = false
  }
}

const downloadQrCode = () => {
  if (!qrCodeImage.value) return
  
  const link = document.createElement('a')
  link.href = qrCodeImage.value
  link.download = `${props.hash || 'qr-code'}.${qrCodeFormat.value}`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const copyImageToClipboard = async () => {
  if (!qrCodeImage.value) return

  try {
    const image = new Image()
    image.src = qrCodeImage.value
    const pngBlob = image.decode().then(() => new Promise((resolve, reject) => {
      const canvas = document.createElement('canvas')
      canvas.width = image.naturalWidth
      canvas.height = image.naturalHeight
      canvas.getContext('2d').drawImage(image, 0, 0)
      canvas.toBlob(blob => blob ? resolve(blob) : reject(new Error('PNG conversion failed')), 'image/png')
    }))

    await navigator.clipboard.write([
      new ClipboardItem({
        'image/png': pngBlob
      })
    ])
    
    errorMessage.value = ''
    alert(t('qr_copied'))
  } catch (error) {
    errorMessage.value = t('clipboard_error')
  }
}

const close = () => {
  emit('close')
  qrCodeImage.value = ''
  errorMessage.value = ''
}

// Auto-generate QR code when modal opens or text changes
watch(() => props.isOpen, (newVal) => {
  if (newVal && props.initialText) {
    formData.value.text = props.initialText
    // Generate after the text is set
    setTimeout(() => {
      generateQrCode()
    }, 0)
  }
})

watch(() => props.initialText, (newVal) => {
  if (props.isOpen && newVal) {
    formData.value.text = newVal
    // Generate immediately
    setTimeout(() => {
      generateQrCode()
    }, 0)
  }
})
</script>
