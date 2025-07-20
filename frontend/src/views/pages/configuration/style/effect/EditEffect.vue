<script setup>
import { getEffectById, updateEffect } from '@/service/EffectService';
import { useToast } from 'primevue';
import { onMounted, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const loading = ref(false);
const errors = reactive({});

const effectForm = reactive({
    name: '',
    particle_config: ''
});

const doorsOpen = ref(true);
const particleOptions = ref(null);

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    loading.value = true;

    updateEffect(route.params.id, effectForm)
        .then(async (res) => {
            const data = await res.json();

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Reset form
                effectForm.name = '';
                effectForm.particle_config = '';
                particleOptions.value = null;

                router.push({ name: 'effect-list' });
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

// Watch for changes in particle_config input
watch(() => effectForm.particle_config, (newValue) => {
    if (newValue && newValue.trim() !== '') {
        try {
            particleOptions.value = JSON.parse(newValue);
        } catch (error) {
            console.error('Invalid particle configuration:', error);
            particleOptions.value = null;
        }
    } else {
        particleOptions.value = null;
    }
});

const particlesLoaded = async (container) => {
    console.log('Particles container loaded', container);
};

onMounted(() => {
    getEffectById(route.params.id).then(async (res) => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        effectForm.value = await res.json();
        // Populate form with music data
        Object.assign(effectForm, effectForm.value);
    });
});
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl animation-semibold">Create Effect</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'effect-list' })" class="p-button-link"></Button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4">
        <div class="md:col-span-5">
            <!-- Form -->
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <div class="col-span-2">
                        <label for="name" class="block"><span class="text-red-500">* </span>Name</label>
                        <InputText id="name" v-model="effectForm.name" placeholder="e.g. Particle Name" :invalid="errors.name" fluid />
                        <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="particle_config" class="block"><span class="text-red-500">* </span>Particle Config</label>
                        <Textarea 
                            id="particle_config" 
                            v-model="effectForm.particle_config" 
                            :rows="30" 
                            :invalid="errors.particle_config" 
                            placeholder="Enter JSON configuration for particles"
                            fluid 
                        />
                        <small class="text-red-500" v-if="errors.particle_config">{{ errors.particle_config[0] }}</small>
                        <small class="text-gray-500" v-else>Enter valid JSON configuration for particles</small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4 gap-4">
                    <Button label="Cancel" @click="$router.push({ name: 'effect-list' })" class="p-button-outlined"></Button>
                    <Button label="Create" type="submit" :loading="loading"></Button>
                </div>
            </form>
        </div>

        <div class="md:col-span-1"><Divider layout="vertical" /></div>

        <!-- Preview -->
        <div class="md:col-span-6 flex flex-col items-center gap-2 mb-8">
            <h1 class="text-center text-xl">Preview</h1>
            <div
                class="relative bg-surface-50 dark:bg-surface-800 border border-gray-300 rounded-2xl w-[420px] h-[747px] overflow-hidden"
                style="background-image: url('/demo/images/design/intro-inside-bg-sample.png'); background-size: cover; background-position: center"
            >
                <!-- Doors container -->
                <div class="perspective-1000 absolute inset-0 flex z-10 transform-3d-preserve isolate">
                    <!-- Shadow -->
                    <div
                        :class="
                            doorsOpen
                                ? `absolute bg-transparent top-1/2 left-0 -translate-y-1/2 w-1/2 h-3/4 rounded-full z-[11] shadow-none`
                                : `absolute bg-transparent top-1/2 left-0 -translate-y-1/2 w-1/2 h-3/4 rounded-full shadow-[8px_10px_30px_20px_rgba(0,0,0,0.2)] z-[11]`
                        "
                    ></div>

                    <!-- Left door -->
                    <div
                        :class="
                            doorsOpen
                                ? `relative w-1/2 h-full origin-left transition-all ease-in-out duration-[2000ms] delay-500 border-r border-surface-400 z-[11] bg-[#f2f2f2] [transform:rotateY(150deg)]`
                                : `relative w-1/2 h-full origin-left transition-all ease-in-out duration-[2000ms] delay-500 border-r border-surface-400 z-[11] bg-[#f2f2f2]`
                        "
                    >
                        <!-- Sealer -->
                        <div
                            :class="
                                doorsOpen
                                    ? `absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2 transition-opacity duration-75 pointer-events-none`
                                    : `absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2 transition-opacity duration-75 pointer-events-auto`
                            "
                        >
                            <button type="button" @click="doorsOpen = true">
                                <div :class="`rounded-full w-32 h-32 flex flex-col items-center justify-center shadow-[0px_0px_15px_rgba(0,0,0,0.7)] pulse-animation bg-[#f2f2f2]`">
                                    <h2 class="text-black">Buka</h2>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Right door -->
                    <div
                        :class="
                            doorsOpen
                                ? `w-1/2 h-full bg-[#f2f2f2] origin-right transition-all ease-in-out duration-[2000ms] delay-500 [transform:rotateY(-150deg)]`
                                : `w-1/2 h-full bg-[#f2f2f2] origin-right transition-all ease-in-out duration-[2000ms] delay-500`
                        "
                    ></div>
                </div>

                <!-- Content that will be revealed -->
                <div class="relative flex flex-col justify-center items-center gap-4 w-full h-full">
                    <!-- Main content (initially hidden) -->
                    <div class="transition-opacity duration-1000 delay-700" :class="{ 'opacity-0': !doorsOpen }">
                        <!-- Only show particles if options are available -->
                        <div v-if="particleOptions">
                            <vue-particles class="absolute inset-0 flex items-center justify-center w-full h-full" id="tsparticles" @particles-loaded="particlesLoaded" :options="particleOptions" />
                        </div>
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
                <button @click="doorsOpen = !doorsOpen" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-30 bg-slate-800 text-white px-4 py-2 rounded-lg">
                    {{ doorsOpen ? 'Close Doors' : 'Open Doors' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="scss"></style>