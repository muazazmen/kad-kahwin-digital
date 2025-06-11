<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

onMounted(() => {
  handleOAuthCallback();
});

const handleOAuthCallback = () => {
  const urlParams = new URLSearchParams(window.location.search);
  const accessToken = urlParams.get('accessToken');
  const error = urlParams.get('error');
  const message = urlParams.get('message');
  const action = urlParams.get('action');

  isLoading.value = true;

  if (accessToken) {
    // Successful authentication
    authStore.setToken(accessToken);
    successMessage.value = decodeURIComponent(message || 'Logged in successfully!');
    
    // Redirect to dashboard after short delay
    setTimeout(() => {
      router.push({name: 'landing'});
    }, 1500);
  } 
  else if (action === 'cancelled') {
    // User cancelled connection
    errorMessage.value = decodeURIComponent(message || 'Action cancelled');
    router.push({name: 'landing'}); // Redirect back to profile
  }
  else if (error) {
    // Error cases
    errorMessage.value = decodeURIComponent(message || 'Authentication failed');
    
    // Special handling for specific errors
    if (error === 'user_exists') {
      router.push({name: 'login'}); // Redirect to login page
    } 
    else if (error === 'not_registered') {
      router.push({name: 'register'}); // Redirect to register page
    }
  }

  isLoading.value = false;
};
</script>

<template>
  <div class="auth-container">
    <!-- Loading state -->
    <div v-if="isLoading" class="loading-overlay">
      <div class="loading-spinner"></div>
      <p>Authenticating...</p>
    </div>

    <!-- Error message -->
    <div v-if="errorMessage" class="alert alert-error">
      {{ errorMessage }}
    </div>

    <!-- Success message -->
    <div v-if="successMessage" class="alert alert-success">
      {{ successMessage }}
    </div>
  </div>
</template>

<style scoped>

</style>