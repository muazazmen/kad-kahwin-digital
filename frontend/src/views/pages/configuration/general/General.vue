<script setup>
import { ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const tabs = [
  { name: "User", path: 'user' },
  { name: "Music", path: 'music' },
  { name: "Prayer", path: 'prayer' },
  { name: "Frame", path: 'frame' },
];

// Initialize active tab
const activeTab = ref(0);

// Handle route changes and initial load
watch(() => route.path, (path) => {
  // Find which tab path exists in the current route
  const tabIndex = tabs.findIndex(tab => 
    path.includes(`/configuration/general/${tab.path}`) ||
    path.includes(`/configuration/general/${tab.path}/`)
  );
  
  activeTab.value = tabIndex >= 0 ? tabIndex : 0;
  
  // Redirect to default tab if needed
  if (path.endsWith('/configuration/general')) {
    router.replace(`/configuration/general/${tabs[0].path}`);
  }
}, { immediate: true });

// Simplified tab selection
const selectTab = (index) => {
  router.push(`/configuration/general/${tabs[index].path}`);
};
</script>

<template>
    <div class="flex space-x-4">
        <!-- Tab Content -->
        <!-- Main Content Area with Router View -->
        <div class="w-11/12 transition-all duration-300">
            <div class="card">
                <router-view></router-view>
                <!-- This will render child routes -->
            </div>
        </div>
        <!-- Sidebar for Tabs -->
        <div class="w-1/12 border-l">
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