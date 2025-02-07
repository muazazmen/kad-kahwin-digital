<script setup>
import { useLayout } from '@/layout/composables/layout';
import { computed, ref, watch } from 'vue';
import AppFooter from './AppFooter.vue';
import AppSidebar from './AppSidebar.vue';
import AppTopbar from './AppTopbar.vue';
import { useMenuStore } from '@/stores/menu';
import { useRoute } from 'vue-router';

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

// const home = ref({
//     icon: 'pi pi-home',
//     route: '/dashboard'
// });

// const breadcrumbs = computed(() => {
//     let items = [];

//     // Special handling for dashboard/home route
//     if (route.name === 'dashboard') {
//         return [{ 
//             label: 'Dashboard', 
//             icon: 'pi pi-fw pi-home',
//             to: { name: 'dashboard' }
//         }];
//     }

//     function findItem(menuItems, parentItems = []) {
//         for (const item of menuItems) {
//             // Create a new item with route information if it exists
//             const currentItem = {
//                 label: item.label,
//                 icon: item.icon,
//                 ...(item.to && { to: item.to }) // Only add 'to' if it exists
//             };
            
//             const currentPath = [...parentItems, currentItem];
            
//             if (item.to && item.to.name === route.name) {
//                 return currentPath;
//             }
            
//             if (item.items) {
//                 const found = findItem(item.items, currentPath);
//                 if (found.length) {
//                     return found;
//                 }
//             }
//         }
//         return [];
//     }

//     // Search through the menu model
//     for (const menu of menuStore.model) {
//         // Include the menu label if it exists
//         const parentItems = menu.label ? [{
//             label: menu.label,
//             icon: menu.icon
//         }] : [];
        
//         if (menu.items) {
//             const found = findItem(menu.items, parentItems);
//             if (found.length) {
//                 items = found;
//                 break;
//             }
//         }
//     }

//     return items;
// });

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
</script>

<template>
    <div class="layout-wrapper" :class="containerClass">
        <app-topbar></app-topbar>
        <app-sidebar></app-sidebar>
        <div class="layout-main-container">
            <div class="layout-main">
                <!-- TODO: Breadcrumb -->
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
            <app-footer></app-footer>
        </div>
        <div class="layout-mask animate-fadein"></div>
    </div>
</template>
