<script setup>
import FileUpload from '@/components/FileUpload.vue';
import { addMusic } from '@/service/MusicService';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { reactive } from 'vue';

const authStore = useAuthStore();

const toast = useToast();

const errors = reactive({}); // Reactive object to hold validation errors

const musicForm = reactive({

});

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    addMusic(musicForm)
        .then(async (res) => {
            const data = await res.json(); // parse JSON from res

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Reset form on success
                Object.keys(musicForm).forEach((key) => (musicForm[key] = ''));
            } else {
                if (data.errors) {
                    // Populate reactive errors object
                    Object.keys(data.errors).forEach((key) => {
                        errors[key] = data.errors[key];
                    });
                }

                console.error('Error creating music:', data.message);
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        })
        .catch((error) => {
            console.error('Error creating music:', error);
            toast.add({ severity: 'error', summary: 'Network Error', detail: 'Could not reach the server', life: 3000 });
        });
}
</script>
<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Create Music</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'music-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label for="title" class="block"><span class="text-red-500">* </span>Title</label>
                    <InputText id="title" v-model="musicForm.title" placeholder="Title" fluid />
                    <small class="text-red-500" v-if="errors.title">{{ errors.title[0] }}</small>
                </div>
                <div>
                    <label for="artist" class="block"><span class="text-red-500">* </span>Artist</label>
                    <InputText id="artist" v-model="musicForm.artist" placeholder="Artist" fluid />
                    <small class="text-red-500" v-if="errors.artist">{{ errors.artist[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="album" class="block">Album</label>
                    <InputText id="album" v-model="musicForm.album" placeholder="Album" fluid />
                </div>
                <div class="col-span-2">
                    <label for="genre" class="block">Genre</label>
                    <InputText id="genre" v-model="musicForm.genre" placeholder="Genre" fluid />
                    <small class="text-red-500" v-if="errors.genre">{{ errors.genre[0] }}</small>
                </div>
                <div class="col-span-2">
                  <label for="file" class="block">Music File</label>
                  <FileUpload simple :supportedFiles="'mp3'" :maxFileSize="10" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'music-list' })" class="p-button-outlined"></Button>
                <Button label="Create" type="submit"></Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
