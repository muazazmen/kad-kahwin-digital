<script setup>
import { addAnimation } from '@/service/AnimationService';
import { useToast } from 'primevue';
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const loading = ref(false);
const errors = reactive({});

const animationForm = reactive({
    name: '',
    left_door: '',
    right_door: '',
    left_door_open: '',
    right_door_open: '',
    sealer_position: '',
    sealer_text: ''
});

const doorsOpen = ref(false);

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    loading.value = true;

    addAnimation(animationForm)
        .then(async (res) => {
            const data = await res.json();

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Reset form
                animationForm.name = '';
                animationForm.left_door = '';
                animationForm.right_door = '';
                animationForm.left_door_open = '';
                animationForm.right_door_open = '';
                animationForm.sealer_position = '';
                animationForm.sealer_text = '';

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
</script>

<template>
    <div class="flex justify-between">
        <h1 class="text-xl animation-semibold">Create Opening Animation</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'animation-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4">
        <div class="md:col-span-5">
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <!-- TODO: Name, check boleh tak retrieve data sekaligus dlm <div></div>, left and right door, name badge, colorpicker right door = left door -->
                    <div class="col-span-2">
                        <label for="name" class="block"><span class="text-red-500">* </span>Name</label>
                        <InputText id="name" v-model="animationForm.name" placeholder="e.g. Door 1" :invalid="errors.name" fluid />
                        <small class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="left_door" class="block"><span class="text-red-500">* </span>Left Door Animation</label>
                        <Textarea id="left_door" v-model="animationForm.left_door" placeholder="e.g. Roboto" :rows="5" :invalid="errors.left_door" fluid />
                        <small class="text-red-500" v-if="errors.left_door">{{ errors.left_door[0] }}</small>
                    </div>
                    <!-- TODO: tak perlu open class sbb boleh merge dgn style lain ikut cara sealer_position -->
                    <div class="col-span-2">
                        <label for="left_door_open" class="block"><span class="text-red-500">* </span>Left Door Animation (Open)</label>
                        <Textarea id="left_door_open" v-model="animationForm.left_door_open" placeholder="e.g. Roboto" :rows="2" :invalid="errors.left_door_open" fluid />
                        <small class="text-red-500" v-if="errors.left_door_open">{{ errors.left_door_open[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="right_door" class="block"><span class="text-red-500">* </span>Right Door Animation</label>
                        <Textarea id="right_door" v-model="animationForm.right_door" placeholder="e.g. Roboto" :rows="5" :invalid="errors.right_door" fluid />
                        <small class="text-red-500" v-if="errors.right_door">{{ errors.right_door[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="right_door_open" class="block"><span class="text-red-500">* </span>Right Door Animation (Open)</label>
                        <Textarea id="right_door_open" v-model="animationForm.right_door_open" placeholder="e.g. Roboto" :rows="2" :invalid="errors.right_door_open" fluid />
                        <small class="text-red-500" v-if="errors.right_door_open">{{ errors.right_door_open[0] }}</small>
                    </div>
                    <div class="col-span-2">
                        <label for="sealer_position" class="block"><span class="text-red-500">* </span>Sealer Style</label>
                        <Textarea id="sealer_position" v-model="animationForm.sealer_position" placeholder="e.g. Regular" :rows="2" :invalid="errors.sealer_position" fluid />
                    </div>
                    <div class="col-span-2">
                        <label for="sealer_text" class="block"><span class="text-red-500">* </span>Sealer Button Text</label>
                        <Textarea id="sealer_text" v-model="animationForm.sealer_text" placeholder="e.g. Regular" :rows="3" :invalid="errors.sealer_text" fluid />
                    </div>
                    <div class="col-span-2">
                        <label for="custom_sealer" class="block"><span class="text-red-500">* </span>Custom Sealer</label>
                        <Textarea id="custom_sealer" v-model="animationForm.custom_sealer" placeholder="e.g. Regular" :rows="3" :invalid="errors.custom_sealer" fluid />
                    </div>
                    <!-- KIV: make custom component InputEditor -->
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4 gap-4">
                    <Button label="Cancel" @click="$router.push({ name: 'animation-list' })" class="p-button-outlined"></Button>
                    <Button label="Create" type="submit" :loading="loading"></Button>
                </div>
            </form>
        </div>

        <div class="md:col-span-1"><Divider layout="vertical" /></div>

        <!-- Preview -->
        <div class="md:col-span-6 flex flex-col items-center gap-2 mb-8">
            <h1 class="text-center text-xl">Preview</h1>
            <div
                class="bg-surface-50 dark:bg-surface-800 border border-gray-300 rounded-2xl p-4 w-[420px] h-[747px] relative overflow-hidden"
                style="background-image: url('/demo/images/design/intro-inside-bg-sample.png'); background-size: cover; background-position: center"
            >
                <!-- Doors container -->
                <div class="perspective-1000 absolute inset-0 flex z-10 transform-3d-preserve">
                    <!-- Shadow -->
                    <div class="absolute bg-transparent top-1/2 left-0 -translate-y-1/2 w-1/2 h-3/4 rounded-full shadow-[8px_10px_30px_20px_rgba(0,0,0,0.2)]" :class="{ 'transform -translate-x-full shadow-none': doorsOpen }"></div>

                    <!-- Left door -->
                    <div class="relative w-1/2 h-full bg-[#6466f1] origin-left transition-all ease-in-out duration-[2000ms] delay-500 border-r border-surface-400 z-[11]" :class="{ '[transform:rotateY(-150deg)]': doorsOpen }">
                        <!-- Sealer -->
                         <!-- div ni utk sealer_position -->
                        <div :class="doorsOpen ? `${animationForm.sealer_position} pointer-events-none z-[12]` : 'absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2 transition-opacity duration-75'">
                            <div v-if="animationForm.custom_sealer" :class="animationForm.custom_sealer"></div>
                            <!-- buat if cutom_sealer == true, akan ada div sendiri letak nama groom, bride else default kluar dkat button sealer text -->
                            <button type="button" @click="doorsOpen = true">
                                <!-- NOTE: sealer style = rounded-full bg-white w-32 h-32 flex flex-col items-center justify-center shadow-[0px_0px_15px_rgba(0,0,0,0.7)] pulse-animation -->
                                <div :class="animationForm.sealer_style">
                                    <h2 class="text-black">Buka</h2>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Right door -->
                    <div class="w-1/2 h-full bg-[#6466f1] origin-right transition-all ease-in-out duration-[2000ms] delay-500 z-10" :class="{ '[transform:rotateY(150deg)]': doorsOpen }"></div>
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
                <button @click="doorsOpen = !doorsOpen" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-30 bg-slate-800 text-white px-4 py-2 rounded-lg">
                    {{ doorsOpen ? 'Close Doors' : 'Open Doors' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="scss">

</style>
