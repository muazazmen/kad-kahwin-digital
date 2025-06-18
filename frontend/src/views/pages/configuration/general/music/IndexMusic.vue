<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useConfirm, useToast } from 'primevue';
import { deleteMusic, getMusics, restoreMusic } from '@/service/MusicService';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const toast = useToast();
const confirm = useConfirm();

const authStore = useAuthStore();

const musics = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
});

function fetchMusics(page = 1) {
    getMusics(page, musics.value.per_page)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            musics.value = {
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

function editMusic(id) {
    router.push({ name: 'config-general-music-edit', params: { id } });
}

function fetchDeleteMusic(event) {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this music?',
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
            deleteMusic(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchMusics();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Delete error:', error);
                });
        },
    });
}

function fetchRestoreMusic(event) {
    confirm.require({
        message: 'Are you sure you want to restore this music?',
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
            restoreMusic(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchMusics();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Restore error:', error);
                });
        },
    });
}

function onPageChange(event) {
    fetchMusics(event.page + 1);
}

onMounted(() => {
    fetchMusics();
});
</script>

<template>
    <ConfirmPopup />
    <DataTable
        :value="musics.data"
        paginator
        lazy
        :rows="musics.per_page"
        :totalRecords="musics.total"
        :first="(musics.current_page - 1) * musics.per_page"
        @page="onPageChange"
        tableStyle="min-width: 50rem"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
    >
        <template #header>
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Music Management</h3>
                <Button label="Add Music" icon="pi pi-plus" @click="$router.push({ name: 'config-general-music-create' })" />
            </div>
          </template>
          <template #empty><div class="text-center">No musics found.</div></template>
        <!-- Running Number Column -->
        <Column header="No." style="width: 5%">
            <template #body="{ index }">
                {{ (musics.current_page - 1) * musics.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="title" header="Title" style="width: 20%"></Column>
        <Column field="artist" header="Artist" style="width: 20%"></Column>
        <Column field="music_path" header="URL" style="width: 20%"></Column>
        <Column field="deleted_at" header="Status" style="width: 15%">
            <template #body="{ data }">
                <Tag :value="data.deleted_at === null ? 'Active' : 'Inactive'" :severity="data.deleted_at === null ? 'success' : 'danger'" />
            </template>
        </Column>
        <Column header="Actions" style="width: 10%">
            <template #body="{ data }">
                <div class="flex gap-2">
                    <!-- Show edit button only for active records -->
                    <Button v-if="data.deleted_at === null" icon="pi pi-pencil" class="p-button-text" @click="editMusic(data.id)"
                    :disabled="data.title === 'Youtube' && authStore.user.role !== 'super_admin'"
                    v-tooltip="'Edit Music'" />

                    <!-- Show delete/restore based on status -->
                    <Button
                        :icon="data.deleted_at === null ? 'pi pi-trash' : 'pi pi-replay'"
                        :class="data.deleted_at === null ? 'p-button-text p-button-danger' : 'p-button-text p-button-success'"
                        @click="data.deleted_at === null ? fetchDeleteMusic(data.id) : fetchRestoreMusic(data.id)"
                        :disabled="data.title === 'Youtube' && authStore.user.role !== 'super_admin'"
                        v-tooltip="data.deleted_at === null ? 'Delete Music' : 'Restore Music'"
                    />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
