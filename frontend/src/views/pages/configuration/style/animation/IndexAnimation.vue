<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useConfirm, useToast } from 'primevue';
import { deleteAnimation, getAnimations, restoreAnimation } from '@/service/AnimationService';

const router = useRouter();
const toast = useToast();
const confirm = useConfirm();

const animations = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
});

function fetchAnimations(page = 1) {
    getAnimations(page, animations.value.per_page)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            animations.value = {
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

function editAnimation(id) {
    router.push({ name: 'config-style-animation-edit', params: { id } });
}

function fetchDeleteAnimation(event) {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this animation?',
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
            deleteAnimation(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchAnimations();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Delete error:', error);
                });
        }
    });
}

function fetchRestoreAnimation(event) {
    confirm.require({
        message: 'Are you sure you want to restore this animation?',
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
            restoreAnimation(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchAnimations();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Restore error:', error);
                });
        }
    });
}

function onPageChange(event) {
    fetchAnimations(event.page + 1);
}

onMounted(() => {
    fetchAnimations();
});
</script>

<template>
    <ConfirmPopup />
    <DataTable
        :value="animations.data"
        paginator
        lazy
        :rows="animations.per_page"
        :totalRecords="animations.total"
        :first="(animations.current_page - 1) * animations.per_page"
        @page="onPageChange"
        tableStyle="min-width: 50rem"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
    >
        <template #header>
            <div class="flex justify-between items-center">
                <h3 class="text-lg animation-semibold">Animation Management</h3>
                <Button label="Add Animation" icon="pi pi-plus" @click="$router.push({ name: 'config-style-animation-create' })" />
            </div>
        </template>
        <template #empty><div class="text-center">No animations found.</div></template>
        <!-- Running Number Column -->
        <Column header="No." style="width: 5%">
            <template #body="{ index }">
                {{ (animations.current_page - 1) * animations.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="name" header="Name" style="width: 20%"></Column>
        <Column field="font_path" header="URL" style="width: 10%"></Column>
        <Column field="deleted_at" header="Status" style="width: 15%">
            <template #body="{ data }">
                <Tag :value="data.deleted_at === null ? 'Active' : 'Inactive'" :severity="data.deleted_at === null ? 'success' : 'danger'" />
            </template>
        </Column>
        <Column header="Actions" style="width: 10%">
            <template #body="{ data }">
                <div class="flex justify-around">
                    <!-- Show edit button only for active records -->
                    <Button v-if="data.deleted_at === null" icon="pi pi-pencil" class="p-button-text" @click="editAnimation(data.id)" v-tooltip="'Edit Animation'" />

                    <!-- Show delete/restore based on status -->
                    <Button
                        :icon="data.deleted_at === null ? 'pi pi-trash' : 'pi pi-replay'"
                        :class="data.deleted_at === null ? 'p-button-text p-button-danger' : 'p-button-text p-button-success'"
                        @click="data.deleted_at === null ? fetchDeleteAnimation(data.id) : fetchRestoreAnimation(data.id)"
                        v-tooltip="data.deleted_at === null ? 'Delete Animation' : 'Restore Animation'"
                    />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
