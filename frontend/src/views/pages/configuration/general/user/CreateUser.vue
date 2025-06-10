<script setup>
import { addUser } from '@/service/GeneralService';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { reactive } from 'vue';

const authStore = useAuthStore();

const toast = useToast();

const errors = reactive({}); // Reactive object to hold validation errors

const userForm = reactive({
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone_no: '',
    role: ''
});

const roles = [
    { name: 'User', value: 'user' },
    { name: 'Admin', value: 'admin' },
    { name: 'Super Admin', value: 'super_admin' } // Recommended to use snake_case for values
];

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);

    addUser(userForm)
        .then(async (res) => {
            const data = await res.json(); // parse JSON from res

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Reset form on success
                Object.keys(userForm).forEach((key) => (userForm[key] = ''));
            } else {
                if (data.errors) {
                    // Populate reactive errors object
                    Object.keys(data.errors).forEach((key) => {
                        errors[key] = data.errors[key];
                    });
                }

                console.error('Error creating user:', data.message);
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        })
        .catch((error) => {
            console.error('Error creating user:', error);
            toast.add({ severity: 'error', summary: 'Network Error', detail: 'Could not reach the server', life: 3000 });
        });
}
</script>
<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Create User</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'user-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label for="first_name" class="block"><span class="text-red-500">* </span>First Name</label>
                    <InputText id="first_name" v-model="userForm.first_name" placeholder="First Name" fluid />
                    <small class="text-red-500" v-if="errors.first_name">{{ errors.first_name[0] }}</small>
                </div>
                <div>
                    <label for="last_name" class="block"><span class="text-red-500">* </span>Last Name</label>
                    <InputText id="last_name" v-model="userForm.last_name" placeholder="Last Name" fluid />
                    <small class="text-red-500" v-if="errors.last_name">{{ errors.last_name[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="username" class="block">Username</label>
                    <InputText id="username" v-model="userForm.username" placeholder="Username" fluid />
                </div>
                <div class="col-span-2">
                    <label for="email" class="block"><span class="text-red-500">* </span>Email</label>
                    <InputText id="email" v-model="userForm.email" type="email" placeholder="Email" fluid />
                    <small class="text-red-500" v-if="errors.email">{{ errors.email[0] }}</small>
                </div>
                <div>
                    <label for="password" class="block"><span class="text-red-500">* </span>Password</label>
                    <InputText id="password" v-model="userForm.password" type="password" placeholder="Password" fluid />
                    <small class="text-red-500" v-if="errors.password">{{ errors.password[0] }}</small>
                </div>
                <div>
                    <label for="password_confirmation" class="block"><span class="text-red-500">* </span>Confirm Password</label>
                    <InputText id="password_confirmation" v-model="userForm.password_confirmation" type="password" placeholder="Confirm Password" fluid />
                    <small class="text-red-500" v-if="errors.password_confirmation">{{ errors.password_confirmation[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="phone_no" class="block">Phone No</label>
                    <InputText id="phone_no" v-model="userForm.phone_no" placeholder="Format: +601123456789" fluid />
                </div>
                <div>
                    <label for="role" class="block"><span class="text-red-500">* </span>Role</label>
                    <div class="flex flex-wrap gap-4">
                        <div v-for="role in roles" :key="role.value" class="flex items-center">
                            <RadioButton v-model="userForm.role" :inputId="role.value" name="role" :value="role.value" :disabled="role.value === 'super_admin' && authStore.user.role !== 'super_admin'" />
                            <label :for="role.value" class="ml-2">{{ role.name }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'user-list' })" class="p-button-outlined"></Button>
                <Button label="Create" type="submit"></Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
