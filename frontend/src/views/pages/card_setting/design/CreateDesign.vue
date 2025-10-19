<script setup>
import { addDesign } from '@/service/DesignService';
import { getOpeningById } from '@/service/OpeningAnimationService';
import { getThemes } from '@/service/ThemeService';
import { useToast } from 'primevue';
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const loading = ref(false);
const previewLoading = ref(false);
const errors = reactive({});

const doorsOpen = ref(false);

const animationForm = reactive({
  name: '',
  shadow: '',
  doors_color: '#f2f2f2',
  left_door: '',
  right_door: '',
  left_door_open: '',
  right_door_open: '',
  sealer_position: '',
  sealer_style: '',
  is_sealer_custom: false
});

const designForm = reactive({
  name: '',
  primary_color: '',
  secondary_color: '',
  tertiary_color: '',
  bg_image: null,
  theme_id: null
});

const themes = ref([]);

const fileUploadRef = ref(null);

async function fetchThemes() {
  try {
    const response = await getThemes();
    const result = await response.json();

    themes.value = result.data;
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: error.message, life: 3000 });
  }
}

function submitForm() {
  // Clear previous errors
  Object.keys(errors).forEach((key) => delete errors[key]);

  const formData = new FormData();
  formData.append('name', designForm.name);
  formData.append('primary_color', designForm.primary_color);
  formData.append('secondary_color', designForm.secondary_color);
  formData.append('tertiary_color', designForm.tertiary_color);
  formData.append('theme_id', designForm.theme_id);

  if (designForm.bg_file instanceof File) {
    formData.append('bg_image', designForm.bg_file);
  }

  // Debug: Check FormData contents
  for (let [key, value] of formData.entries()) {
    console.log(key, value);
  }

  loading.value = true;

  addDesign(formData)
    .then(async (res) => {
      const data = await res.json();

      if (res.ok) {
        toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

        // Reset form
        Object.assign(designForm, {
          name: '',
          primary_color: '',
          secondary_color: '',
          tertiary_color: '',
          bg_image: null,
          theme_id: null
        });

        // Call the clearAll method from the FileUpload component
        if (fileUploadRef.value) {
          fileUploadRef.value.clearAll();
        }

        router.push({ name: 'design-list' });
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

function validateHex(event) {
  const value = event.target.value;
  if (!/^#[0-9A-Fa-f]{6}$/.test(value)) {
    errors.primary_color = ['Invalid hex color code'];
    event.target.value = designForm.primary_color; // Reset to default if invalid
  } else {
    delete errors.primary_color; // Clear error if valid
  }
}

function removeColor() {
  designForm.primary_color = '';
  delete errors.primary_color; // Clear error if any
}

function onFileSelect(files) {
  if (files && files.length > 0) {
    const file = files[0];
    designForm.bg_image = URL.createObjectURL(file); // preview URL
    designForm.bg_file = file; // store actual File for upload
  } else {
    designForm.bg_image = null;
    designForm.bg_file = null;
  }
}

onMounted(async () => {
  await fetchThemes();

  await getOpeningById(1)
    .then(async (res) => {
      if (!res.ok) throw new Error('Network response was not ok');
      const data = await res.json();
      Object.assign(animationForm, data);
      previewLoading.value = true;
    })
    .catch((error) => {
      console.error('Error:', error);
      toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load data', life: 3000 });
    });
});
</script>

<template>
  <div class="flex justify-between">
    <h1 class="text-xl animation-semibold">Create Design</h1>
    <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'design-list' })"
      class="p-button-link"></Button>
  </div>

  <!-- Form -->
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4">
    <div class="md:col-span-5">
      <form @submit.prevent="submitForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
          <div class="col-span-2">
            <label for="name" class="block"><span class="text-red-500">* </span>Name</label>
            <InputText id="name" v-model="designForm.name" placeholder="e.g. Design 1" :invalid="errors.name" fluid />
            <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
          </div>
          <div class="col-span-2">
            <label for="theme_id" class="block"><span class="text-red-500">* </span>Theme</label>
            <Select v-model="designForm.theme_id" :options="themes" optionLabel="name" optionValue="id" placeholder="Select Theme"
              class="w-full" />
            <small class="text-red-500" v-if="errors.theme_id">{{ errors.theme_id[0] }}</small>
          </div>
          <div class="col-span-2">
            <label for="primary_color" class="block"><span class="text-red-500">* </span>Primary Color</label>
            <input type="color" v-model="designForm.primary_color" @input="validateHex" :invalid="errors.primary_color"
              fluid />
            <button type="button" class="ml-4" @click="removeColor"><i
                class="pi pi-times text-2xl hover:text-red-500"></i></button>
            <small class="text-red-500" v-if="errors.primary_color">{{ errors.primary_color[0] }}</small>
          </div>
          <div class="col-span-2">
            <label for="tertiary_color" class="block"><span class="text-red-500">* </span>Secondary Color</label>
            <input type="color" v-model="designForm.secondary_color" @input="validateHex"
              :invalid="errors.secondary_color" fluid />
            <button type="button" class="ml-4" @click="removeColor"><i
                class="pi pi-times text-2xl hover:text-red-500"></i></button>
            <small class="text-red-500" v-if="errors.tertiary_color">{{ errors.secondary_color[0] }}</small>
          </div>
          <div class="col-span-2">
            <label for="secondary_color" class="block">Tertiary Color</label>
            <input type="color" v-model="designForm.tertiary_color" @input="validateHex"
              :invalid="errors.tertiary_color" fluid />
            <button type="button" class="ml-4" @click="removeColor"><i
                class="pi pi-times text-2xl hover:text-red-500"></i></button>
            <small class="text-red-500" v-if="errors.tertiary_color">{{ errors.tertiary_color[0] }}</small>
          </div>
          <div class="col-span-2">
            <label for="bg_image" class="block"><span class="text-red-500">* </span>Background Image <i
                class="pi pi-info-circle text-xs"
                v-tooltip="'Only PNG files. Max file size: 10MB. Image size: 540px * 1080px'"></i></label>
            <FileUpload ref="fileUploadRef" name="bg_image" simple :supportedFiles="'PNG'" :maxFileSize="10"
              @files-selected="onFileSelect" />
            <small class="text-red-500" v-if="errors.bg_image">{{ errors.bg_image[0] }}</small>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mt-4 gap-4">
          <Button label="Cancel" @click="$router.push({ name: 'design-list' })" class="p-button-outlined"></Button>
          <Button label="Create" type="submit" :loading="loading"></Button>
        </div>
      </form>
    </div>

    <div class="md:col-span-1">
      <Divider layout="vertical" />
    </div>

    <!-- Preview -->
    <div v-if="previewLoading" class="md:col-span-6 flex flex-col items-center gap-2 mb-8">
      <h1 class="text-center text-xl">Preview</h1>
      <div
        class="bg-surface-50 dark:bg-surface-800 border border-gray-300 rounded-2xl p-4 w-[420px] h-[747px] relative overflow-hidden"
        :style="{
          backgroundImage: designForm.bg_image
            ? `url(${designForm.bg_image})`
            : 'url(/demo/images/design/intro-inside-bg-sample.png)',
          backgroundSize: 'cover',
          backgroundPosition: 'center'
        }">
        <!-- Doors container -->
        <div class="perspective-1000 absolute inset-0 flex z-10 transform-3d-preserve isolate">
          <!-- Shadow -->
          <!-- NOTE: absolute bg-transparent top-1/2 left-0 -translate-y-1/2 w-1/2 h-3/4 rounded-full shadow-[8px_10px_30px_20px_rgba(0,0,0,0.2)] -->
          <div :class="doorsOpen ? `${animationForm.shadow} shadow-none` : `${animationForm.shadow}`"></div>

          <!-- Left door -->
          <!-- NOTE: relative w-1/2 h-full origin-left transition-all ease-in-out duration-[2000ms] delay-500 border-r border-surface-400 z-[11] bg-[#6466f1] [transform:rotateY(150deg)] -->
          <div
            :class="doorsOpen ? `${animationForm.left_door} ${animationForm.left_door_open}` : `${animationForm.left_door}`"
            :style="animationForm.doors_color != '#f2f2f2' ? { 'background-color': animationForm.doors_color } : { 'background-color': '#f2f2f2' }">
            <!-- Sealer -->
            <!-- div ni utk sealer_position -->
            <!-- NOTE: absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2 transition-opacity duration-75 -->
            <div
              :class="doorsOpen ? `${animationForm.sealer_position} pointer-events-none` : `${animationForm.sealer_position} opacity-100 pointer-events-auto`">
              <div v-if="!animationForm.is_sealer_custom"></div>
              <!-- buat if is_sealer_custom == true, akan ada div sendiri letak nama groom, bride else default kluar dkat button sealer text -->
              <button type="button" @click="doorsOpen = true">
                <!-- NOTE: sealer style = rounded-full bg-white w-32 h-32 flex flex-col items-center justify-center shadow-[0px_0px_15px_rgba(0,0,0,0.7)] pulse-animation -->
                <div :class="`${animationForm.sealer_style} bg-[#f2f2f2]`">
                  <h2 class="text-black">Buka</h2>
                </div>
              </button>
            </div>
          </div>

          <!-- Right door -->
          <!-- NOTE: w-1/2 h-full bg-[#6466f1] origin-right transition-all ease-in-out duration-[2000ms] delay-500 -->
          <div
            :class="doorsOpen ? `${animationForm.right_door} ${animationForm.right_door_open}` : `${animationForm.right_door}`"
            :style="animationForm.doors_color != '#f2f2f2' ? { 'background-color': animationForm.doors_color } : { 'background-color': '#f2f2f2' }">
          </div>
        </div>

        <!-- Content that will be revealed -->
        <div class="flex flex-col justify-center items-center gap-4 w-full h-full relative">
          <!-- Main content (initially hidden) -->
          <div class="transition-opacity duration-1000 delay-700" :class="{ 'opacity-0': !doorsOpen }">
            <h1 class="text-black">WALIMATUL URUS</h1>
            <div class="flex flex-col items-center justify-center gap-2 mt-8">
              <h1 class="text-7xl text-black" style="font-family: 'Billabong', serif">Groom</h1>
              <h1 class="text-7xl text-black" style="font-family: 'Billabong', serif">&</h1>
              <h1 class="text-7xl text-black" style="font-family: 'Billabong', serif">Spouse</h1>
            </div>
            <div class="mt-8 mb-16">
              <h2 class="text-black">day | dd/mm/yyyy</h2>
            </div>
          </div>
        </div>

        <!-- Open/close button for demo -->
        <button @click="doorsOpen = !doorsOpen"
          class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-30 bg-slate-800 text-white px-4 py-2 rounded-lg">
          {{ doorsOpen ? 'Close Doors' : 'Open Doors' }}
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss"></style>
