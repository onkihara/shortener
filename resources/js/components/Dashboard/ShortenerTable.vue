<template>
  <div class="overflow-x-auto bg-white shadow-sm">
    <table class="w-full">
      <thead class="bg-gray-100 border-b border-gray-200">
        <tr>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hash</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">URL</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Häufigkeit</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Verfügbar ab</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Läuft ab am</th>
          <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Aktionen</th>
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
