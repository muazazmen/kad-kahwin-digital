import AppLayout from '@/layout/AppLayout.vue';
import Layout from '@/layout/Layout.vue';
import { useAuthStore } from '@/stores/auth';
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        // {
        //     path: '/',
        //     name: 'landing',
        //     component: () => import('@/views/pages/Landing.vue')
        // },
        {
            path: '/',
            component: Layout,
            children: [
                {
                    path: '/',
                    name: 'landing',
                    component: () => import('@/views/pages/Landing.vue')
                },
                {
                    path: '/profile',
                    name: 'profile',
                    component: () => import('@/views/pages/account/Account.vue')
                },
                {
                    path: '/guest',
                    name: 'guest',
                    component: () => import('@/views/pages/account/Account.vue')
                }
            ]
        },
        {
            path: '/',
            component: AppLayout,
            meta: { auth: true, admin: true },
            children: [
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    component: () => import('@/views/Dashboard.vue'),
                },
                {
                    path: '/order',
                    name: 'order',
                    component: () => import('@/views/pages/order/OrderTab.vue')
                },
                {
                    path: '/design',
                    name: 'design',
                    component: () => import('@/views/pages/design/Design.vue')
                },
                {
                    path: '/payment',
                    name: 'payment',
                    component: () => import('@/views/pages/payment/PaymentTab.vue'),
                    meta: { superAdmin: true }
                },
                {
                    path: '/configuration/general',
                    name: 'config-general',
                    component: () => import('@/views/pages/configuration/general/General.vue'),
                    redirect: { name: 'user-list' },
                    children: [
                        {
                            path: 'user',
                            name: 'user-list',
                            component: () => import('@/views/pages/configuration/general/user/IndexUser.vue'),
                        },
                        {
                            path: 'user/create',
                            name: 'config-general-user-create',
                            component: () => import('@/views/pages/configuration/general/user/CreateUser.vue')
                        },
                        {
                            path: 'user/:id/edit',
                            name: 'config-general-user-edit',
                            component: () => import('@/views/pages/configuration/general/user/EditUser.vue')
                        },
                        {
                            path: 'music',
                            name: 'music-list',
                            component: () => import('@/views/pages/configuration/general/music/IndexMusic.vue'),
                        },
                        {
                            path: 'music/create',
                            name: 'config-general-music-create',
                            component: () => import('@/views/pages/configuration/general/music/CreateMusic.vue'),
                        },
                        {
                            path: 'music/:id/edit',
                            name: 'config-general-music-edit',
                            component: () => import('@/views/pages/configuration/general/music/EditMusic.vue'),
                        },
                    ]
                },
                {
                    path: '/configuration/style',
                    name: 'config-style',
                    component: () => import('@/views/pages/configuration/style/Style.vue'),
                    meta: { auth: true, superAdmin: true }
                },
                {
                    path: '/testing',
                    name: 'testing',
                    component: () => import('@/views/pages/Empty.vue'),
                },
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
            path: '/auth/forgot-password',
            name: 'forgot-password',
            component: () => import('@/views/pages/auth/ForgotPassword.vue'),
            meta: { guest: true }
        },
        {
            path: '/auth/reset-password',
            name: 'reset-password',
            component: () => import('@/views/pages/auth/ResetPassword.vue'),
            meta: { guest: true }
        },
        {
            path: '/auth/google/redirect',
            name: 'google-redirect',
            component: () => import('@/views/pages/auth/GoogleRedirect.vue'),
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
    // const accessToken = to.query.accessToken;
    // if (accessToken) {
    //     localStorage.setItem('accessToken', accessToken);
    //     return next({ path: to.path, query: {} }); // Remove accessToken from URL and proceed
    // }

    await authStore.fetchUser();

    if (authStore.user) {
        // Check super admin routes first
        if (to.meta.superAdmin) {
            if (authStore.user.role === 'super_admin') {
                return next();
            } else {
                return next({ name: 'accessDenied' });
            }
        }

        // Then check admin routes
        if (to.meta.admin) {
            if (authStore.user.role === 'super_admin' || authStore.user.role === 'admin') {
                return next();
            } else {
                return next({ name: 'accessDenied' });
            }
        }

        // Prevent logged-in users from accessing guest routes
        if (to.meta.guest) {
            return next({ name: 'landing' });
        }
    } else {
        // If the user is not logged in and tries to access an admin route, redirect to login
        if (to.meta.admin || to.meta.superAdmin) {
            next({ name: 'login' });
            return
        }

        // If the user is not logged in and tries to access a authenticated route, redirect to login
        if (to.meta.auth) {
            next({ name: 'login' });
            return;
        }
    }

    next();
});

export default router;