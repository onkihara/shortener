<template>
  <div>
    <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
      URL <span class="text-red-500">*</span>
    </label>
    <input
      id="url"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="url"
      placeholder="https://example.com/very/long/url"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
      required
    >
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true
  },
  isRequired: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])
</script>
