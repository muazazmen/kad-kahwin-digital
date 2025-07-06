<template>
  <input 
    type="color" 
    v-model="colorValue"
    @input="validateHex"
  >

  <span>{{ colorValue }}</span>
</template>

<script>
export default {
  data() {
    return {
      colorValue: '#ffffff'
    }
  },
  methods: {
    validateHex(event) {
      if (event.target.value.startsWith('rgb(')) {
        // Revert to previous valid hex value
        event.target.value = this.colorValue;
      } else {
        this.colorValue = event.target.value;
      }
    }
  }
}
</script>