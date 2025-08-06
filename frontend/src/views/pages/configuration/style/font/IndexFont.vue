<script setup>
import { ref, onMounted } from 'vue';
import { getFontsWithTrashed, deleteFont, restoreFont } from '@/service/FontService';
import { useRouter } from 'vue-router';
import { useConfirm, useToast } from 'primevue';

const router = useRouter();
const toast = useToast();
const confirm = useConfirm();

const fonts = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
});

function fetchFonts(page = 1) {
    getFontsWithTrashed(page, fonts.value.per_page)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            fonts.value = {
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

function editFont(id) {
    router.push({ name: 'config-style-font-edit', params: { id } });
}

function fetchDeleteFont(event) {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this font?',
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
            deleteFont(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchFonts();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Delete error:', error);
                });
        }
    });
}

function fetchRestoreFont(event) {
    confirm.require({
        message: 'Are you sure you want to restore this font?',
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
            restoreFont(event)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    fetchFonts();
                    toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                })
                .catch((error) => {
                    console.error('Restore error:', error);
                });
        }
    });
}

function onPageChange(event) {
    fetchFonts(event.page + 1);
}

onMounted(() => {
    fetchFonts();
});
</script>

<template>
    <ConfirmPopup />
    <DataTable
        :value="fonts.data"
        paginator
        lazy
        :rows="fonts.per_page"
        :totalRecords="fonts.total"
        :first="(fonts.current_page - 1) * fonts.per_page"
        @page="onPageChange"
        tableStyle="min-width: 50rem"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
    >
        <template #header>
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Font Management</h3>
                <Button label="Add Font" icon="pi pi-plus" @click="$router.push({ name: 'config-style-font-create' })" />
            </div>
        </template>
        <template #empty><div class="text-center">No fonts found.</div></template>
        <!-- Running Number Column -->
        <Column header="No." style="width: 5%">
            <template #body="{ index }">
                {{ (fonts.current_page - 1) * fonts.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="name" header="Name" style="width: 20%">
            <template #body="{ data }">
                <span :style="{ fontFamily: data.font_family, fontSize: '20px' }">{{ data.name }}</span>
            </template>
        </Column>
        <Column field="font_family" header="Font Family" style="width: 20%"></Column>
        <Column field="font_type" header="Font Fallback" style="width: 20%"></Column>
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
                    <Button v-if="data.deleted_at === null" icon="pi pi-pencil" class="p-button-text" @click="editFont(data.id)" v-tooltip="'Edit Font'" />

                    <!-- Show delete/restore based on status -->
                    <Button
                        :icon="data.deleted_at === null ? 'pi pi-trash' : 'pi pi-replay'"
                        :class="data.deleted_at === null ? 'p-button-text p-button-danger' : 'p-button-text p-button-success'"
                        @click="data.deleted_at === null ? fetchDeleteFont(data.id) : fetchRestoreFont(data.id)"
                        v-tooltip="data.deleted_at === null ? 'Delete Font' : 'Restore Font'"
                    />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
