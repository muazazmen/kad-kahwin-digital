<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

onMounted(() => {
    // KIV: Make a different state for this (signup, signin)
    // Get the full URL
    const accessToken = route.query.accessToken;

    if (accessToken) {
        localStorage.setItem('accessToken', accessToken);
        router.push({ name: 'landing' });
    } else {
        console.error('No access token found in URL');
        router.push({ name: 'login' });
    }
});
</script>

<template>
    <div class="flex flex-col justify-center items-center gap-4 h-screen bg-gray-100 dark:bg-gray-900">
        <ProgressSpinner />
        <div class="text-gray-700 dark:text-gray-300">Redirecting...</div>
    </div>
</template>
