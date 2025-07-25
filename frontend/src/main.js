import { createApp, markRaw } from 'vue';
import App from './App.vue';
import router from './router';
import { definePreset } from '@primevue/themes';
import Aura from '@primevue/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Editor from 'primevue/editor';
import VueParticles from '@tsparticles/vue3';
import { loadFull } from 'tsparticles';

import '@/assets/styles.scss';
import '@/assets/tailwind.css';
import { createPinia } from 'pinia';

const app = createApp(App);
const pinia = createPinia();

pinia.use(({ store }) => {
    store.router = markRaw(router);
});

app.use(router);

const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{sky.50}',
            100: '{sky.100}',
            200: '{sky.200}',
            300: '{sky.300}',
            400: '{sky.400}',
            500: '{sky.500}',
            600: '{sky.600}',
            700: '{sky.700}',
            800: '{sky.800}',
            900: '{sky.900}',
            950: '{sky.950}'
        }
    }
});

app.use(PrimeVue, {
    theme: {
        preset: MyPreset,
        options: {
            darkModeSelector: '.app-dark'
        }
    }
});

app.use(VueParticles, {
    init: async (engine) => {
        console.log('Particles engine initialized');
        await loadFull(engine);
    }
})
app.use(ToastService);
app.use(ConfirmationService);
app.use(pinia);

app.component('Editor', Editor)

app.mount('#app');
