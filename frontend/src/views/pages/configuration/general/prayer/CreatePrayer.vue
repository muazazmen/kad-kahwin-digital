<script setup>
import { addPrayer } from '@/service/PrayerService';
import { useToast } from 'primevue';
import { computed, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const loading = ref(false);
const errors = reactive({}); // Reactive object to hold validation errors

const prayerForm = reactive({
    title: '',
    prayer: ''
});

const maxPrayerLength = 350;
const prayerCharCount = computed(() => {
    return prayerForm.prayer ? prayerForm.prayer.length : 0;
});

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    loading.value = true;

    addPrayer(prayerForm)
        .then(async (res) => {
            const data = await res.json(); // parse JSON from res

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Reset form on success
                Object.keys(prayerForm).forEach((key) => (prayerForm[key] = ''));

                // Optionally, redirect to prayer list or another page
                router.push({ name: 'prayer-list' });
            } else {
                if (data.errors) {
                    // Populate reactive errors object
                    Object.keys(data.errors).forEach((key) => {
                        errors[key] = data.errors[key];
                    });
                }

                console.error('Error creating prayer:', data.message);
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        })
        .catch((error) => {
            console.error('Error creating prayer:', error);
            toast.add({ severity: 'error', summary: 'Network Error', detail: 'Could not reach the server', life: 3000 });
        })
        .finally(() => (loading.value = false));
}
</script>
<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Create Prayer</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'prayer-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="title" class="block"><span class="text-red-500">* </span>Title</label>
                    <InputText id="title" v-model="prayerForm.title" placeholder="e.g. Doa 1" :invalid="errors.title" fluid />
                    <small class="text-red-500" v-if="errors.title">{{ errors.title[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="prayer" class="block"><span class="text-red-500">* </span>Prayer</label>
                    <Textarea v-model="prayerForm.prayer" placeholder="Prayer" autoResize :rows="8" :cols="80" :invalid="errors.prayer || prayerCharCount > maxPrayerLength" fluid />
                    <small :class="prayerCharCount > maxPrayerLength ? 'text-red-500' : 'text-gray-500'"> {{ prayerCharCount }}/{{ maxPrayerLength }} </small>
                    <small class="text-red-500" v-if="errors.prayer">{{ errors.prayer[0] }}</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'prayer-list' })" class="p-button-outlined"></Button>
                <Button label="Create" type="submit" :loading="loading"></Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
