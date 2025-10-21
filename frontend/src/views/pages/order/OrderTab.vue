<script setup>
import MarkdownEditor from '@/components/markdown/MarkdownEditor.vue';
import { ref } from 'vue'

const markdown = ref('# Hello World\n\nWrite your **markdown** here!')
</script>

<template>
    <MarkdownEditor v-model="markdown" placeholder="Start writing..." :show-preview="true" min-height="400px" />
</template>