<template>
  <div class="mb-8">
    <a 
      :href="createRoute" 
      class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
    >
      + Neuen Short-Link erstellen
    </a>
  </div>
</template>

<script setup>

  defineProps({
    createRoute : {
      type: String,
      default: 'create'
    }
  })

</script>
