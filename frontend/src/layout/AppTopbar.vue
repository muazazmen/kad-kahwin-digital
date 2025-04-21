<script setup>
import { useLayout } from '@/layout/composables/layout';
import AppConfigurator from './AppConfigurator.vue';
import { useAuthStore } from '@/stores/auth';
import { computed, ref } from 'vue';
import router from '@/router';
import { useToast } from 'primevue';

const { onMenuToggle, toggleDarkMode, isDarkTheme } = useLayout();
const authStore = useAuthStore();

const toast = useToast()

const message = computed(() => authStore.message);

const menu = ref();
const items = ref([
    {
        label: 'Profile',
        command: () => {
            router.push({ name: 'profile' });
        },
    },
    {
        label: 'Homepage',
        command: () => {
            router.push({ name: 'landing' });
        },
    },
    {
        label: 'Logout',
        command: async () => {
            try {
                await authStore.logout();
            } catch (error) {
                console.error('An unexpected error occurred:', error);
            } finally {
                toast.add({ severity: 'success', summary: 'Success', detail: message, life: 3000 });
                router.push({ name: 'landing' });
            }
        },
    },
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

</script>

<template>
    <div class="layout-topbar">
        <!-- <Toast position="top-center" /> -->
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="onMenuToggle">
                <i class="pi pi-bars"></i>
            </button>
            <router-link to="/" class="layout-topbar-logo">
                <img src="/demo/images/logo.png" alt="" />
            </router-link>
        </div>

        <div class="layout-topbar-actions">
            <div class="layout-config-menu">
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>
                <div class="relative">
                    <button
                        v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
                        type="button"
                        class="layout-topbar-action layout-topbar-action-highlight"
                    >
                        <i class="pi pi-palette"></i>
                    </button>
                    <AppConfigurator />
                </div>
            </div>

            <!-- <button
                class="layout-topbar-menu-button layout-topbar-action"
                v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
            >
                <i class="pi pi-ellipsis-v"></i>
            </button>

            <div class="layout-topbar-menu hidden lg:block">
                <div class="layout-topbar-menu-content">
                    <button type="button" class="layout-topbar-action">
                        <i class="pi pi-calendar"></i>
                        <span>Calendar</span>
                    </button>
                    <button type="button" class="layout-topbar-action">
                        <i class="pi pi-inbox"></i>
                        <span>Messages</span>
                    </button>
                    <button type="button" class="layout-topbar-action">
                        <Avatar :image="authStore.user?.avatar" shape="circle" />
                        <span>Profile</span>
                    </button>
                </div>
            </div> -->
            <div class="flex items-center gap-2">
                <button type="button" class="layout-topbar-action">
                    <i class="pi pi-bell"></i>
                </button>
                <button type="button" class="layout-topbar-action" aria-haspopup="true" aria-controls="overlay_menu" @click="toggle" v-tooltip.left="`${authStore.user?.first_name}`">
                    <i v-if="!authStore.user?.avatar" class="pi pi-spinner pi-spin"></i>
                    <img v-else :src="authStore.user?.avatar" style="width: 2rem; height: 2rem;" />
                </button>
                <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
            </div>
        </div>
    </div>
</template>
