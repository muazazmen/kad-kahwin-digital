<template>
    <div class="card relative z-10">
      <!-- FIXME: particles were overlapping to other elements it should be in this container only -->
      <div class="absolute z-[-5]">
        <vue-particles id="tsparticles" @particles-loaded="particlesLoaded" :options="particlesConfig" />
      </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const particlesConfig = ref({
  particles: { number: { value: 10 }, move: { enable: true } }
});

const particlesLoaded = async container => {
    console.log("Particles container loaded", container);
};
</script>
<style scoped>

</style>