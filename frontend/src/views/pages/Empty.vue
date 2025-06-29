
<script setup>
import ColorPicker from 'primevue/colorpicker';
import { ref } from 'vue';

const colorHEX = ref();

</script>
<template>
  <div class="card">
    <div class="flex gap-4">
      <ColorPicker v-model="colorHEX" />
      <p>Selected Color: {{ colorHEX }}</p>
      <input type="color" v-model="colorHEX" class="w-24 h-10 border rounded" />
    </div>
  </div>
</template>

<style scoped>
/* Add your styles here */
</style>