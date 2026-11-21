<template>
  <div class="md:w-lg lg:w-xl max-w-xl2 m-auto">
    <div class="bg-white shadow-xl p-8">
      <ErrorAlert 
        v-if="errorMessage" 
        :message="errorMessage"
        @close="errorMessage = ''"
      />

      <SuccessAlert 
        v-if="successMessage" 
        :message="successMessage"
        :short-link="createdShortLink"
        :show-dashboard-link="isAuthenticated"
        :dashboard-route="dashboardRoute"
        :csrf-token="csrfToken"
        @close="successMessage = ''"
      />

      <form @submit.prevent="submitForm" class="space-y-4">
        <UrlInput 
          v-model="form.url"
          :is-required="true"
        />

        <HashInput 
          v-if="isAuthenticated"
          v-model="form.hash"
        />

        <DescriptionInput 
          v-model="form.description"
          @update:char-count="charCount = $event"
        />

        <DateRangeSlider
          id-prefix="create-dates"
          :publish-date="form.publish_date"
          :expiration-date="form.expiration_date"
          @update:publish-date="form.publish_date = $event"
          @update:expiration-date="form.expiration_date = $event"
        />

        <SubmitButton 
          :is-loading="isSubmitting"
        />
      </form>

      <AuthCallout 
        :is-authenticated="isAuthenticated"
        :login-route="loginRoute"
        :dashboard-route="dashboardRoute"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ErrorAlert from './CreateForm/ErrorAlert.vue'
import SuccessAlert from './CreateForm/SuccessAlert.vue'
import UrlInput from './CreateForm/UrlInput.vue'
import HashInput from './CreateForm/HashInput.vue'
import DescriptionInput from './CreateForm/DescriptionInput.vue'
import DateRangeSlider from './DateRangeSlider.vue'
import SubmitButton from './CreateForm/SubmitButton.vue'
import AuthCallout from './CreateForm/AuthCallout.vue'

const props = defineProps({
  csrfToken: {
    type: String,
    required: true
  },
  isAuthenticated: {
    type: Boolean,
    default: false
  },
  hashPattern: {
    type: String,
    default: '[a-zA-Z0-9_-]{1,16}'
  },
  loginRoute: {
    type: String,
    default: 'login'
  },
  dashboardRoute: {
    type: String,
    default: 'dashboard'
  }
})

const form = ref({
  url: '',
  hash: '',
  description: '',
  publish_date: '',
  expiration_date: ''
})

const errorMessage = ref('')
const successMessage = ref('')
const createdShortLink = ref('')
const charCount = ref(0)
const isSubmitting = ref(false)

const submitForm = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (
    form.value.publish_date
    && form.value.expiration_date
    && form.value.expiration_date < form.value.publish_date
  ) {
    errorMessage.value = 'Das Ablaufdatum darf nicht vor dem Veröffentlichungsdatum liegen.'
    return
  }

  isSubmitting.value = true

  try {
    const response = await fetch('/api/shorten', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken
      },
      body: JSON.stringify({
        url: form.value.url.trim(),
        hash: form.value.hash ? form.value.hash.trim() || null : null,
        description: form.value.description.trim(),
        publish_date: form.value.publish_date ? `${form.value.publish_date} 00:00` : null,
        expiration_date: form.value.expiration_date ? `${form.value.expiration_date} 23:59:59` : null
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Ein Fehler ist aufgetreten.')
    }

    successMessage.value = data.message
    createdShortLink.value = data.data.short_link
    form.value = {
      url: '',
      hash: '',
      description: '',
      publish_date: '',
      expiration_date: ''
    }
    charCount.value = 0
  } catch (error) {
    errorMessage.value = error.message || 'Ein unbekannter Fehler ist aufgetreten.'
  } finally {
    isSubmitting.value = false
  }
}
</script>
