<script setup>
import { useAuthStore } from '@/stores/auth';
import { Form } from '@primevue/forms';
import { useToast } from 'primevue';
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();

const toast = useToast();
const router = useRouter();

const loginForm = reactive({
    email: '',
    password: '',
});

const resolver = ({ values }) => {
    const errors = {};

    if (!values.email) {
        // TODO: error message should take from backend,
        // currently not working with Form primevue v4.2.5
        errors.email = [{ message: 'email is required' }];
    }

    if (!values.password) {
        errors.password = [{ message: 'password is required' }];
    }

    return {
        errors
    };
};

const handleLogin = async () => {
    try {
        // Call the authenticate method in the authStore
        await authStore.authenticate('login', loginForm);

        if (authStore.response.status === 200) {
            toast.add({ severity: 'success', summary: 'Success', detail: authStore.message, life: 3000 });
            router.push({ name: 'dashboard' });
        } else {
            toast.add({ severity: 'error', summary: 'Error', detail: authStore.message, life: 3000 });
        }
    } catch (error) {
        console.error('An unexpected error occurred:', error);
    }
};

onMounted(() => {
    authStore.errors = {};
    authStore.message = {};
});
</script>

<template>
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Welcome to Resepsi!</div>
                        <span class="text-muted-color font-medium">Sign in to continue</span>
                    </div>
                    <!-- <Toast position="top-center" /> -->

                    <Form v-slot="$form" :initialValues="loginForm" :resolver @submit="handleLogin">
                        <label for="email1" class="block text-surface-900 dark:text-surface-0 text-xl font-medium">Email</label>
                        <InputText v-model="loginForm.email" name="email" id="email1" type="text" placeholder="Email address" class="w-full md:w-[30rem]" />
                        <Message v-if="authStore.errors.email" severity="error" size="small" variant="simple">{{ authStore.errors.email[0] }}</Message>

                        <label for="password1" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mt-4">Password</label>
                        <Password id="password1" v-model="loginForm.password" name="password" placeholder="Password" :toggleMask="true" fluid :feedback="false"></Password>
                        <Message v-if="authStore.errors.password" severity="error" size="small" variant="simple">{{ authStore.errors.password[0] }}</Message>

                        <div class="flex items-center justify-between mt-4 mb-8 gap-8">
                            <div class="flex items-center">
                                <Checkbox v-model="checked" id="rememberme1" binary class="mr-2"></Checkbox>
                                <label for="rememberme1">Remember me</label>
                            </div>
                            <span class="font-medium no-underline ml-2 text-right cursor-pointer text-primary">Forgot password?</span>
                        </div>
                        <Button type="submit" label="Sign In" class="w-full"></Button>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
