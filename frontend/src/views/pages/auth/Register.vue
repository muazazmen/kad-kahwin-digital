<script setup>
import router from '@/router';
import { useAuthStore } from '@/stores/auth';
import { Form } from '@primevue/forms';
import { useToast } from 'primevue';
import { computed } from 'vue';
import { reactive } from 'vue';

const authStore = useAuthStore();

const toast = useToast();

const message = computed(() => authStore.message);

const registerForm = reactive({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const resolver = ({ values }) => {
    const errors = {};

    if (!values.first_name) {
        // TODO: error message should take from backend,
        // currently not working with Form primevue v4.2.5
        errors.first_name = [{ message: 'First name is required' }];
    }

    return {
        errors
    };
};

const handleRegister = async () => {
    try {
        // Call the authenticate method in the authStore
        await authStore.authenticate('register', registerForm);

        if (authStore.response.status >= 200 && authStore.response.status < 300) {
            toast.add({ severity: 'success', summary: 'Success', detail: message, life: 3000 });
            router.push({ name: 'dashboard' });
        } else {
            toast.add({ severity: 'error', summary: 'Error', detail: message, life: 3000 });
        }
    } catch (error) {
        console.error('An unexpected error occurred:', error);
    }
};
</script>

<template>
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Welcome to Resepsi!</div>
                        <span class="text-muted-color font-medium">Register a new account</span>
                    </div>

                    <Form v-slot="$form" :initialValues="registerForm" :resolver class="w-full" @submit="handleRegister">
                        <label for="firstName" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">First Name</label>
                        <InputText name="first_name" id="firstName" type="text" placeholder="First Name" class="w-full md:w-[30rem]" v-model="registerForm.first_name" />
                        <Message v-if="authStore.errors.first_name" severity="error" size="small" variant="simple">{{ authStore.errors.first_name[0] }}</Message>
                        
                        <label for="lastName" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2 mt-4">Last Name</label>
                        <InputText id="lastName" type="text" placeholder="Last Name" class="w-full md:w-[30rem] mb-6" v-model="registerForm.last_name" />
                        
                        <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email</label>
                        <InputText id="email" type="text" placeholder="Email address" class="w-full md:w-[30rem] mb-6" v-model="registerForm.email" />

                        <label for="password" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Password</label>
                        <Password id="password" v-model="registerForm.password" placeholder="Password" :toggleMask="true" class="mb-8" fluid :feedback="false"></Password>
                        
                        <label for="confirmPassword" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Confirm Password</label>
                        <Password id="confirmPassword" v-model="registerForm.password_confirmation" placeholder="Confirm Password" :toggleMask="true" class="mb-8" fluid :feedback="false"></Password>

                        <Button type="submit" label="Register" class="w-full"></Button>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>
