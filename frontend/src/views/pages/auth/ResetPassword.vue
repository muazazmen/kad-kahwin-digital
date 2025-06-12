<script setup>
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { computed, reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const authStore = useAuthStore();

const toast = useToast();

const route = useRoute();
const router = useRouter();

const message = computed(() => authStore.message);

const loading = ref(false);

const resetPasswordForm = reactive({
    token: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submitresetPasswordForm = async () => {
    // Clear previous errors
    Object.keys(authStore.errors).forEach((key) => delete authStore.errors[key]);
    loading.value = true;
    try {
        // Call the authenticate method in the authStore
        await authStore.handlePassword('reset-password', resetPasswordForm);

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

// Capture token and email from URL when component mounts
onMounted(() => {
    const accessToken = route.query.accessToken;
    const email = route.query.email;
    if (accessToken && email) {
        resetPasswordForm.token = accessToken;
        resetPasswordForm.email = email;
    } 
});
</script>

<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="card">
            <div class="text-center mb-8">
                <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Reset Password</div>
                <span class="text-muted-color font-medium">Enter a new password for {{ resetPasswordForm.email }}</span>
            </div>

            <div class="w-full md:w-[30rem]">
                <form @submit.prevent="submitresetPasswordForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                        <input type="hidden" name="token" v-model="resetPasswordForm.token" />
                        <input type="hidden" name="email" v-model="resetPasswordForm.email" />
                        <div class="col-span-2">
                            <label for="password" class="block text-surface-900 dark:text-surface-0 font-medium text-xl">Password</label>
                            <Password id="password" v-model="resetPasswordForm.password" name="password" placeholder="Password" :toggleMask="true" fluid :feedback="false" :invalid="authStore.errors.password"></Password>
                            <small v-if="authStore.errors.password" class="text-red-500">{{ authStore.errors.password[0] }}</small>
                        </div>
                        
                        <div class="col-span-2">
                            <label for="passwordConfirmation" class="block text-surface-900 dark:text-surface-0 font-medium text-xl">Password</label>
                            <Password v-model="resetPasswordForm.password_confirmation" name="password" placeholder="Re-enter password" :toggleMask="true" fluid :feedback="false" :invalid="authStore.errors.password_confirmation"></Password>
                            <small v-if="authStore.errors.password_confirmation" class="text-red-500">{{ authStore.errors.password_confirmation[0] }}</small>
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
