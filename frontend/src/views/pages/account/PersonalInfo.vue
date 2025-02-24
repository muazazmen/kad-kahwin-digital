<script setup>
import { updateAccount } from '@/service/AccountService';
import { useAuthStore } from '@/stores/auth';
import { ProgressSpinner, useToast } from 'primevue';
import { onMounted, ref, computed } from 'vue';

const authStore = useAuthStore();

const toast = useToast();

const previewImageUrl = ref(null);
const avatar = ref(null);

const unchangedData = ref(null);
const loading = ref(false);

const uploadImage = (event) => {
    const file = event.target.files[0];
    if (file) {
        previewImageUrl.value = URL.createObjectURL(file);
        avatar.value = file;
    }
};

// Compare current user data with the original user data
const isChanged = computed(() => {
    if (!unchangedData.value) return false;

    return (
        authStore.user.first_name !== unchangedData.value.first_name ||
        authStore.user.last_name !== unchangedData.value.last_name ||
        authStore.user.username !== unchangedData.value.username ||
        authStore.user.phone_no !== unchangedData.value.phone_no ||
        !!avatar.value // Check if a new profile image is selected
    );
});

const fetchUpdateAccount = async () => {
    loading.value = true;
    try {
        const formData = new FormData();

        formData.append('first_name', authStore.user.first_name);
        formData.append('last_name', authStore.user.last_name || '');
        formData.append('username', authStore.user.username || '');
        formData.append('phone_no', authStore.user.phone_no || '');

        if (avatar.value) {
            formData.append('avatar', avatar.value);
        }

        const res = await updateAccount(formData);
        const data = await res.json();

        if (res.ok) {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: data.message,
                life: 3000
            });
            await authStore.fetchUser();

            // Update the original user data to the latest data
            unchangedData.value = { ...authStore.user };
            avatar.value = null;
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: data.message,
                life: 3000
            });
        }
    } catch (error) {
        console.error(error.message);
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    // Set original user data after fetching
    unchangedData.value = { ...authStore.user };
});
</script>

<template>
    <div v-if="authStore.user" class="grid md:grid-cols-2 sm:grid-cols-1 m-4 gap-4">
        <div class="md:col-span-2 sm:col-span-1 flex flex-col justify-center items-center gap-4 mb-4 relative">
            <label class="relative flex items-center justify-center md:w-36 md:h-36 w-24 h-24 bg-gray-200 rounded-full cursor-pointer overflow-hidden group">
                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" @change="uploadImage" />
                <img id="profileImage" :src="previewImageUrl || authStore.user.avatar" alt="profile" class="w-full h-full object-cover rounded-full" />
                <div class="absolute inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="pi pi-pencil text-2xl sm:text-3xl"></i>
                </div>
            </label>
        </div>
        <div class="flex flex-col gap-2 mb-2">
            <label for="firstName">First Name</label>
            <InputText v-model="authStore.user.first_name" id="firstName" type="text" placeholder="First Name" variant="filled" />
        </div>
        <div class="flex flex-col gap-2 mb-2">
            <label for="lastName">Last Name</label>
            <InputText v-model="authStore.user.last_name" id="lastName" type="text" placeholder="Last Name" variant="filled" />
        </div>
        <div class="flex flex-col gap-2 mb-2">
            <label for="email">Email</label>
            <InputText v-model="authStore.user.email" id="email" type="email" placeholder="Email" disabled />
        </div>
        <div class="flex flex-col gap-2 mb-2">
            <label for="username">Username</label>
            <InputText v-model="authStore.user.username" id="username" type="text" placeholder="Username" variant="filled" />
        </div>
        <div class="col-span-2 flex flex-col gap-2 mb-2">
            <label for="phoneNo">Phone Number</label>
            <InputText v-model="authStore.user.phone_no" id="phoneNo" v-tooltip="'Format: +601123456789'" type="text" placeholder="Phone Number" variant="filled" />
        </div>
        <div></div>
        <div class="col-span-1 flex justify-end gap-4">
            <Button label="Save" @click="fetchUpdateAccount" :loading="loading" :disabled="!isChanged" />
        </div>
    </div>
    <div v-else>
        <ProgressSpinner />
    </div>
</template>
