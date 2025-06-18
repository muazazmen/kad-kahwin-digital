<script setup>
import FileUpload from '@/components/FileUpload.vue';
import { addFrame } from '@/service/FrameService';
import { useToast } from 'primevue';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const loading = ref(false);
const errors = reactive({});

const frameForm = reactive({
    title: '',
    frame_path: null
});

// Create a ref for the FileUpload component
const fileUploadRef = ref(null);

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);
    
    // Create FormData object
    const formData = new FormData();
    
    // Append all fields
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

    addFrame(formData)
        .then(async (res) => {
            const data = await res.json();

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                
                // Reset form
                frameForm.title = '';
                frameForm.frame_path = null;
                
                // Call the clearAll method from the FileUpload component
                if (fileUploadRef.value) {
                    fileUploadRef.value.clearAll();
                }

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
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Create Frame</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'frame-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:w-[400px] sm:w-full">
                <div class="col-span-2">
                    <label for="title" class="block"><span class="text-red-500">* </span>Title</label>
                    <InputText id="title" v-model="frameForm.title" placeholder="e.g. Frame 1" :invalid="errors.title" fluid />
                    <small class="text-red-500" v-if="errors.title">{{ errors.title[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="frame_path" class="block"><span class="text-red-500">* </span>Frame File <i class="pi pi-info-circle text-xs" v-tooltip="'Only PNG files. Max file size: 10MB. Image size: 645px * 500px'"></i></label>
                    <FileUpload ref="fileUploadRef" name="frame_path" simple :supportedFiles="'PNG'" :maxFileSize="10" @files-selected="onFileSelect" />
                    <small class="text-red-500" v-if="errors.frame_path">{{ errors.frame_path[0] }}</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'frame-list' })" class="p-button-outlined"></Button>
                <Button label="Create" type="submit" :loading="loading"></Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
