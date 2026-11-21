<template>
  <div>
    <label for="publish_date" class="block text-sm font-medium text-gray-700 mb-2">
      Veröffentlichungsdatum <span class="text-gray-500 text-xs">(optional)</span>
    </label>
    <input
      id="publish_date"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="date"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
    >
    <p class="text-xs text-gray-500 mt-1">
      Link wird ab diesem Datum aktiv
    </p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])
</script>
