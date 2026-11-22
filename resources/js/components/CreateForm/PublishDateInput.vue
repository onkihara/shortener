<template>
  <div>
    <label :for="id" class="block text-sm font-medium text-gray-700 mb-2">
      {{ $t('available_from') }} <span class="text-gray-500 text-xs">({{ $t('optional') }})</span>
    </label>
    <input
      :id="id"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="date"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
    >
    <p class="text-xs text-gray-500 mt-1">
      {{ $t('publish_help') }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  id: {
    type: String,
    default: 'publish_date'
  },
  modelValue: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])
</script>
