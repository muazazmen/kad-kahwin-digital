
<script setup>
import InputEditor from '@/components/InputEditor.vue';
import { ref } from 'vue';

const content = ref('');

</script>
<template>
  <div class="card">
    <InputEditor v-model="content"></InputEditor>

  </div>
</template>

<style scoped>
/* Add your styles here */
</style>