<script setup>
import router from '@/router';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';

const authStore = useAuthStore();

const route = useRoute();
const toast = useToast();

const message = computed(() => authStore.message);

const menu = ref();
const items = computed(() => {
    const baseItems = [
        {
            label: 'Profile',
            command: () => {
                router.push({ name: 'profile' });
            }
        },
        {
            label: 'Logout',
            command: async () => {
                try {
                    await authStore.logout();
                } catch (error) {
                    console.error('An unexpected error occurred:', error);
                } finally {
                    toast.add({ severity: 'success', summary: 'Success', detail: message.value, life: 3000 });
                    router.push({ name: 'landing' });
                }
            }
        }
    ];

    // Add Admin menu item if the user is an admin
    if (authStore.user?.role === 'admin' || authStore.user?.role === 'super_admin') {
        baseItems.splice(1, 0, {
            label: 'Admin',
            command: () => {
                router.push({ name: 'dashboard' });
            }
        });
    }

    return baseItems;
});

const toggle = (event) => {
    menu.value.toggle(event);
};

function smoothScroll(id) {
    document.body.click();
    document.querySelector(id).scrollIntoView({
        behavior: 'smooth'
    });
}
</script>

<template>
    <div class="bg-surface-0 dark:bg-surface-900">
        <div id="home" class="landing-wrapper overflow-hidden">
            <div class="py-6 px-6 mx-0 md:mx-10 lg:mx-18 lg:px-20 flex items-center justify-between relative lg:static">
                <a class="flex items-center" href="#">
                    <img src="/demo/images/logo.png" alt="logo" class="w-30 h-30 pb-3 pr-10" />
                </a>
                <Button
                    class="lg:!hidden"
                    text
                    severity="secondary"
                    rounded
                    v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
                >
                    <i class="pi pi-bars !text-2xl"></i>
                </Button>
                <div class="items-center bg-surface-0 dark:bg-surface-900 grow justify-between hidden lg:flex absolute lg:static w-full left-0 top-full px-12 lg:px-0 z-20 rounded-border">
                    <ul class="list-none p-0 m-0 flex lg:items-center select-none flex-col lg:flex-row cursor-pointer gap-8 ml-auto mr-12">
                        <li>
                            <router-link :to="{ name: 'landing' }" class="px-0 py-4 text-surface-900 dark:text-surface-0 font-medium text-xl">
                                <span>UTAMA</span>
                            </router-link>
                        </li>
                        <li>
                            <a @click="smoothScroll('#features')" class="px-0 py-4 text-surface-900 dark:text-surface-0 font-medium text-xl">
                                <span>KAD DIGITAL</span>
                            </a>
                        </li>
                        <li>
                            <a @click="smoothScroll('#pricing')" class="px-0 py-4 text-surface-900 dark:text-surface-0 font-medium text-xl">
                                <span>PAKEJ</span>
                            </a>
                        </li>
                        <li>
                            <a @click="smoothScroll('#highlights')" class="px-0 py-4 text-surface-900 dark:text-surface-0 font-medium text-xl">
                                <span>TUTORIAL</span>
                            </a>
                        </li>
                        <li>
                            <a @click="smoothScroll('#highlights')" class="px-0 py-4 text-surface-900 dark:text-surface-0 font-medium text-xl">
                                <span>HUBUNGI KAMI</span>
                            </a>
                        </li>
                    </ul>
                    <div class="flex border-t lg:border-t-0 border-surface py-4 lg:py-0 mt-4 lg:mt-0 gap-2">
                        <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
                        <button v-if="authStore.user" type="button" class="layout-topbar-action" aria-haspopup="true" aria-controls="overlay_menu" @click="toggle" v-tooltip.left="`${authStore.user?.first_name}`">
                            <img :src="authStore.user?.avatar" class="w-8 h-8 object-cover rounded-full" v-tooltip.left="`${authStore.user?.first_name}`" />
                        </button>
                        <Button v-else label="LOG MASUK" as="router-link" :to="{ name: 'login' }" rounded />
                    </div>
                </div>
            </div>

            <!-- TODO: add breadcrumb -->
            <!-- Hero Section: Hidden if route is 'landing' -->
            <div v-if="route.name !== 'landing'" id="hero" class="flex flex-col overflow-hidden">
                <div class="flex justify-center md:justify-end">
                    <img src="/demo/images/landing/bg-img-breadcrumb.jpg" alt="Hero Image" class="w-full" style="height: 250px; background-position: center; object-fit: cover" />
                </div>
            </div>
        </div>
    </div>
</template>
