<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useConfirm, useToast } from 'primevue';
import { useAuthStore } from '@/stores/auth';
import { backendUrl } from '@/constants/env.constant';
import { deleteDesign, getDesignsWithTrashed, restoreDesign } from '@/service/DesignService';

const authStore = useAuthStore();

const router = useRouter();
const toast = useToast();
const confirm = useConfirm();

const designs = ref({
  data: [],
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1
});

function fetchDesigns(page = 1) {
  getDesignsWithTrashed(page, designs.value.per_page)
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then((data) => {
      designs.value = {
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

function editDesign(id) {
  router.push({ name: 'card-setting-design-edit', params: { id } });
}

function fetchDeleteDesign(event) {
  confirm.require({
    target: event.currentTarget,
    message: 'Are you sure you want to delete this design?',
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
      deleteDesign(event)
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          fetchDesigns();
          toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
        })
        .catch((error) => {
          console.error('Delete error:', error);
        });
    }
  });
}

function fetchRestoreDesign(event) {
  confirm.require({
    message: 'Are you sure you want to restore this design?',
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
      restoreDesign(event)
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          fetchDesigns();
          toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
        })
        .catch((error) => {
          console.error('Restore error:', error);
        });
    }
  });
}

function onPageChange(event) {
  fetchDesigns(event.page + 1);
}

function getTextColor(hexColor) {
  if (!hexColor) return '#000';

  // Convert HEX → RGB
  const hex = hexColor.replace('#', '');
  const r = parseInt(hex.substring(0, 2), 16);
  const g = parseInt(hex.substring(2, 4), 16);
  const b = parseInt(hex.substring(4, 6), 16);

  // Compute brightness (YIQ formula)
  const brightness = (r * 299 + g * 587 + b * 114) / 1000;

  // Return dark or light text based on brightness
  return brightness > 150 ? '#1a1a1a' : '#f5f5f5';
}

onMounted(() => {
  fetchDesigns();
});
</script>

<template>
  <ConfirmPopup />
  <DataTable :value="designs.data" paginator lazy :rows="designs.per_page" :totalRecords="designs.total"
    :first="(designs.current_page - 1) * designs.per_page" @page="onPageChange" tableStyle="min-width: 50rem"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records">
    <template #header>
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold">Design Management</h3>
        <Button label="Add Design" icon="pi pi-plus" @click="$router.push({ name: 'card-setting-design-create' })" />
      </div>
    </template>
    <template #empty>
      <div class="text-center">No designs found.</div>
    </template>
    <!-- Running Number Column -->
    <Column header="#" style="width: 5%">
      <template #body="{ index }">
        {{ (designs.current_page - 1) * designs.per_page + index + 1 }}
      </template>
    </Column>
    <Column field="code" header="Code" style="width: 10%"></Column>
    <Column field="name" header="Name" style="width: 10%"></Column>
    <Column field="primary_color" header="Primary Color" style="width: 10%">
      <template #body="{ data }">
        <div class="flex items-center justify-center h-10 rounded-md" :style="{
          backgroundColor: data.primary_color,
          color: getTextColor(data.primary_color)
        }">
          {{ data.primary_color }}
        </div>
      </template>
    </Column>

    <Column field="secondary_color" header="Secondary Color" style="width: 10%">
      <template #body="{ data }">
        <div class="flex items-center justify-center h-10 rounded-md" :style="{
          backgroundColor: data.secondary_color,
          color: getTextColor(data.secondary_color)
        }">
          {{ data.secondary_color }}
        </div>
      </template>
    </Column>

    <Column field="tertiary_color" header="Tertiary Color" style="width: 10%">
      <template #body="{ data }">
        <div class="flex items-center justify-center h-10 rounded-md" :style="{
          backgroundColor: data.tertiary_color || '#ffffff',
          color: getTextColor(data.tertiary_color || '#ffffff')
        }">
          {{ data.tertiary_color || '-' }}
        </div>
      </template>
    </Column>
    <Column field="images" header="Thumbnail" style="width: 20%">
      <template #body="{ data }">
        <div v-if="data.thumbnails && data.thumbnails.length" class="w-40">
          <Carousel :value="data.thumbnails" :numVisible="1" :numScroll="1" circular autoplayInterval="4000">
            <template #item="{ data: img }">
              <img :src="`${backendUrl}/storage/${img.image_path}`" alt="Design Thumbnail"
                class="w-40 h-24 object-cover rounded-md shadow" />
            </template>
          </Carousel>
        </div>
        <div v-else class="text-center text-gray-400 italic">No images</div>
      </template>
    </Column>
    <Column field="theme_name" header="Theme" style="width: 10%"></Column>
    <Column field="deleted_at" header="Status" style="width: 10%">
      <template #body="{ data }">
        <Tag :value="data.deleted_at === null ? 'Active' : 'Inactive'"
          :severity="data.deleted_at === null ? 'success' : 'danger'" />
      </template>
    </Column>
    <Column header="Actions" style="width: 15%">
      <template #body="{ data }">
        <div class="flex gap-2">
          <!-- Show edit button only for active records -->
          <Button v-if="data.deleted_at === null" icon="pi pi-pencil" class="p-button-text" @click="editDesign(data.id)"
            :disabled="data.title === 'No Design' && authStore.user.role !== 'super_admin'" v-tooltip="'Edit Design'" />

          <!-- Show delete/restore based on status -->
          <Button :icon="data.deleted_at === null ? 'pi pi-trash' : 'pi pi-replay'"
            :class="data.deleted_at === null ? 'p-button-text p-button-danger' : 'p-button-text p-button-success'"
            @click="data.deleted_at === null ? fetchDeleteDesign(data.id) : fetchRestoreDesign(data.id)"
            :disabled="data.title === 'No Design' && authStore.user.role !== 'super_admin'"
            v-tooltip="data.deleted_at === null ? 'Delete Design' : 'Restore Design'" />
        </div>
      </template>
    </Column>
  </DataTable>
</template>

<style scoped>
/* Add your styles here */
</style>
