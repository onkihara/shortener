<template>
  <div>
    <label for="hash" class="block text-sm font-medium text-gray-700 mb-2">
      Benutzerdefinierter Hash <span class="text-gray-500 text-xs">(optional)</span>
    </label>
    <input
      id="hash"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="text"
      placeholder="mein-link"
      pattern="[a-zA-Z0-9_-]{1,16}"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
    >
    <p class="text-xs text-gray-500 mt-1">
      Nur Buchstaben, Zahlen, Bindestrich und Unterstrich (max. 16 Zeichen)
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
