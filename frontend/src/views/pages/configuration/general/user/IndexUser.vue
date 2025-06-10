<script setup>
import { ref, onMounted } from 'vue';
import { getUsers } from '@/service/GeneralService';

const users = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
});

function fetchUsers(page = 1) {
    getUsers(page, users.value.per_page)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            users.value = {
                data: data.data,
                current_page: data.current_page,
                per_page: data.per_page,
                total: data.total,
                last_page: data.last_page
            };
        })
        .catch((error) => {
            console.error('Fetch error:', error);
        });
}

function onPageChange(event) {
    fetchUsers(event.page + 1);
}

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <DataTable
        :value="users.data"
        paginator
        lazy
        :rows="users.per_page"
        :totalRecords="users.total"
        :first="(users.current_page - 1) * users.per_page"
        @page="onPageChange"
        tableStyle="min-width: 50rem"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
    >
        <template #header>
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">User Management</h3>
                <Button label="Add User" icon="pi pi-plus" @click="$router.push({ name: 'config-general-user-create' })" />
            </div>
        </template>
        <!-- Running Number Column -->
        <Column header="No." style="width: 5%">
            <template #body="{ index }">
                {{ (users.current_page - 1) * users.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="first_name" header="First Name" style="width: 20%"></Column>
        <Column field="last_name" header="Last Name" style="width: 20%"></Column>
        <Column field="email" header="Email" style="width: 20%"></Column>
        <Column field="phone_no" header="Phone No" style="width: 15%"></Column>
        <Column field="role" header="Role" style="width: 10%"></Column>
        <Column header="Actions" style="width: 10%">
            <template #body="{ data }">
                <div class="flex justify-around">
                    <Button icon="pi pi-pencil" class="p-button-text" @click="editUser(data)" />
                    <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="deleteUser(data)" />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
