<script setup>
import FileUpload from '@/components/FileUpload.vue';
import { addMusic } from '@/service/MusicService';
import { useToast } from 'primevue';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const errors = reactive({});
const musicForm = reactive({
    title: '',
    artist: '',
    album: '',
    genre: '',
    url: null
});

// Create a ref for the FileUpload component
const fileUploadRef = ref(null);

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    // Create FormData object
    const formData = new FormData();
    
    // Append all fields
    formData.append('title', musicForm.title);
    formData.append('artist', musicForm.artist);
    formData.append('album', musicForm.album);
    formData.append('genre', musicForm.genre);
    
    // Only append file if it exists
    if (musicForm.url instanceof File) {
        formData.append('url', musicForm.url);
    }

    // Debug: Check FormData contents
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    addMusic(formData)
        .then(async (res) => {
            const data = await res.json();

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                
                // Reset form
                musicForm.title = '';
                musicForm.artist = '';
                musicForm.album = '';
                musicForm.genre = '';
                musicForm.url = null;
                
                // Call the clearAll method from the FileUpload component
                if (fileUploadRef.value) {
                    fileUploadRef.value.clearAll();
                }

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
                    <label for="url" class="block">Music File</label>
                    <FileUpload ref="fileUploadRef" name="url" simple :supportedFiles="'MP3, WAV, OGG, AAC'" :maxFileSize="10" @files-selected="onFileSelect" />
                    <small class="text-red-500" v-if="errors.url">{{ errors.url[0] }}</small>
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
