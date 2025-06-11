<script setup>
import router from '@/router';
import { googleLogin } from '@/service/AuthService';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { ref } from 'vue';
import { computed, reactive } from 'vue';

const authStore = useAuthStore();

const toast = useToast();

const message = computed(() => authStore.message);

const loading = ref(false);

const loginForm = reactive({
    email: '',
    password: ''
});

function signInwithGoogle() {
    googleLogin()
}

const submitLoginForm = async () => {
    // Clear previous errors
    Object.keys(authStore.errors).forEach((key) => delete authStore.errors[key]);
    loading.value = true;
    try {
        // Call the authenticate method in the authStore
        await authStore.authenticate('login', loginForm);

        if (authStore.response.status === 200) {
            toast.add({ severity: 'success', summary: 'Success', detail: message, life: 3000 });
            router.push({ name: 'landing' });
        } else {
            toast.add({ severity: 'error', summary: 'Error', detail: message, life: 3000 });
        }
    } catch (error) {
        console.error('An unexpected error occurred:', error);
        router.push({ name: 'error' });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="card">
            <div class="text-center mb-8">
                <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Welcome to Resepsi!</div>
                <span class="text-muted-color font-medium">Login to your account</span>
            </div>

            <div class="w-full md:w-[30rem]">
                <!-- Social sign up -->
                <div class="mb-4">
                    <Button @click="signInwithGoogle" icon="pi pi-google" label="Sign in with Google" class="w-full" severity="secondary"></Button>
                </div>

                <!-- Divider with "or" text -->
                <div class="flex items-center my-6">
                    <div class="flex-1 border-t border-surface-200 dark:border-surface-700"></div>
                    <span class="px-4 text-muted-color">or</span>
                    <div class="flex-1 border-t border-surface-200 dark:border-surface-700"></div>
                </div>

                <form @submit.prevent="submitLoginForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                        <div class="col-span-2">
                            <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium">Email</label>
                            <InputText v-model="loginForm.email" name="email" type="email" placeholder="Email address" fluid :invalid="authStore.errors.email" />
                            <small v-if="authStore.errors.email" class="text-red-500">{{ authStore.errors.email[0] }}</small>
                        </div>

                        <div class="col-span-2">
                            <label for="password" class="block text-surface-900 dark:text-surface-0 font-medium text-xl">Password</label>
                            <Password id="password" v-model="loginForm.password" name="password" placeholder="Password" :toggleMask="true" fluid :feedback="false" :invalid="authStore.errors.password"></Password>
                            <small v-if="authStore.errors.password" class="text-red-500">{{ authStore.errors.password[0] }}</small>
                        </div>
                    </div>

                    <div class="mt-8">
                        <Button type="submit" label="Sign In" :loading="loading" class="w-full"></Button>
                    </div>
                </form>

                <div class="flex items-center justify-center mt-4">
                    <button type="button" class="font-medium no-underline text-right cursor-pointer text-primary" @click="$router.push({ name: 'forgot-password' })">Forgot password?</button>
                </div>

                <Divider />

                <div class="mt-4 text-center">
                    <span class="text-muted-color">Don't have an account?</span>
                    <Button label="Register" class="p-button-link" @click="$router.push({ name: 'register' })"></Button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
