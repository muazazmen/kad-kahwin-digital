<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useConfirm, useToast } from 'primevue';
import { deleteOpening, getOpeningsWithTrashed, restoreOpening } from '@/service/OpeningAnimationService';

const router = useRouter();
const toast = useToast();
const confirm = useConfirm();

const openings = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
});

function fetchOpenings(page = 1) {
    getOpeningsWithTrashed(page, openings.value.per_page)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            openings.value = {
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

function editOpening(id) {
    router.push({ name: 'config-style-opening-edit', params: { id } });
}

function fetchDeleteOpening(event) {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this opening?',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            deleteOpening(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchOpenings();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Delete error:', error);
                });
        }
    });
}

function fetchRestoreOpening(event) {
    confirm.require({
        message: 'Are you sure you want to restore this opening?',
        target: event.currentTarget,
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Restore',
            severity: 'success'
        },
        accept: () => {
            restoreOpening(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchOpenings();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Restore error:', error);
                });
        }
    });
}

function onPageChange(event) {
    fetchOpenings(event.page + 1);
}

onMounted(() => {
    fetchOpenings();
});
</script>

<template>
    <ConfirmPopup />
    <DataTable
        :value="openings.data"
        paginator
        lazy
        :rows="openings.per_page"
        :totalRecords="openings.total"
        :first="(openings.current_page - 1) * openings.per_page"
        @page="onPageChange"
        tableStyle="min-width: 50rem"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
    >
        <template #header>
            <div class="flex justify-between items-center">
                <h3 class="text-lg opening-semibold">Opening Animation Management</h3>
                <Button label="Add Opening" icon="pi pi-plus" @click="$router.push({ name: 'config-style-opening-create' })" />
            </div>
        </template>
        <template #empty><div class="text-center">No openings found.</div></template>
        <!-- Running Number Column -->
        <Column header="No." style="width: 5%">
            <template #body="{ index }">
                {{ (openings.current_page - 1) * openings.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="name" header="Name" style="width: 20%"></Column>
        <Column field="is_sealer_custom" header="Custom Sealer" style="width: 10%">
            <template #body="{ data }">
                <Badge :value="data.is_sealer_custom ? 'Yes' : 'No'" :severity="data.is_sealer_custom ? 'info' : 'warn'" />
            </template>
        </Column>
        <Column field="deleted_at" header="Status" style="width: 15%">
            <template #body="{ data }">
                <Tag :value="data.deleted_at === null ? 'Active' : 'Inactive'" :severity="data.deleted_at === null ? 'success' : 'danger'" />
            </template>
        </Column>
        <Column header="Actions" style="width: 10%">
            <template #body="{ data }">
                <div>
                    <!-- Show edit button only for active records -->
                    <Button v-if="data.deleted_at === null" icon="pi pi-pencil" class="p-button-text" @click="editOpening(data.id)" v-tooltip="'Edit Opening'" />

                    <!-- Show delete/restore based on status -->
                    <Button
                        :icon="data.deleted_at === null ? 'pi pi-trash' : 'pi pi-replay'"
                        :class="data.deleted_at === null ? 'p-button-text p-button-danger' : 'p-button-text p-button-success'"
                        @click="data.deleted_at === null ? fetchDeleteOpening(data.id) : fetchRestoreOpening(data.id)"
                        v-tooltip="data.deleted_at === null ? 'Delete Opening' : 'Restore Opening'"
                    />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
