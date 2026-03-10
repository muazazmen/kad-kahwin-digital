<script setup>
import Carousel from 'primevue/carousel'
import CarouselV1 from '@/components/CarouselV1.vue';
import { getDesignById, updateDesignById } from '@/service/DesignService';
import { getEffectById } from '@/service/EffectService';
import { getOpeningById } from '@/service/OpeningAnimationService';
import { getThemes } from '@/service/ThemeService';
import { hexToRgba } from '@/utils/color.util';
import { useYearEndCountdown } from '@/utils/countdown';
import { galleryImages } from '@/utils/gallery.util';
import { tentatives } from '@/utils/tentative.util';
import { useToast } from 'primevue';
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { backendUrl } from '@/constants/env.constant';
import { METHOD } from '@/constants/method.constant';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const { countdown } = useYearEndCountdown();

const loading = ref(false);
const previewLoading = ref(true);
const errors = reactive({});

const doorsOpen = ref(false);
const design = ref(null)

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
    primary_color: design.value?.primary_color || '#F1EAFF',
    secondary_color: design.value?.secondary_color || '#F1EAFF',
    tertiary_color: design.value?.tertiary_color || '#F1EAFF',
    thumbnails: [],
    bg_image: null,
    tentative_bg_image: null,
    theme_id: null
});

const themes = ref([]);

const fileUploadThumbnailRef = ref(null);
const fileUploadRefs = reactive({
    thumbnails: null,
    bg_image: null,
    tentative_bg_image: null
});
const particleOptions = ref(null);

async function fetchOpeningAnimation(id) {
    try {
        const response = await getOpeningById(id);
        const result = await response.json();

        if (response.ok) {
            Object.assign(animationForm, result);
            previewLoading.value = false;
        } else {
            toast.add({ severity: 'error', summary: 'Error', detail: result.message, life: 3000 });
        }
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.message, life: 3000 });
    }
}

