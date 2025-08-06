<script setup>
import { getThemeById, updateTheme } from '@/service/ThemeService';
import { useToast } from 'primevue';
import { reactive, ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const errors = reactive({}); // Reactive object to hold validation errors

const theme = ref(null);
const loading = ref(false);

const themeForm = reactive({
    name: '',
});

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);
    loading.value = true;

    updateTheme(route.params.id, themeForm)
        .then(async (res) => {
            const data = await res.json(); // parse JSON from res

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Optionally, redirect to theme list or another page
                router.push({ name: 'theme-list' });
            } else {
                if (data.errors) {
                    // Populate reactive errors object
                    Object.keys(data.errors).forEach((key) => {
                        errors[key] = data.errors[key];
                    });
                }

                console.error('Error creating theme:', data.message);
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        })
        .catch((error) => {
            console.error('Error creating theme:', error);
            toast.add({ severity: 'error', summary: 'Network Error', detail: 'Could not reach the server', life: 3000 });
        })
        .finally(() => {
            loading.value = false; // Reset loading state
        });
}

onMounted(() => {
    getThemeById(route.params.id)
        .then(async (res) => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            theme.value = await res.json(); // Parse JSON from response
            // Populate form with theme data
            Object.assign(themeForm, theme.value);
        })
        .catch((error) => {
            console.error('Error fetching theme:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not fetch theme data', life: 3000 });
        });
});
</script>
<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Edit Theme</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'theme-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div class="col-span-2">
                    <label for="name" class="block"><span class="text-red-500">* </span>Name</label>
                    <InputText v-model="themeForm.name" placeholder="Name" variant="filled" :invalid="errors.name" fluid style="width: 500px;" />
                    <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'theme-list' })" class="p-button-outlined"></Button>
                <Button label="Update" type="submit" :loading="loading"></Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
