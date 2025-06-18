<script setup>
import FileUpload from '@/components/FileUpload.vue';
import { METHOD } from '@/constants/method.constant';
import { useToast } from 'primevue';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { backendUrl } from '@/constants/env.constant';
import { getFrameById, updateFrame } from '@/service/FrameService';

const router = useRouter();
const route = useRoute();
const toast = useToast();

const loading = ref(false);
const errors = reactive({});

const frameForm = reactive({
    title: '',
    frame_path: null
});

// Edit a ref for the FileUpload component
const fileUploadRef = ref(null);
const frame = ref(null);
const currentFileName = ref('');

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    // Create FormData object
    const formData = new FormData();

    // Append all fields
    formData.append('_method', METHOD.PUT); // need to declare put if form-data
    formData.append('title', frameForm.title);

    // Only append file if it exists
    if (frameForm.frame_path instanceof File) {
        formData.append('frame_path', frameForm.frame_path);
    }

    // Debug: Check FormData contents
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    loading.value = true;

    updateFrame(route.params.id, formData)
        .then(async (res) => {
            const data = await res.json();
            
            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                router.push({ name: 'frame-list' });
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
        })
        .finally(() => {
            loading.value = false;
        })
}

function onFileSelect(files) {
    if (files && files.length > 0) {
        frameForm.frame_path = files[0];
    } else {
        frameForm.frame_path = null;
    }
}

onMounted(() => {
    getFrameById(route.params.id)
        .then(async (res) => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            frame.value = await res.json(); // Parse JSON from response
            // Populate form with frame data
            Object.assign(frameForm, frame.value);

            if (frame.value.frame_path) {
                currentFileName.value = `${backendUrl}/storage/${frame.value.frame_path}`;
            }
        })
        .catch((error) => {
            console.error('Error fetching frame:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not fetch frame data', life: 3000 });
        });
});
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Edit Frame</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'frame-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label for="title" class="block"><span class="text-red-500">* </span>Title</label>
                    <InputText id="title" v-model="frameForm.title" :variant="frameForm.title ? 'filled' : 'outlined'" placeholder="Title" fluid />
                    <small class="text-red-500" v-if="errors.title">{{ errors.title[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="frame_path" class="block">Frame File</label>
                    <FileUpload ref="fileUploadRef" name="frame_path" simple :supportedFiles="'PNG'" :maxFileSize="10" @files-selected="onFileSelect" />
                    <small class="text-red-500" v-if="errors.frame_path">{{ errors.frame_path[0] }}</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'frame-list' })" class="p-button-outlined"></Button>
                <Button label="Update" type="submit" :loading="loading"></Button>
            </div>
        </form>

        <Divider />

        <!-- Image preview -->
        <div>
            <h2 class="text-lg font-semibold mb-4 text-center">Frame Preview</h2>
            <div class="flex justify-center">
                <img v-if="currentFileName" :src="currentFileName" alt="Frame Preview" class="max-w-full max-h-96" />
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
