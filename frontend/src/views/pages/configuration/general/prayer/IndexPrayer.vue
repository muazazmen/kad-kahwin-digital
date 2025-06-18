<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useConfirm, useToast } from 'primevue';
import { getPrayers, deletePrayer, restorePrayer } from '@/service/PrayerService';

const router = useRouter();
const toast = useToast();
const confirm = useConfirm();

const prayers = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
});

function fetchPrayers(page = 1) {
    getPrayers(page, prayers.value.per_page)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            prayers.value = {
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

function editPrayer(id) {
    router.push({ name: 'config-general-prayer-edit', params: { id } });
}

function fetchDeletePrayer(event) {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this prayer?',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
        },
        accept: () => {
            deletePrayer(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchPrayers();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Delete error:', error);
                });
        },
    });
}

function fetchRestorePrayer(event) {
    confirm.require({
        message: 'Are you sure you want to restore this prayer?',
        target: event.currentTarget,
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Restore',
            severity: 'success',
        },
        accept: () => {
            restorePrayer(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchPrayers();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Restore error:', error);
                });
        },
    });
}

function onPageChange(event) {
    fetchPrayers(event.page + 1);
}

onMounted(() => {
    fetchPrayers();
});
</script>

<template>
    <ConfirmPopup />
    <DataTable
        :value="prayers.data"
        paginator
        lazy
        :rows="prayers.per_page"
        :totalRecords="prayers.total"
        :first="(prayers.current_page - 1) * prayers.per_page"
        @page="onPageChange"
        tableStyle="min-width: 50rem"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
    >
        <template #header>
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Prayer Management</h3>
                <Button label="Add Prayer" icon="pi pi-plus" @click="$router.push({ name: 'config-general-prayer-create' })" />
            </div>
        </template>
        <template #empty><div class="text-center">No prayers found.</div></template>
        <!-- Running Number Column -->
        <Column header="No." style="width: 5%">
            <template #body="{ index }">
                {{ (prayers.current_page - 1) * prayers.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="title" header="Title" style="width: 20%"></Column>
        <Column field="prayer" header="Prayer" style="width: 55%"></Column>
        <Column field="deleted_at" header="Status" style="width: 15%">
            <template #body="{ data }">
                <Tag :value="data.deleted_at === null ? 'Active' : 'Inactive'" :severity="data.deleted_at === null ? 'success' : 'danger'" />
            </template>
        </Column>
        <Column header="Actions" style="width: 10%">
            <template #body="{ data }">
                <div class="flex justify-around">
                    <!-- Show edit button only for active records -->
                    <Button v-if="data.deleted_at === null" icon="pi pi-pencil" class="p-button-text" @click="editPrayer(data.id)" v-tooltip="'Edit Prayer'" />

                    <!-- Show delete/restore based on status -->
                    <Button
                        :icon="data.deleted_at === null ? 'pi pi-trash' : 'pi pi-replay'"
                        :class="data.deleted_at === null ? 'p-button-text p-button-danger' : 'p-button-text p-button-success'"
                        @click="data.deleted_at === null ? fetchDeletePrayer(data.id) : fetchRestorePrayer(data.id)"
                        v-tooltip="data.deleted_at === null ? 'Delete Prayer' : 'Restore Prayer'"
                    />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
