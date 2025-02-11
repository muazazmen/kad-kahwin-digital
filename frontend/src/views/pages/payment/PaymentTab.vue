<template>
  <div class="card">
    <h1>Payment</h1>
  </div>
</template>

<script setup>
import { getUsers } from '@/service/GeneralService';
import { onMounted } from 'vue';

const fetchUsers = async () => {
    const res = await getUsers();
    console.log('sini', res);
};

onMounted(() => {
    fetchUsers();
});
</script>

<style scoped>
/* Add your styles here */
</style>