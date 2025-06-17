<script setup>
import { getUserById, updateUser } from '@/service/GeneralService';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'primevue';
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const authStore = useAuthStore();

const route = useRoute();
const router = useRouter();
const toast = useToast();

const errors = reactive({}); // Reactive object to hold validation errors

const user = ref(null);
const loading = ref(false);

const editUserForm = reactive({
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone_no: '',
    role: '',
    sso_providers: []
});

const roles = [
    { name: 'User', value: 'user' },
    { name: 'Admin', value: 'admin' },
    { name: 'Super Admin', value: 'super_admin' } // Recommended to use snake_case for values
];

// Computed property to filter roles based on current user's role
const filteredRoles = computed(() => {
    if (authStore.user?.role === 'super_admin') {
        return roles; // Show all roles for super admin
    }
    return roles.filter((role) => role.value !== 'super_admin'); // Filter out super_admin for others
});

function submitForm() {
    // Clear previous errors
    Object.keys(errors).forEach((key) => delete errors[key]);
    loading.value = true;

    updateUser(route.params.id, editUserForm)
        .then(async (res) => {
            const data = await res.json(); // parse JSON from res

            if (res.ok) {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });

                // Optionally, redirect to user list or another page
                router.push({ name: 'user-list' });
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
        })
        .finally(() => {
            loading.value = false; // Reset loading state
        });
}

onMounted(() => {
    getUserById(route.params.id)
        .then(async (res) => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            user.value = await res.json(); // Parse JSON from response
            // Populate form with user data
            Object.assign(editUserForm, user.value);
        })
        .catch((error) => {
            console.error('Error fetching user:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not fetch user data', life: 3000 });
        });
});
</script>
<template>
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Edit User</h1>
        <Button label="Back" icon="pi pi-arrow-left" @click="$router.push({ name: 'user-list' })" class="p-button-link"></Button>
    </div>

    <!-- Form -->
    <div class="flex flex-col items-center justify-center gap-4 mt-4">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label for="first_name" class="block"><span class="text-red-500">* </span>First Name</label>
                    <InputText v-model="editUserForm.first_name" placeholder="First Name" variant="filled" :disabled="authStore.user?.role !== 'super_admin' && editUserForm.role === 'super_admin'" fluid />
                    <small class="text-red-500" v-if="errors.first_name">{{ errors.first_name[0] }}</small>
                </div>
                <div>
                    <label for="last_name" class="block">Last Name</label>
                    <InputText v-model="editUserForm.last_name" placeholder="Last Name" :variant="editUserForm.last_name ? 'filled' : 'outlined'" :disabled="authStore.user?.role !== 'super_admin' && editUserForm.role === 'super_admin'" fluid />
                    <small class="text-red-500" v-if="errors.last_name">{{ errors.last_name[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="username" class="block">Username</label>
                    <InputText v-model="editUserForm.username" placeholder="Username" :variant="editUserForm.username ? 'filled' : 'outlined'" :disabled="authStore.user?.role !== 'super_admin' && editUserForm.role === 'super_admin'" fluid />
                </div>
                <div class="col-span-2">
                    <label for="email" class="block"><span class="text-red-500">* </span>Email</label>
                    <InputText id="email" v-model="editUserForm.email" type="email" placeholder="Email" variant="filled" :disabled="authStore.user?.role !== 'super_admin'" fluid />
                    <small class="text-red-500" v-if="errors.email">{{ errors.email[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="password" class="block">Password</label>
                    <InputText id="password" v-model="editUserForm.password" type="password" placeholder="Password" :disabled="authStore.user?.role !== 'super_admin' && editUserForm.role === 'super_admin'" fluid />
                    <small class="text-red-500" v-if="errors.password">{{ errors.password[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label for="phone_no" class="block">Phone No</label>
                    <InputText
                        id="phone_no"
                        v-model="editUserForm.phone_no"
                        placeholder="Format: +601123456789"
                        :variant="editUserForm.phone_no ? 'filled' : 'outlined'"
                        :disabled="authStore.user?.role !== 'super_admin' && editUserForm.role === 'super_admin'"
                        fluid
                    />
                </div>
                <div class="col-span-2">
                    <label for="role" class="block"><span class="text-red-500">* </span>Role</label>
                    <div v-if="authStore.user?.role !== 'super_admin' && editUserForm.role === 'super_admin'" class="flex flex-wrap gap-4">
                        <span>{{ editUserForm.role }}</span>
                    </div>
                    <div v-else class="flex flex-wrap gap-4">
                        <div v-for="role in filteredRoles" :key="role.value" class="flex items-center">
                            <RadioButton v-model="editUserForm.role" :inputId="role.value" name="role" :value="role.value" />
                            <label :for="role.value" class="ml-2">{{ role.name }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4 gap-4">
                <Button label="Cancel" @click="$router.push({ name: 'user-list' })" class="p-button-outlined"></Button>
                <Button label="Update" type="submit" :loading="loading"></Button>
            </div>
        </form>

        <Divider />

        <div class="flex flex-col items-center gap-4">
            <h2 class="text-lg font-semibold">Social Links</h2>
            <div class="flex flex-col gap-2">
                <ul v-if="editUserForm.sso_providers.length">
                    <li v-for="provider in editUserForm.sso_providers" :key="provider.id">
                        <span>Connected with: {{ provider.provider.charAt(0).toUpperCase() + provider.provider.slice(1) }}</span>
                    </li>
                </ul>
                <span v-else>No social accounts connected.</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add your styles here */
</style>
