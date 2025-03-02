<script setup>
import { useLayout } from '@/layout/composables/layout';
import { computed, ref, watch } from 'vue';
import Topbar from './Topbar.vue';
import { useMenuStore } from '@/stores/menu';
import { useRoute } from 'vue-router';
import Footer from './Footer.vue';

const { layoutConfig, layoutState, isSidebarActive, resetMenu } = useLayout();
const menuStore = useMenuStore();

const route = useRoute();

const outsideClickListener = ref(null);

watch(isSidebarActive, (newVal) => {
    if (newVal) {
        bindOutsideClickListener();
    } else {
        unbindOutsideClickListener();
    }
});

const containerClass = computed(() => {
    return {
        'layout-overlay': layoutConfig.menuMode === 'overlay',
        'layout-static': layoutConfig.menuMode === 'static',
        'layout-static-inactive': layoutState.staticMenuDesktopInactive && layoutConfig.menuMode === 'static',
        'layout-overlay-active': layoutState.overlayMenuActive,
        'layout-mobile-active': layoutState.staticMenuMobileActive
    };
});

function bindOutsideClickListener() {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event) => {
            if (isOutsideClicked(event)) {
                resetMenu();
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
}

function unbindOutsideClickListener() {
    if (outsideClickListener.value) {
        document.removeEventListener('click', outsideClickListener);
        outsideClickListener.value = null;
    }
}

function isOutsideClicked(event) {
    const sidebarEl = document.querySelector('.layout-sidebar');
    const topbarEl = document.querySelector('.layout-menu-button');

    return !(sidebarEl.isSameNode(event.target) || sidebarEl.contains(event.target) || topbarEl.isSameNode(event.target) || topbarEl.contains(event.target));
}

function whatsapp() {
    window.open('https://api.whatsapp.com/send/?phone=601113132848&text=Hai, saya berminat untuk menggunakan perkhidmatan Resepsi', '_blank');
}
</script>

<template>
    <div class="layout-wrapper" :class="containerClass">
        <Topbar />
        <div class="layout-home-main-container"  :class="{ 'homepage-layout-container': route.name === 'landing' }">
            <div class="layout-home-main" :class="{ 'homepage-layout': route.name !== 'landing' }">
                <!-- <Breadcrumb :home="home" :model="breadcrumbs">
                        <template #item="{ item }">
                            <router-link 
                                v-if="item.to" 
                                :to="item.to"
                                custom
                            >
                                <span :class="[item.icon, 'text-color mr-2']" />
                                <span class="text-primary font-semibold">{{ item.label }}</span>
                            </router-link>
                            <span v-else>
                                <span :class="[item.icon, 'text-color mr-2']" />
                                <span class="text-surface-700 dark:text-surface-0">{{ item.label }}</span>
                            </span>
                        </template>
                    </Breadcrumb> -->
                <router-view></router-view>
            </div>
            <Footer />
        </div>
        <div class="layout-mask animate-fadein"></div>
        <div class="fixed bottom-5 left-5 z-50">
            <Button @click="whatsapp" label="Borak dengan kami" icon="pi pi-whatsapp" severity="success" rounded />
        </div>
    </div>
</template>
