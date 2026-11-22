<template>
  <section class="border border-gray-200 rounded-lg overflow-hidden">
    <button
      type="button"
      class="w-full flex items-center justify-between gap-4 px-4 py-3 bg-gray-50 text-left hover:bg-gray-100 transition"
      :aria-expanded="isOpen"
      :aria-controls="contentId"
      @click="isOpen = !isOpen"
    >
      <div>
        <span class="block text-sm font-semibold text-gray-900">{{ $t('advanced_settings') }}</span>
        <span class="block text-xs text-gray-500 mt-0.5">{{ dateSummary }}</span>
      </div>
      <svg
        class="size-5 shrink-0 text-gray-500 transition-transform"
        :class="{ 'rotate-180': isOpen }"
        viewBox="0 0 20 20"
        fill="none"
        aria-hidden="true"
      >
        <path d="M5 7.5 10 12.5 15 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>

    <div
      v-show="isOpen"
      :id="contentId"
      class="grid gap-4 p-4 sm:grid-cols-2"
    >
      <PublishDateInput
        :id="publishId"
        :model-value="publishDate"
        @update:model-value="$emit('update:publishDate', $event)"
      />

      <ExpirationDateInput
        :id="expirationId"
        :model-value="expirationDate"
        @update:model-value="$emit('update:expirationDate', $event)"
      />
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import { t } from '../translations'
import PublishDateInput from './CreateForm/PublishDateInput.vue'
import ExpirationDateInput from './CreateForm/ExpirationDateInput.vue'

const props = defineProps({
  publishDate: {
    type: String,
    default: ''
  },
  expirationDate: {
    type: String,
    default: ''
  },
  idPrefix: {
    type: String,
    default: 'date-range'
  },
  initiallyOpen: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:publishDate', 'update:expirationDate'])

const isOpen = ref(props.initiallyOpen)
const contentId = `${props.idPrefix}-content`
const publishId = `${props.idPrefix}-publish`
const expirationId = `${props.idPrefix}-expiration`

const dateSummary = computed(() => {
  if (props.publishDate && props.expirationDate) {
    return t('date_range', { start: props.publishDate, end: props.expirationDate })
  }
  if (props.publishDate) {
    return t('date_from', { date: props.publishDate })
  }
  if (props.expirationDate) {
    return t('date_until', { date: props.expirationDate })
  }
  return ''
})
</script>
