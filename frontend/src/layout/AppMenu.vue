<script setup>
import AppMenuItem from './AppMenuItem.vue';
import { useMenuStore } from '@/stores/menu';

const menuStore = useMenuStore();
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in menuStore.model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
