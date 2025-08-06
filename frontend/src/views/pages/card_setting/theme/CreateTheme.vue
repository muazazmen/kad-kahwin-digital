<script setup>
import { addTheme } from '@/service/ThemeService';
import { useToast } from 'primevue';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const loading = ref(false);
const errors = reactive({}); // Reactive object to hold validation errors

const themeForm = reactive({
    name: '',
    theme: ''
});

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    loading.value = true;

    addTheme(themeForm)
        .then(async (res) => {
            const data = await res.json(); // parse JSON from res

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Reset form on success
                Object.keys(themeForm).forEach((key) => (themeForm[key] = ''));

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
        .finally(() => (loading.value = false));
}
</script>
<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Create Theme</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'theme-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="name" class="block"><span class="text-red-500">* </span>Name</label>
                    <InputText id="name" v-model="themeForm.name" placeholder="e.g. Watercolor" :invalid="errors.name" fluid style="width: 500px;" />
                    <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'theme-list' })" class="p-button-outlined"></Button>
                <Button label="Create" type="submit" :loading="loading"></Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
