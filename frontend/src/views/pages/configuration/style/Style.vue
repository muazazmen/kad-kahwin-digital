<script setup>
import { ref } from 'vue';

const tabs = [
    { name: 'Fonts', content: 'This is the content of Fonts' },
    { name: 'Animations', content: 'This is the content of Animations' },
    { name: 'Effects', content: 'This is the content of Effects' },
];

const activeTab = ref(0);

const selectTab = (index) => {
    activeTab.value = index;
};
</script>
<template>
    <div class="flex space-x-4">
        <!-- Tab Content -->
        <div class="w-10/12 transition-all duration-300">
            <p class="card">{{ tabs[activeTab].content }}</p>
        </div>
        <!-- Sidebar for Tabs -->
        <div class="w-2/12 border-l">
            <ul>
                <li
                    v-for="(tab, index) in tabs"
                    :key="index"
                    @click="selectTab(index)"
                    class="p-3 cursor-pointer font-medium border-l-4 transition-all"
                    :class="activeTab === index ? 'layout-right-menu-active' : 'border-transparent layout-right-menu-inactive'"
                >
                    {{ tab.name }}
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped></style>
