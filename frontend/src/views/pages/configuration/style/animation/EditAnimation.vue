<script setup>
import FileUpload from '@/components/FileUpload.vue';
import { METHOD } from '@/constants/method.constant';
import { updateFont, getFontById } from '@/service/FontService';
import { useToast } from 'primevue';
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();
const toast = useToast();

const errors = reactive({});
const fontForm = reactive({
    name: '',
    font_family: '',
    font_type: '',
    font_path: null
});

const font = ref(null);
const previewText = ref('');
const fontSize = ref(24);

// Create a ref for the FileUpload component
const fileUploadRef = ref(null);

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    // Create FormData object
    const formData = new FormData();

    // Append all fields
    formData.append('_method', METHOD.PUT);
    formData.append('name', fontForm.name);
    formData.append('font_family', fontForm.font_family);
    formData.append('font_type', fontForm.font_type);

    // Only append file if it exists
    if (fontForm.font_path instanceof File) {
        formData.append('font_path', fontForm.font_path);
    }

    // Debug: Check FormData contents
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    updateFont(route.params.id, formData)
        .then(async (res) => {
            const data = await res.json();

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Call the clearAll method from the FileUpload component
                if (fileUploadRef.value) {
                    fileUploadRef.value.clearAll();
                }

                router.push({ name: 'font-list' });
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
        fontForm.font_path = files[0];
    } else {
        fontForm.font_path = null;
    }
}

onMounted(() => {
    getFontById(route.params.id).then(async (res) => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        font.value = await res.json();
        // Populate form with music data
        Object.assign(fontForm, font.value);
    });
});
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Edit Font</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'font-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label for="name" class="block"><span class="text-red-500">* </span>Name</label>
                    <InputText id="name" v-model="fontForm.name" :variant="fontForm.name ? 'filled' : 'outlined'" placeholder="e.g. Roboto" fluid />
                    <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
                </div>
                <div>
                    <label for="font_family" class="block"><span class="text-red-500">* </span>Font Family</label>
                    <InputText id="font_family" v-model="fontForm.font_family" :variant="fontForm.font_family ? 'filled' : 'outlined'" placeholder="e.g. Roboto" fluid />
                    <small class="text-red-500" v-if="errors.font_family">{{ errors.font_family[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="font_type" class="block"><span class="text-red-500">* </span>Font Type</label>
                    <InputText id="font_type" v-model="fontForm.font_type" :variant="fontForm.font_type ? 'filled' : 'outlined'" placeholder="e.g. Regular" fluid />
                </div>
                <div class="col-span-2">
                    <label for="font_path" class="block"><span class="text-red-500">* </span>Font File</label>
                    <FileUpload ref="fileUploadRef" name="font_path" simple :supportedFiles="'TTF, OTF, WOFF, WOFF2'" :maxFileSize="10" @files-selected="onFileSelect" />
                    <small class="text-red-500" v-if="errors.font_path">{{ errors.font_path[0] }}</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'font-list' })" class="p-button-outlined"></Button>
                <Button label="Update" type="submit"></Button>
            </div>
        </form>
        <Divider />
        <!-- Preview -->
        <div class="w-full max-w-2xl p-6">
            <h2 class="text-lg font-semibold mb-4 text-center">Font Preview</h2>

            <div class="mb-8 grid grid-cols-4 gap-4">
                <div class="col-span-3">
                    <!-- 3 columns for InputText -->
                    <InputText v-model="previewText" placeholder="Enter text for preview" fluid />
                </div>
                <div class="col-span-1">
                    <!-- 1 column for InputNumber -->
                    <InputNumber v-model="fontSize" showButtons buttonLayout="horizontal" :min="8" :max="99" fluid v-tooltip.top="'Font Size'">
                        <template #incrementbuttonicon>
                            <span class="pi pi-plus" />
                        </template>
                        <template #decrementbuttonicon>
                            <span class="pi pi-minus" />
                        </template>
                    </InputNumber>
                </div>
            </div>

            <!-- Preview Output -->
            <div class="pt-4 text-center" :style="{ fontFamily: fontForm.font_family, fontSize: fontSize + 'px' }">
                {{ previewText || 'Your text preview will appear here...' }}
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
