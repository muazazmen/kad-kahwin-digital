<script setup>
import FileUpload from '@/components/FileUpload.vue';
import { addAnimation } from '@/service/AnimationService';
import { useToast } from 'primevue';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const loading = ref(false);
const errors = reactive({});

const animationForm = reactive({
    name: '',
    font_family: '',
    font_type: '',
    font_path: null
});

// Create a ref for the FileUpload component
const fileUploadRef = ref(null);

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    // Create FormData object
    const formData = new FormData();

    // Append all fields
    formData.append('name', animationForm.name);
    formData.append('font_family', animationForm.font_family);
    formData.append('font_type', animationForm.font_type);

    // Only append file if it exists
    if (animationForm.font_path instanceof File) {
        formData.append('font_path', animationForm.font_path);
    }

    // Debug: Check FormData contents
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    loading.value = true;

    addAnimation(formData)
        .then(async (res) => {
            const data = await res.json();

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Reset form
                animationForm.name = '';
                animationForm.font_family = '';
                animationForm.font_type = '';
                animationForm.font_path = null;

                // Call the clearAll method from the FileUpload component
                if (fileUploadRef.value) {
                    fileUploadRef.value.clearAll();
                }

                router.push({ name: 'animation-list' });
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
        });
}

function onFileSelect(files) {
    if (files && files.length > 0) {
        animationForm.font_path = files[0];
    } else {
        animationForm.font_path = null;
    }
}
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl animation-semibold">Create Animation</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'animation-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4">
        <div class="md:col-span-4">
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <div>
                        <label for="name" class="block"><span class="text-red-500">* </span>Name</label>
                        <InputText id="name" v-model="animationForm.name" placeholder="e.g. Roboto" :invalid="errors.name" fluid />
                        <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
                    </div>
                    <div>
                        <label for="font_family" class="block"><span class="text-red-500">* </span>Animation Family</label>
                        <InputText id="font_family" v-model="animationForm.font_family" placeholder="e.g. Roboto" :invalid="errors.font_family" fluid />
                        <small class="text-red-500" v-if="errors.font_family">{{ errors.font_family[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="font_type" class="block"><span class="text-red-500">* </span>Animation Type</label>
                        <InputText id="font_type" v-model="animationForm.font_type" placeholder="e.g. Regular" :invalid="errors.font_type" fluid />
                    </div>
                    <div class="col-span-2">
                        <label for="font_path" class="block"><span class="text-red-500">* </span>Animation File</label>
                        <FileUpload ref="fileUploadRef" name="font_path" simple :supportedFiles="'TTF, OTF, WOFF, WOFF2'" :maxFileSize="10" @files-selected="onFileSelect" />
                        <small class="text-red-500" v-if="errors.font_path">{{ errors.font_path[0] }}</small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4 gap-4">
                    <Button label="Cancel" @click="$router.push({ name: 'animation-list' })" class="p-button-outlined"></Button>
                    <Button label="Create" type="submit" :loading="loading"></Button>
                </div>
            </form>
        </div>

        <div class="md:col-span-1"><Divider layout="vertical" /></div>

        <div class="md:col-span-7 flex flex-col items-center gap-4 my-8">
            <h1 class="text-center">Preview</h1>
            <div class="bg-surface-50 dark:bg-surface-800 border border-gray-300 rounded-2xl  p-4 w-[420px] h-[747px]" 
            style="
            background-image: url('/demo/images/design/intro-inside-bg-sample.png'); 
            background-size: cover;
            background-position: center;">
                <div class="flex flex-col justify-center items-center gap-4 w-full h-full">
                    <h1 class="text-black">WALIMATUL URUS</h1>
                    <div class="flex flex-col items-center justify-center gap-2 mt-8">
                        <h1 class="text-7xl text-black" style="font-family: 'Billabong', serif;">Groom</h1>
                        <h1 class="text-7xl text-black" style="font-family: 'Billabong', serif;">&</h1>
                        <h1 class="text-7xl text-black" style="font-family: 'Billabong', serif;">Spouse</h1>
                    </div>
                    <div class="mt-8 mb-16">
                        <h2 class="text-black">day | dd/mm/yyyy</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
