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
            meta: { auth: true, admin: true },
            children: [
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    component: () => import('@/views/Dashboard.vue')
                },
                {
                    path: '/profile',
                    name: 'profile',
                    component: () => import('@/views/pages/account/Account.vue')
                },
                {
                    path: '/payment',
                    name: 'payment',
                    component: () => import('@/views/pages/payment/PaymentTab.vue')
                },
                {
                    path: '/configuration/general',
                    name: 'config-general',
                    component: () => import('@/views/pages/configuration/general/General.vue')
                },
                {
                    path: '/design',
                    name: 'design',
                    component: () => import('@/views/pages/design/Design.vue')
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

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Check if there is an access accessToken in the query params
    const accessToken = to.query.accessToken;
    if (accessToken) {
        localStorage.setItem('accessToken', accessToken);
        return next({ path: to.path, query: {} }); // Remove accessToken from URL and proceed
    }

    await authStore.fetchUser();

    if (authStore.user) {
        // Prevent "user" role from accessing admin routes
        if (authStore.user.role === 'user' && to.meta.admin) {
            return { name: 'accessDenied' }; // Redirect to access denied page
        }

        // Prevent logged-in users from accessing guest routes
        // if (authStore.user && to.meta.guest) {
        //     return { name: 'landing' }; // Redirect logged-in users to dashboard
        // }
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

    next();
});

export default router;
