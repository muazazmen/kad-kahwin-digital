<script setup>
import { onMounted, ref } from 'vue';
import { getDesignsWithTrashed } from '@/service/DesignService';
// import Carousel from 'primevue/carousel'

const particlesConfig = ref({
  particles: { number: { value: 10 }, move: { enable: true } },
  fullScreen: { enable: false },
});

const code = ref('')
const designs = ref({
  data: [],
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1
});

const particlesLoaded = async container => {
  console.log("Particles container loaded", container);
};

function fetchDesigns(page = 1) {
  getDesignsWithTrashed(page, designs.value.per_page)
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then((data) => {
      designs.value = {
        data: data.data,
        current_page: data.current_page,
        per_page: data.per_page,
        total: data.total,
        last_page: data.last_page
      };
    })
    .catch((error) => {
      console.error('Fetch error:', error);
    });
}

onMounted(() => {
  fetchDesigns();
});
</script>
<template>
  <div class="card relative z-10">
    <vue-particles id="tsparticles" @particles-loaded="particlesLoaded" :options="particlesConfig" />
    <div v-for="design in designs.data" :key="design.id">
      <h3>{{ design.name }}</h3>

      <Carousel v-if="design.thumbnails && design.thumbnails.length" :value="design.thumbnails" :numVisible="1"
        :numScroll="1" circular :autoplayInterval="4000">
        <template #item="slotProps">
          <img :src="`http://127.0.0.1:8000/storage/${slotProps.data.image_path}`" class="w-40 h-24 object-cover" />
          <span>{{ slotProps.data.image_path }}</span>
        </template>
      </Carousel>
    </div>
  </div>
</template>
<style scoped></style>