<script setup>
import router from '@/router';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { computed, ref, reactive } from 'vue';
import { useRoute } from 'vue-router';

const authStore = useAuthStore();

const toast = useToast();
const route = useRoute();
const message = computed(() => authStore.message);

const loading = ref(false);

const forgotPasswordForm = reactive({
    email: ''
});

const submitForgotPasswordForm = async () => {
    // Clear previous errors
    Object.keys(authStore.errors).forEach((key) => delete authStore.errors[key]);
    loading.value = true;
    try {
        // Call the authenticate method in the authStore
        await authStore.handlePassword('forgot-password', forgotPasswordForm);

        if (authStore.response.status === 200) {
            toast.add({ severity: 'success', summary: 'Success', detail: message, life: 3000 });
            router.push({ name: 'login' });
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
                <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Forgot Password?</div>
                <span class="text-muted-color font-medium">Enter your email to reset your password</span>
            </div>

            <div class="w-full md:w-[30rem]">
                <form @submit.prevent="submitForgotPasswordForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                        <div class="col-span-2">
                            <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium">Email</label>
                            <InputText v-model="forgotPasswordForm.email" name="email" type="email" placeholder="Email address" fluid :invalid="authStore.errors.email" />
                            <small v-if="authStore.errors.email" class="text-red-500">{{ authStore.errors.email[0] }}</small>
                        </div>
                    </div>

                    <div class="mt-8">
                        <Button type="submit" label="Submit" :loading="loading" class="w-full"></Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
