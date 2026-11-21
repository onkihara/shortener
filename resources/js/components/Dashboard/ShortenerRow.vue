<template>
  <tr class="hover:bg-gray-50 transition">
    <td class="px-6 py-4 text-sm font-mono font-semibold text-indigo-600">
      <a 
        :href="`/${shortener.Hash}`" 
        target="_blank" 
        class="hover:underline"
      >
        {{ shortener.Hash }}
      </a>
    </td>
    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
      <a 
        :href="shortener.Url" 
        target="_blank" 
        :title="shortener.Url"
        class="text-blue-600 hover:underline"
      >
        {{ shortener.Url }}
      </a>
    </td>
    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">
      📊 {{ shortener.Frequency }}
    </td>
    <td class="px-6 py-4 text-sm text-gray-600">
      {{ formatDate(shortener.PublishDate) }}
    </td>
    <td class="px-6 py-4 text-sm text-gray-600">
      {{ formatDate(shortener.ExpirationDate) }}
    </td>
    <td class="px-6 py-4 text-right text-sm space-x-3">
      <button 
        @click="$emit('edit')"
        class="text-blue-600 hover:text-blue-800 hover:underline font-semibold"
      >
        Bearbeiten
      </button>
      <button 
        @click="$emit('delete')"
        class="text-red-600 hover:text-red-800 hover:underline font-semibold"
      >
        Löschen
      </button>
    </td>
  </tr>
</template>

<script setup>
defineProps({
  shortener: {
    type: Object,
    required: true
  },
  timezone: {
    type: String,
    default: 'UTC'
  }
})

defineEmits(['edit', 'delete'])

const formatDate = (dateString) => {
  if (!dateString) return '-'

  const match = String(dateString).match(
    /^(\d{4})-(\d{2})-(\d{2})(?:[T\s](\d{2}):(\d{2})(?::(\d{2}))?)?/
  )

  if (!match) return '-'

  const [, year, month, day, hour = '00', minute = '00', second = '00'] = match
  return `${day}.${month}.${year} ${hour}:${minute}:${second}`
}
</script>
