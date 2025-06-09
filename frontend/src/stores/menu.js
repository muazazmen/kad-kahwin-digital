import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useAuthStore } from './auth';

export const useMenuStore = defineStore('menu', () => {
    const authStore = useAuthStore();

    const models = ref([
        {
            items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: { name: 'dashboard' } }]
        },
        {
            items: [{ label: 'Order', icon: 'pi pi-fw pi-shopping-cart', to: { name: 'order' } }]
        },
        {
            items: [{ label: 'Design', icon: 'pi pi-fw pi-desktop', to: { name: 'design' } }]
        },
        {
            items: [{ label: 'Payment', icon: 'pi pi-fw pi-wallet', to: { name: 'payment' }, meta: { superAdmin: true } }]
        },
        {
            items: [
                {
                    label: 'Configuration',
                    icon: 'pi pi-fw pi-cog',
                    items: [
                        {
                            label: 'General',
                            icon: 'pi pi-fw pi-wrench',
                            to: { name: 'config-general' }
                        },
                        {
                            label: 'Style',
                            icon: 'pi pi-fw pi-wrench',
                            to: { name: 'config-style' },
                            meta: { superAdmin: true }
                        }
                    ]
                }
            ]
        }
        // {
        //     label: 'Hierarchy',
        //     items: [
        //         {
        //             label: 'Submenu 1',
        //             icon: 'pi pi-fw pi-bookmark',
        //             items: [
        //                 {
        //                     label: 'Submenu 1.1',
        //                     icon: 'pi pi-fw pi-bookmark',
        //                     items: [
        //                         { label: 'Submenu 1.1.1', icon: 'pi pi-fw pi-bookmark' },
        //                         { label: 'Submenu 1.1.2', icon: 'pi pi-fw pi-bookmark' },
        //                         { label: 'Submenu 1.1.3', icon: 'pi pi-fw pi-bookmark' }
        //                     ]
        //                 },
        //                 {
        //                     label: 'Submenu 1.2',
        //                     icon: 'pi pi-fw pi-bookmark',
        //                     items: [{ label: 'Submenu 1.2.1', icon: 'pi pi-fw pi-bookmark' }]
        //                 }
        //             ]
        //         },
        //     ]
        // },
    ]);

    // Computed property that filters menu based on user role
    const model = computed(() => {
        if (!authStore.user) return [];

        return models.value
            .map((section) => {
                // Filter items in each section
                const filteredItems = section.items.filter((item) => {
                    // If the item has sub-items, filter them first
                    if (item.items) {
                        item.items = item.items.filter((subItem) => {
                            return !subItem.meta?.superAdmin || authStore.user.role === 'super admin';
                        });
                        // Only keep the parent item if it has sub-items remaining
                        return item.items.length > 0;
                    }

                    // For regular items, check if they're accessible
                    return !item.meta?.superAdmin || authStore.user.role === 'super admin';
                });

                // Only return sections that still have items
                return { ...section, items: filteredItems };
            })
            .filter((section) => section.items.length > 0);
    });

    return { model };
});