async function fetchEffect(id) {
    try {
        const response = await getEffectById(id);
        const result = await response.json();

        particleOptions.value = JSON.parse(result.particle_config);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.message, life: 3000 });
    }
}

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
    formData.append('_method', METHOD.PUT);
    formData.append('name', designForm.name);
    formData.append('primary_color', designForm.primary_color);
    formData.append('secondary_color', designForm.secondary_color);
    formData.append('tertiary_color', designForm.tertiary_color);
    formData.append('theme_id', designForm.theme_id);

    if (designForm.thumbnails.length > 0) {
        designForm.thumbnails.forEach((imgObj, index) => {
            if (imgObj.file) {
                formData.append(`thumbnails[${index}]`, imgObj.file);
            }
        });
    }

    if (designForm.bg_image?.file) {
        formData.append('bg_image', designForm.bg_image.file);
    }

    if (designForm.tentative_bg_image?.file) {
        formData.append('tentative_bg_image', designForm.tentative_bg_image.file);
    }

    // Debug: Check FormData contents
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    loading.value = true;

    updateDesignById(route.params.id, formData)
        .then(async (res) => {
            const data = await res.json();

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                router.push({ name: 'design-list' });
            } else {
                if (data.errors) Object.assign(errors, data.errors);
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
    designForm.primary_color = '#F1EAFF';
    delete errors.primary_color;

    designForm.secondary_color = '#F1EAFF';
    delete errors.secondary_color;

    designForm.tertiary_color = '#F1EAFF';
    delete errors.tertiary_color;
}

function onFilesSelect(field, files) {
    if (!files?.length) return;
    if (field === 'thumbnails') {
        designForm.thumbnails = Array.from(files).map(file => ({
            file,
            preview: URL.createObjectURL(file)
        }));
    } else {
        console.log('executed here')
        const file = files[0];

        designForm[field] = {
            file,
            preview: URL.createObjectURL(file)
        };
    }
}

const particlesLoaded = async (container) => {
    console.log('Particles container loaded', container);
};

onMounted(async () => {
    await fetchEffect(1);
    await fetchOpeningAnimation(1);
    await fetchThemes();

    await getDesignById(route.params.id)
        .then(async (res) => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            design.value = await res.json(); // Parse JSON from response
            // Populate form with theme data

            Object.assign(designForm, {
                ...design.value,

                primary_color: design.value.primary_color || '#F1EAFF',
                secondary_color: design.value.secondary_color || '#F1EAFF',
                tertiary_color: design.value.tertiary_color || '#F1EAFF',

                thumbnails: design.value.thumbnails?.map(img => ({
                    preview: `${backendUrl}/storage/${img.image_path}`
                })) || [],

                bg_image: design.value.background_image
                    ? {
                        preview: `${backendUrl}/storage/${design.value.background_image.image_path}`
                    }
                    : null,

                tentative_bg_image: design.value.tentative_image
                    ? {
                        preview: `${backendUrl}/storage/${design.value.tentative_image.image_path}`
                    }
                    : null
            });
        })
        .catch((error) => {
            console.error('Error fetching design:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not fetch design data', life: 3000 });
        });
});
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl animation-semibold">Edit Design ({{ designForm.code }})</h1>
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
                        <InputText id="name" v-model="designForm.name" placeholder="e.g. Design 1"
                            :invalid="errors.name" :variant="designForm.name ? 'filled' : 'outlined'" fluid />
                        <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="theme_id" class="block"><span class="text-red-500">* </span>Theme</label>
                        <Select v-model="designForm.theme_id" :options="themes" optionLabel="name" optionValue="id"
                            :variant="designForm.theme_id ? 'filled' : 'outlined'" placeholder="Select Theme"
                            class="w-full" />
                        <small class="text-red-500" v-if="errors.theme_id">{{ errors.theme_id[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="primary_color" class="block"><span class="text-red-500">* </span>Primary
                            Color</label>
                        <input type="color" v-model="designForm.primary_color" @input="validateHex"
                            :invalid="errors.primary_color" fluid />
                        <button type="button" class="ml-4" @click="removeColor"><i
                                class="pi pi-times text-2xl hover:text-red-500"></i></button>
                        <small class="text-red-500" v-if="errors.primary_color">{{ errors.primary_color[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="tertiary_color" class="block"><span class="text-red-500">* </span>Secondary
                            Color</label>
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
                        <label for="thumbnails" class="block"><span class="text-red-500">* </span>Thumbnails <i
                                class="pi pi-info-circle text-xs"
                                v-tooltip="'Only PNG files. Max file size: 10MB. Image size: 540px * 1080px'"></i></label>
                        <FileUpload :ref="el => fileUploadRefs.thumbnails = el" name="thumbnails[]" multiple simple
                            :supportedFiles="'PNG'" :maxFileSize="10"
                            @files-selected="files => onFilesSelect('thumbnails', files)" />
                        <small class="text-red-500" v-if="errors.thumbnails">{{ errors.thumbnails[0] }}</small>
                        <div v-if="designForm.thumbnails.length" class="mt-4">
                            <Carousel :value="designForm.thumbnails" :numVisible="designForm.thumbnails.length > 1 ? 3 : 1" :numScroll="3" circular>
                                <template #item="{ data }">
                                    <img :src="data.preview" class="w-20 h-32 object-cover rounded-md shadow" />
                                </template>
                            </Carousel>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label for="bg_image" class="block"><span class="text-red-500">* </span>Background Image <i
                                class="pi pi-info-circle text-xs"
                                v-tooltip="'Only PNG files. Max file size: 10MB. Image size: 540px * 1080px'"></i></label>
                        <FileUpload :ref="el => fileUploadRefs.bg_image = el" name="bg_image"
                            :supportedFiles="'PNG'" :maxFileSize="10" simple
                            @files-selected="files => onFilesSelect('bg_image', files)" />
                        <small class="text-red-500" v-if="errors.bg_image">{{ errors.bg_image[0] }}</small>
                        <div v-if="designForm.bg_image" class="mt-4">
                            <img :src="designForm.bg_image.preview" class="w-24 h-40 object-cover rounded-md shadow" />
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label for="tentative_bg_image" class="block">Tentative Background Image <i
                                class="pi pi-info-circle text-xs"
                                v-tooltip="'Only PNG files. Max file size: 10MB. Image size: 540px * 1080px'"></i></label>
                        <FileUpload :ref="el => fileUploadRefs.tentative_bg_image = el" name="tentative_bg_image"
                            :supportedFiles="'PNG'" :maxFileSize="10" simple
                            @files-selected="files => onFilesSelect('tentative_bg_image', files)" />
                        <small class="text-red-500" v-if="errors.tentative_bg_image">{{ errors.tentative_bg_image[0]}}</small>
                        <div v-if="designForm.tentative_bg_image" class="mt-4">
                            <img :src="designForm.tentative_bg_image.preview"
                                class="w-24 h-40 object-cover rounded-md shadow" />
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4 gap-4">
                    <Button label="Cancel" @click="$router.push({ name: 'design-list' })"
                        class="p-button-outlined"></Button>
                    <Button label="Update" type="submit" :loading="loading"></Button>
                </div>
            </form>
        </div>

        <div class="md:col-span-1">
            <Divider layout="vertical" />
        </div>

        <!-- Preview -->
        <div v-if="!previewLoading" class="md:col-span-6 flex flex-col items-center gap-2 mb-8">
            <h1 class="text-center text-xl">Preview</h1>
            <div
                class="bg-surface-50 dark:bg-surface-800 border border-gray-300 rounded-2xl w-[420px] h-[747px] relative overflow-x-hidden overflow-y-hidden">
                <!-- Doors container -->
                <div class="perspective-1000 absolute inset-0 flex z-10 transform-3d-preserve isolate"
                    :class="{ 'pointer-events-none': doorsOpen }">
                    <!-- Shadow -->
                    <!-- NOTE: absolute bg-transparent top-1/2 left-0 -translate-y-1/2 w-1/2 h-3/4 rounded-full shadow-[8px_10px_30px_20px_rgba(0,0,0,0.2)] -->
                    <div :class="doorsOpen ? `${animationForm.shadow} shadow-none` : `${animationForm.shadow}`"></div>

                    <!-- Left door -->
                    <!-- NOTE: relative w-1/2 h-full origin-left transition-all ease-in-out duration-[2000ms] delay-500 border-r border-surface-400 z-[11] bg-[#6466f1] [transform:rotateY(150deg)] -->
                    <div :class="doorsOpen ? `${animationForm.left_door} ${animationForm.left_door_open}` : `${animationForm.left_door}`"
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
                    <div :class="doorsOpen ? `${animationForm.right_door} ${animationForm.right_door_open}` : `${animationForm.right_door}`"
                        :style="animationForm.doors_color != '#f2f2f2' ? { 'background-color': animationForm.doors_color } : { 'background-color': '#f2f2f2' }">
                    </div>
                </div>

                <!-- Content that will be revealed -->
                <div class="relative w-full h-full overflow-y-scroll overflow-x-hidden no-scrollbar">
                    <div class="relative w-full" :class="{ 'opacity-0': !doorsOpen }">
                        <!-- Main content (initially hidden) -->
                        <!-- Only show particles if options are available -->
                        <vue-particles class="absolute top-0 left-0 w-full h-full pointer-events-none" id="tsparticles"
                            @particles-loaded="particlesLoaded" :options="particleOptions" />

                        <!-- section 1 (front page) -->
                        <section class="min-h-[747px] flex flex-col items-center justify-center" :style="{
                            backgroundImage: designForm.bg_image
                                ? `url(${designForm.bg_image.preview || designForm.bg_image.preview})`
                                : 'url(/demo/images/design/intro-inside-bg-sample.png)',
                            backgroundSize: 'cover',
                            backgroundPosition: 'center',
                        }">
                            <h1 class="text-black">WALIMATUL URUS</h1>
                            <div class="flex flex-col items-center justify-center gap-2 mt-8">
                                <h1 class="text-7xl text-black"
                                    style="font-family: 'Fleur De Leah', 'Alex Brush',cursive">Adam</h1>
                                <h1 class="text-7xl text-black"
                                    style="font-family: 'Fleur De Leah', 'Alex Brush',cursive">&</h1>
                                <h1 class="text-7xl text-black"
                                    style="font-family: 'Fleur De Leah', 'Alex Brush',cursive">Hawa</h1>
                            </div>
                            <div class="mt-8 mb-16">
                                <h2 class="text-black">Sabtu | 15 Ogos 2025</h2>
                            </div>
                            <div class="absolute top-4 right-4">
                                <Button @click="doorsOpen = !doorsOpen" severity="warn" icon="pi pi-info" rounded
                                    v-tooltip="doorsOpen ? 'Close Doors' : 'Open Doors'" />
                            </div>
                        </section>

                        <!-- Section 2 (wedding details)-->
                        <section class="min-h-[747px] flex flex-col gap-8 items-center px-12 py-16"
                            :style="{ backgroundColor: designForm.primary_color ? designForm.primary_color : '#F1EAFF' }">
                            <h3 class="text-black">WALIMATUL URUS</h3>

                            <div class="flex flex-col items-center">
                                <h3 class="text-black text-2xl" style="font-family: 'Alex Brush', cursive;">Nama Bapa
                                    Pengantin</h3>
                                <h3 class="text-black text-2xl" style="font-family: 'Alex Brush', cursive;">&</h3>
                                <h3 class="text-black text-2xl" style="font-family: 'Alex Brush', cursive;">Nama Ibu
                                    Pengantin</h3>
                            </div>

                            <div class="text-center text-black max-w-md">
                                <span>
                                    Dengan penuh kesyukuran, kami mempersilakan
                                    Dato' | Datin | Tuan | Puan | Encik | Cik
                                    seisi keluarga hadir ke majlis perkahwinan anakanda kami
                                </span>
                            </div>

                            <div class="text-center">
                                <h2 class="text-black text-xl" style="font-family: 'Cinzel Decorative', serif;">Nama
                                    Penuh Pengantin
                                    Lelaki</h2>
                                <h2 class="text-black text-xl" style="font-family: 'Cinzel Decorative', serif;">&</h2>
                                <h2 class="text-black text-xl" style="font-family: 'Cinzel Decorative', serif;">Nama
                                    Penuh Pengantin
                                    Perempuan</h2>
                            </div>

                            <div class="text-center">
                                <h3 class="font-semibold text-xl text-black">Tempat</h3>
                                <p class="text-black" style="font-family: 'Cinzel Decorative';">
                                    Alamat Utama
                                </p>
                                <span class="text-black">
                                    No 35, Jalan Kasih 3, Sungai Sekamat, 43000 Kajang, Selangor Daruh Ehsan
                                </span>
                            </div>

                            <div class="text-center">
                                <h3 class="font-semibold text-xl text-black">Tarikh</h3>
                                <p class="text-black">20 Ogos 2025</p>
                                <p class="text-black italic">28 Rabiul AKhir 1447</p>
                            </div>

                            <div class="text-center">
                                <h3 class="font-semibold text-xl text-black">Waktu</h3>
                                <p class="text-black">11:00AM - 04:00PM</p>
                            </div>
                        </section>

                        <!-- Section 3 (tentative) -->
                        <section class="min-h-[450px] flex items-center justify-center" :style="{
                            backgroundImage: designForm.tentative_bg_image
                                ? `url(${designForm.tentative_bg_image.preview || designForm.bg_image.preview})`
                                : 'url(/demo/images/design/intro-inside-bg-sample.png)',
                            backgroundSize: 'cover',
                        }">
                            <div class="rounded-xl p-2" :style="{
                                backgroundColor: designForm.tertiary_color
                                    ? hexToRgba(designForm.tertiary_color, 0.3)
                                    : designForm.secondary_color
                                        ? hexToRgba(designForm.secondary_color, 0.3)
                                        : 'rgba(241, 234, 255, 0.3)'
                            }">
                                <div class="rounded-xl m-4 p-16" :style="{
                                    backgroundColor: designForm.secondary_color
                                        ? hexToRgba(designForm.secondary_color, 0.6)
                                        : designForm.primary_color
                                            ? hexToRgba(designForm.primary_color, 0.6)
                                            : 'rgba(241, 234, 255, 0.6)'
                                }">
                                    <div class="flex flex-col items-center gap-4">
                                        <h3 class="font-semibold text-xl text-black">Atur Cara Majlis</h3>
                                        <!-- TENTATIVE LIST -->
                                        <div class="flex flex-col justify-center items-center gap-4">
                                            <div v-for="(item, index) in tentatives" :key="index"
                                                class="flex flex-col items-center">
                                                <span class="text-black font-medium">{{ item.tentative }}</span>
                                                <span class="text-black text-sm">
                                                    {{ item.start_time }} - {{ item.end_time }}
                                                </span>
                                            </div>

                                            <!-- Empty fallback -->
                                            <div v-if="tentatives.length === 0" class="text-gray-500 italic text-sm">
                                                Tiada atur cara ditetapkan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="min-h-[180px] flex flex-col items-center justify-center"
                            style="background-color: #f1eafe;">
                            <div class="flex flex-col gap-8 m-8">
                                <!-- NOTE: Sini kena letak frame sepatutnya -->
                                <p class="text-black text-center">
                                    Ya Allah Ya Rahman Ya Rahim,

                                    berkatilah majlis perkahwinan ini.

                                    Limpahkanlah baraqah dan rahmatMu kepada kedua mempelai ini. Kurniakanlah mereka
                                    kelak zuriat yang
                                    soleh dan solehah. Kekalkanlah jodoh mereka hingga ke jannah.
                                </p>
                                <span class="text-black text-center text-xl"
                                    style="font-family: 'Cinzel Decorative', cursive;">#ADAMHAWA</span>
                                <!-- Countdown -->
                                <h3 class="text-black font-semibold text-center text-xl">Menghitung Hari</h3>
                                <div class="flex justify-evenly text-black text-center">
                                    <div class="flex flex-col gap-2">
                                        <span>{{ countdown.days }}</span>
                                        <span>Hari</span>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <span>{{ countdown.hours }}</span>
                                        <span>Jam</span>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <span>{{ countdown.minutes }}</span>
                                        <span>Minit</span>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <span>{{ countdown.seconds }}</span>
                                        <span>Saat</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- TODO: Section 5 (gallery) -->
                        <section class="min-h-[420px] flex flex-col items-center justify-center"
                            style="background-color: #f1eafe;">
                            <CarouselV1 :items="galleryImages" :visible="1" :loop="true" :show-arrows="false" autoplay
                                :autoPlayInterval="5000">
                                <template #default="{ item }">
                                    <div class="min-h-[420px] flex flex-col items-center justify-center">
                                        <img :src="item.src" class="w-full min-h-[420px] object-cover shadow-md" />
                                    </div>
                                </template>
                            </CarouselV1>
                        </section>
                        <!-- TODO: Section 6 (kehadiran & ucapan) -->
                        <section class="flex flex-col items-center justify-center" style="background-color: #f1eafe;">
                            <span class="text-black">test</span>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss"></style>
