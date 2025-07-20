<template>
    <div class="card relative z-10">
      <vue-particles id="tsparticles" @particles-loaded="particlesLoaded" :options="particlesConfig" />
      <div>
        <Editor v-model="code" editor-style="height: 320px" />
      </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const particlesConfig = ref({
  particles: { number: { value: 10 }, move: { enable: true } },
  fullScreen: { enable: false },
});

const code = ref('')

const particlesLoaded = async container => {
    console.log("Particles container loaded", container);
};
</script>
<style scoped>

</style>