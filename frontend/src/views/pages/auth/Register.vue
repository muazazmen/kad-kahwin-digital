<script setup>
import router from '@/router';
import { googleSignUp } from '@/service/AuthService';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { ref } from 'vue';
import { computed } from 'vue';
import { reactive } from 'vue';

const authStore = useAuthStore();

const toast = useToast();

const message = computed(() => authStore.message);
const loading = ref(false);

const registerForm = reactive({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

function signUpWithGoogle() {
    googleSignUp();
}

const submitRegisterForm = async () => {
    // Clear previous errors
    Object.keys(authStore.errors).forEach((key) => delete authStore.errors[key]);
    loading.value = true;
    try {
        // Call the authenticate method in the authStore
        await authStore.authenticate('register', registerForm);

        if (authStore.response.status >= 200 && authStore.response.status < 300) {
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
                <span class="text-muted-color font-medium">Register a new account</span>
            </div>

            <!-- KIV: check if this sign up with google is needed -->
            <div class="w-full md:w-[30rem]">
                <!-- Social sign up -->
                <div class="mb-4">
                    <Button @click="signUpWithGoogle" icon="pi pi-google" label="Sign up with Google" class="w-full" severity="secondary"></Button>
                </div>

                <!-- Divider with "or" text -->
                <div class="flex items-center my-6">
                    <div class="flex-1 border-t border-surface-200 dark:border-surface-700"></div>
                    <span class="px-4 text-muted-color">or</span>
                    <div class="flex-1 border-t border-surface-200 dark:border-surface-700"></div>
                </div>

                <form @submit.prevent="submitRegisterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                        <div>
                            <label for="firstName" class="block text-surface-900 dark:text-surface-0 text-xl font-medium">First Name</label>
                            <InputText name="first_name" id="firstName" type="text" placeholder="Muaz" fluid v-model="registerForm.first_name" :invalid="authStore.errors.first_name" />
                            <small v-if="authStore.errors.first_name" class="text-red-500">{{ authStore.errors.first_name[0] }}</small>
                        </div>
                        <div>
                            <label for="lastName" class="block text-surface-900 dark:text-surface-0 text-xl font-medium">Last Name</label>
                            <InputText id="lastName" type="text" placeholder="Rahman" fluid v-model="registerForm.last_name" :invalid="authStore.errors.last_name" />
                            <small v-if="authStore.errors.last_name" class="text-red-500">{{ authStore.errors.last_name[0] }}</small>
                        </div>
                        <div class="col-span-2">
                            <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium">Email</label>
                            <InputText id="email" type="text" placeholder="muaz@example.com" fluid v-model="registerForm.email" :invalid="authStore.errors.email" />
                            <small v-if="authStore.errors.email" class="text-red-500">{{ authStore.errors.email[0] }}</small>
                        </div>
                        <div class="col-span-2">
                            <label for="password" class="block text-surface-900 dark:text-surface-0 font-medium text-xl">Password</label>
                            <Password id="password" v-model="registerForm.password" placeholder="e.g. Password123!" :toggleMask="true" fluid :feedback="false" :invalid="authStore.errors.password"></Password>
                            <small v-if="authStore.errors.password" class="text-red-500">{{ authStore.errors.password[0] }}</small>
                        </div>
                        <div class="col-span-2">
                            <label for="confirmPassword" class="block text-surface-900 dark:text-surface-0 font-medium text-xl">Confirm Password</label>
                            <Password id="confirmPassword" v-model="registerForm.password_confirmation" placeholder="e.g. Password123!" :toggleMask="true" fluid :feedback="false" :invalid="authStore.errors.password_confirmation"></Password>
                            <small v-if="authStore.errors.password_confirmation" class="text-red-500">{{ authStore.errors.password_confirmation[0] }}</small>
                        </div>
                    </div>

                    <div class="mt-8">
                        <Button type="submit" label="Register" :loading="loading" class="w-full"></Button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <span class="text-muted-color">Already have an account?</span>
                    <Button label="Sign In" class="p-button-link" @click="$router.push({ name: 'login' })"></Button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
