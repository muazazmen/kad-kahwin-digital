import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMenuStore = defineStore('menu', () => {
    const model = ref([
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
            items: [{ label: 'Payment', icon: 'pi pi-fw pi-wallet', to: { name: 'payment' } }]
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
                            to: { name: 'config-style' }
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

    return { model };
});
