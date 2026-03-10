<template>
  <div class="relative w-full overflow-hidden select-none" ref="carouselRef" @mouseenter="pauseAutoplay"
    @mouseleave="startAutoplay">
    <!-- Carousel track -->
    <div class="flex transition-transform duration-500 ease-out"
      :style="{ transform: `translateX(-${currentIndex * (100 / visible)}%)` }" @mousedown="startDrag"
      @mousemove="onDrag" @mouseup="endDrag" @mouseleave="endDrag" @touchstart="startDrag" @touchmove="onDrag"
      @touchend="endDrag">
      <div v-for="(item, i) in props.items" :key="i" class="flex-shrink-0" :style="{ width: `${100 / visible}%` }">
        <slot :item="item" :index="i" />
      </div>
    </div>

    <!-- Left arrow -->
    <button v-if="showArrows"
      class="absolute top-1/2 left-2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full"
      @click="prev">
      <i class="pi pi-chevron-left"></i>
    </button>

    <!-- Right arrow -->
    <button v-if="showArrows"
      class="absolute top-1/2 right-2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full"
      @click="next">
      <i class="pi pi-chevron-right"></i>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  visible: {
    type: Number,
    default: 1,
  },
  loop: {
    type: Boolean,
    default: true,
  },
  showArrows: {
    type: Boolean,
    default: true,
  },
  autoplay: {
    type: Boolean,
    default: false,
  },
  autoplayInterval: {
    type: Number,
    default: 3000,
  },
})

const currentIndex = ref(1)
let startX = 0
let isDragging = false
let autoplayTimer = null

// --- Drag Logic ---
function startDrag(e) {
  isDragging = true
  pauseAutoplay()
  startX = e.touches ? e.touches[0].clientX : e.clientX
}

function onDrag(e) {
  if (!isDragging) return
  const currentX = e.touches ? e.touches[0].clientX : e.clientX
  const diff = startX - currentX
  if (Math.abs(diff) > 50) {
    if (diff > 0) next()
    else prev()
    endDrag()
  }
}

function endDrag() {
  isDragging = false
  startAutoplay()
}

// --- Navigation ---
function next() {
  if (currentIndex.value < props.items.length - 1) {
    currentIndex.value++
  } else if (props.loop) {
    currentIndex.value = 1
  }
}

function prev() {
  if (currentIndex.value > 0) {
    currentIndex.value--
  } else if (props.loop) {
    currentIndex.value = props.items.length - 1
  }
}

// --- Autoplay ---
function startAutoplay() {
  if (props.autoplay) {
    stopAutoplay()
    autoplayTimer = setInterval(next, props.autoplayInterval)
  }
}

function pauseAutoplay() {
  stopAutoplay()
}

function stopAutoplay() {
  if (autoplayTimer) {
    clearInterval(autoplayTimer)
    autoplayTimer = null
  }
}

// Lifecycle
onMounted(() => {
  startAutoplay()
})

onBeforeUnmount(() => {
  stopAutoplay()
})
</script>

<style scoped>
</style>
