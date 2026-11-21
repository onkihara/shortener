<template>
  <div>
    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
      Beschreibung <span class="text-gray-500 text-xs">(optional)</span>
    </label>
    <textarea
      id="description"
      :value="modelValue"
      @input="handleInput"
      placeholder="Beschreibe diesen Link..."
      rows="3"
      maxlength="1024"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition resize-none"
    ></textarea>
    <p class="text-xs text-gray-500 mt-1">
      {{ charCount }} / 1024
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'update:char-count'])

const charCount = computed(() => props.modelValue.length)

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
  emit('update:char-count', event.target.value.length)
}
</script>
