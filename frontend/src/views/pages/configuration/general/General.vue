<template>
    <div class="card" style="text-align: center">
        <li>users</li>
        <li>music</li>
        <li>prayer</li>
        <li>frame</li>
    </div>
</template>

<script setup>
import { getUsers } from '@/service/GeneralService';
import { onMounted, ref } from 'vue';

const users = ref([]);

const fetchUsers = async () => {
    const res = await getUsers();
    console.log('sini', res);
    const data = await res.json();
    users.value = data;
    console.log('data', data);
    
};

onMounted(() => {
    fetchUsers();
});
</script>
