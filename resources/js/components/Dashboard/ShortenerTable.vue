<template>
  <div class="overflow-x-auto bg-white shadow-sm">
    <table class="w-full">
      <thead class="bg-gray-100 border-b border-gray-200">
        <tr>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hash</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">URL</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">{{ $t('frequency') }}</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">{{ $t('available_from') }}</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">{{ $t('expires_at') }}</th>
          <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">{{ $t('actions') }}</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <ShortenerRow 
          v-for="shortener in shorteners"
          :key="shortener.ID"
          :shortener="shortener"
          :timezone="timezone"
          @edit="$emit('edit', shortener)"
          @delete="$emit('delete', shortener)"
          @show-qr="$emit('show-qr', $event)"
        />
      </tbody>
    </table>
  </div>
</template>

<script setup>
import ShortenerRow from './ShortenerRow.vue'

defineProps({
  shorteners: {
    type: Array,
    required: true
  },
  timezone: {
    type: String,
    default: 'UTC'
  }
})

defineEmits(['edit', 'delete', 'show-qr'])
</script>
