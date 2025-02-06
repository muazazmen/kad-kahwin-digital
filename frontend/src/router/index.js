import AppLayout from '@/layout/AppLayout.vue';
import { useAuthStore } from '@/stores/auth';
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'landing',
            component: () => import('@/views/pages/Landing.vue')
        },
        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    component: () => import('@/views/Dashboard.vue'),
                    meta: { auth: true, admin: true }
                },
                {
                    path: '/configuration/general',
                    name: 'config-general',
                    component: () => import('@/views/pages/configuration/general/General.vue'),
                    meta: { auth: true, admin: true }
                },
                {
                    path: '/configuration/design',
                    name: 'config-design',
                    component: () => import('@/views/pages/configuration/design/Design.vue'),
                    meta: { auth: true, admin: true }
                },
                {
                    path: '/configuration/style',
                    name: 'config-style',
                    component: () => import('@/views/pages/configuration/style/Style.vue'),
                    meta: { auth: true, admin: true }
                }
            ]
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'notfound',
            component: () => import('@/views/pages/NotFound.vue')
        },

        {
            path: '/auth/register',
            name: 'register',
            component: () => import('@/views/pages/auth/Register.vue'),
            meta: { guest: true }
        },
        {
            path: '/auth/login',
            name: 'login',
            component: () => import('@/views/pages/auth/Login.vue'),
            meta: { guest: true }
        },
        {
            path: '/auth/access',
            name: 'accessDenied',
            component: () => import('@/views/pages/auth/Access.vue')
        },
        {
            path: '/auth/error',
            name: 'error',
            component: () => import('@/views/pages/auth/Error.vue')
        }
    ]
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();
    await authStore.fetchUser();

    if (authStore.user) {
        // Prevent "user" role from accessing admin routes
        if (authStore.user.role === 'user' && to.meta.admin) {
            return { name: 'accessDenied' }; // Redirect to access denied page
        }

        // Prevent logged-in users from accessing guest routes
        if (authStore.user && to.meta.guest) {
            return { name: 'landing' }; // Redirect logged-in users to dashboard
        }
    } else {
        // If the user is not logged in and tries to access an admin route, redirect to login
        if (to.meta.admin) {
            return { name: 'login' };
        }

        // If the user is not logged in and tries to access a authenticated route, redirect to login
        if (to.meta.auth) {
            return { name: 'login' };
        }
    }
});


export default router;