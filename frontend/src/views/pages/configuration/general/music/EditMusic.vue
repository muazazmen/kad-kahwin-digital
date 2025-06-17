<script setup>
import FileUpload from '@/components/FileUpload.vue';
import { METHOD } from '@/constants/method.constant';
import { getMusicById, updateMusic } from '@/service/MusicService';
import { useToast } from 'primevue';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { backendUrl } from '@/constants/env.constant';

const router = useRouter();
const route = useRoute();
const toast = useToast();

const errors = reactive({});
const musicForm = reactive({
    title: '',
    artist: '',
    album: '',
    genre: '',
    url: null
});

// Edit a ref for the FileUpload component
const fileUploadRef = ref(null);
const music = ref(null);
const currentFileName = ref('');

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    // Create FormData object
    const formData = new FormData();

    // Append all fields
    formData.append('_method', METHOD.PUT); // need to declare put if form-data
    formData.append('title', musicForm.title);
    formData.append('artist', musicForm.artist);
    formData.append('album', musicForm.album || '');
    formData.append('genre', musicForm.genre || '');

    // Only append file if it exists
    if (musicForm.url instanceof File) {
        formData.append('url', musicForm.url);
    }

    // Debug: Check FormData contents
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    updateMusic(route.params.id, formData)
        .then(async (res) => {
            const data = await res.json();
            
            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                router.push({ name: 'music-list' });
            } else {
                if (data.errors) {
                    Object.assign(errors, data.errors);
                }
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to submit form', life: 3000 });
        });
}

function onFileSelect(files) {
    if (files && files.length > 0) {
        musicForm.url = files[0];
    } else {
        musicForm.url = null;
    }
}

onMounted(() => {
    getMusicById(route.params.id)
        .then(async (res) => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            music.value = await res.json(); // Parse JSON from response
            // Populate form with music data
            Object.assign(musicForm, music.value);
            console.log('music data fetched:', musicForm);

            if (music.value.url) {
                currentFileName.value = `${backendUrl}/storage/${music.value.url}`;
            }

            console.log('currentFileName:', currentFileName.value);
        })
        .catch((error) => {
            console.error('Error fetching music:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not fetch music data', life: 3000 });
        });
});
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Edit Music</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'music-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label for="title" class="block"><span class="text-red-500">* </span>Title</label>
                    <InputText id="title" v-model="musicForm.title" :variant="musicForm.title ? 'filled' : 'outlined'" placeholder="Title" fluid />
                    <small class="text-red-500" v-if="errors.title">{{ errors.title[0] }}</small>
                </div>
                <div>
                    <label for="artist" class="block"><span class="text-red-500">* </span>Artist</label>
                    <InputText id="artist" v-model="musicForm.artist" :variant="musicForm.artist ? 'filled' : 'outlined'" placeholder="Artist" fluid />
                    <small class="text-red-500" v-if="errors.artist">{{ errors.artist[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="album" class="block">Album</label>
                    <InputText id="album" v-model="musicForm.album" :variant="musicForm.album ? 'filled' : 'outlined'" placeholder="Album" fluid />
                </div>
                <div class="col-span-2">
                    <label for="genre" class="block">Genre</label>
                    <InputText id="genre" v-model="musicForm.genre" :variant="musicForm.genre ? 'filled' : 'outlined'" placeholder="Genre" fluid />
                    <small class="text-red-500" v-if="errors.genre">{{ errors.genre[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="url" class="block">Music File</label>
                    <!-- Show current file info -->
                    <!-- KIV: make custom audio player component -->
                    <div v-if="currentFileName" class="my-2">
                        <audio controls>
                            <source :src="currentFileName" type="audio/mpeg" />
                        </audio>
                    </div>
                    <FileUpload ref="fileUploadRef" name="url" simple :supportedFiles="'MP3, WAV, OGG, AAC'" :maxFileSize="10" @files-selected="onFileSelect" />
                    <small class="text-red-500" v-if="errors.url">{{ errors.url[0] }}</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'music-list' })" class="p-button-outlined"></Button>
                <Button label="Update" type="submit"></Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
