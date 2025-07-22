<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useConfirm, useToast } from 'primevue';
import { deleteFrame, getFrames, restoreFrame } from '@/service/FrameService';
import { useAuthStore } from '@/stores/auth';
import { backendUrl } from '@/constants/env.constant';

const authStore = useAuthStore();

const router = useRouter();
const toast = useToast();
const confirm = useConfirm();

const frames = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
});

function fetchFrames(page = 1) {
    getFrames(page, frames.value.per_page)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            frames.value = {
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

function editFrame(id) {
    router.push({ name: 'config-general-frame-edit', params: { id } });
}

function fetchDeleteFrame(event) {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this frame?',
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
            deleteFrame(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchFrames();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Delete error:', error);
                });
        }
    });
}

function fetchRestoreFrame(event) {
    confirm.require({
        message: 'Are you sure you want to restore this frame?',
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
            restoreFrame(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchFrames();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Restore error:', error);
                });
        }
    });
}

function onPageChange(event) {
    fetchFrames(event.page + 1);
}

onMounted(() => {
    fetchFrames();
});
</script>

<template>
    <ConfirmPopup />
    <DataTable
        :value="frames.data"
        paginator
        lazy
        :rows="frames.per_page"
        :totalRecords="frames.total"
        :first="(frames.current_page - 1) * frames.per_page"
        @page="onPageChange"
        tableStyle="min-width: 50rem"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
    >
        <template #header>
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Theme Management</h3>
                <Button label="Add Theme" icon="pi pi-plus" @click="$router.push({ name: 'config-general-frame-create' })" />
            </div>
        </template>
        <template #empty><div class="text-center">No frames found.</div></template>
        <!-- Running Number Column -->
        <Column header="No." style="width: 5%">
            <template #body="{ index }">
                {{ (frames.current_page - 1) * frames.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="title" header="Title" style="width: 20%"></Column>
        <Column field="frame_path" header="Path" style="width: 20%">
            <template #body="{ data }">
                <div class="flex items-center gap-2">
                    <img :src="`${backendUrl}/storage/${data.frame_path}`" :alt="data.title" class="w-32 h-1w-32 object-cover" />
                </div>
            </template>
        </Column>
        <Column field="deleted_at" header="Status" style="width: 15%">
            <template #body="{ data }">
                <Tag :value="data.deleted_at === null ? 'Active' : 'Inactive'" :severity="data.deleted_at === null ? 'success' : 'danger'" />
            </template>
        </Column>
        <Column header="Actions" style="width: 10%">
            <template #body="{ data }">
                <div class="flex gap-2">
                    <!-- Show edit button only for active records -->
                    <Button v-if="data.deleted_at === null" icon="pi pi-pencil" class="p-button-text" @click="editFrame(data.id)" :disabled="data.title === 'No Theme' && authStore.user.role !== 'super_admin'" v-tooltip="'Edit Theme'" />

                    <!-- Show delete/restore based on status -->
                    <Button
                        :icon="data.deleted_at === null ? 'pi pi-trash' : 'pi pi-replay'"
                        :class="data.deleted_at === null ? 'p-button-text p-button-danger' : 'p-button-text p-button-success'"
                        @click="data.deleted_at === null ? fetchDeleteFrame(data.id) : fetchRestoreFrame(data.id)"
                        :disabled="data.title === 'No Theme' && authStore.user.role !== 'super_admin'"
                        v-tooltip="data.deleted_at === null ? 'Delete Theme' : 'Restore Theme'"
                    />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
