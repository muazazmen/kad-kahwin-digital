<template>

<div class="card flex flex-wrap justify-center items-end gap-4">
    <div class="card flex flex-wrap justify-center gap-4">
        <FloatLabel variant="in">
            <InputText id="in_label" v-model="value1" autocomplete="off" />
            <label for="in_label">In Label</label>
        </FloatLabel>

        <FloatLabel variant="on">
            <InputText id="on_label" v-model="value2" autocomplete="off" />
            <label for="on_label">On Label</label>
        </FloatLabel>

        <div class="card flex justify-center">
        <IftaLabel>
            <InputText id="username" v-model="value" />
            <label for="username">Iftalabel</label>
        </IftaLabel>
    </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';

const value = ref(null);
const value1 = ref(null);
const value2 = ref(null);
</script>

