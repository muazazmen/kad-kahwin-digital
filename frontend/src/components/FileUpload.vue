<script setup>
import { ref } from 'vue';

// Props definition
const props = defineProps({
    title: {
        type: String,
        default: ''
    },
    multiple: {
        type: Boolean,
        default: false
    },
    maxFiles: {
        type: Number,
        default: 5
    },
    supportedFiles: {
        type: String,
        default: 'JPG, PNG'
    },
    maxFileSize: {
        type: Number,
        default: 10
    },
    simple: {
        type: Boolean,
        default: false
    },
    name: {
        type: String,
        default: 'files'
    }
});

const emit = defineEmits(['files-selected']);

// File upload state
const uploadedFiles = ref([]);
const draggedIndex = ref(null);
const error = ref('');
const fileInput = ref(null);

// Supported file types and their MIME types
const fileTypeMapping = {
    pdf: 'application/pdf',
    doc: 'application/msword',
    docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    jpg: 'image/jpeg',
    jpeg: 'image/jpeg',
    png: 'image/png',
    xlsx: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    mp3: 'audio/mpeg',
    wav: 'audio/wav',
    ogg: 'audio/ogg',
    flac: 'audio/flac',
    aac: 'audio/aac',
    // Font MIME types
    ttf: 'font/ttf',
    otf: 'font/otf',
    woff: 'font/woff',
    woff2: 'font/woff2',
};

// Handle file selection
const handleFileChange = (event) => {
    const files = event.target.files;
    validateAndAddFiles(files);
};

// Validate and add files
const validateAndAddFiles = (files) => {
    const supportedExtensions = props.supportedFiles.split(',').map((type) => type.trim().toLowerCase());
    const supportedTypes = supportedExtensions.map((ext) => fileTypeMapping[ext]);

    error.value = ''; // Clear previous errors
    let hasError = false;

    // Check if multiple files are allowed
    if (!props.multiple && files.length > 1) {
        error.value = 'Only one file can be uploaded at a time.';
        hasError = true;
    }

    // Check if the total number of files exceeds the maxFiles limit
    if (props.multiple && uploadedFiles.value.length + files.length > props.maxFiles) {
        error.value = `You can only upload up to ${props.maxFiles} files.`;
        hasError = true;
    }

    if (!hasError) {
        // Clear the uploadedFiles array if multiple is false
        if (!props.multiple) {
            uploadedFiles.value = [];
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const mimeType = file.type.toLowerCase();

            // Check if the file type is supported
            if (!supportedExtensions.includes(fileExtension) && !supportedTypes.includes(mimeType)) {
                error.value = `Unsupported file type: ${file.name}. Only ${props.supportedFiles} are allowed.`;
                hasError = true;
                break;
            }

            // Check if the file size exceeds the limit
            if (file.size / (1024 * 1024) > props.maxFileSize) {
                error.value = `File size exceeds the limit of ${props.maxFileSize}MB: ${file.name}`;
                hasError = true;
                break;
            }

            // Add the file to the uploadedFiles array
            uploadedFiles.value.push(file);
        }
    }

    // Reset file input on error
    if (hasError) {
        if (fileInput.value) {
            fileInput.value.value = '';
        }
        uploadedFiles.value = props.multiple ? uploadedFiles.value.slice(0, uploadedFiles.value.length - files.length) : [];
    } else {
        // Emit the selected files to the parent component
        emit('files-selected', uploadedFiles.value);
    }
};

// Drag-and-drop logic for reordering
const dragStart = (index, event) => {
    draggedIndex.value = index;
    event.dataTransfer.setData('text/plain', index);
    event.dataTransfer.effectAllowed = 'move';
};

const drop = (targetIndex, event) => {
    event.preventDefault();
    const draggedIdx = parseInt(event.dataTransfer.getData('text/plain'));

    if (draggedIdx === targetIndex) return;

    // Reorder the files array
    const draggedItem = uploadedFiles.value.splice(draggedIdx, 1)[0];
    uploadedFiles.value.splice(targetIndex, 0, draggedItem);
    draggedIndex.value = null;

    // Emit updated files after reordering
    emit('files-selected', uploadedFiles.value);
};

// Remove file logic
const removeFile = (index) => {
    uploadedFiles.value.splice(index, 1);
    emit('files-selected', uploadedFiles.value);

    // Reset file input if no files remain
    if (uploadedFiles.value.length === 0 && fileInput.value) {
        fileInput.value.value = '';
    }
};

// Clear all files logic
const clearAll = () => {
    uploadedFiles.value = [];
    error.value = '';
    if (fileInput.value) {
        fileInput.value.value = '';
    }
    emit('files-selected', []);
};

// Format file size for display
const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

defineExpose({
    clearAll
});
</script>

<template>
    <div v-if="!simple" class="group relative">
        <div class="card-upload">
            <div class="relative p-6">
                <h3 v-if="title" class="title-upload">{{ title }}</h3>

                <!-- Dropzone -->
                <div class="group/dropzone mt-6">
                    <div class="dropzone">
                        <input type="file" :name="name" :multiple="multiple" class="absolute inset-0 z-50 h-full w-full cursor-pointer opacity-0" @change="handleFileChange" ref="fileInput" />
                        <div class="space-y-6 text-center">
                            <div class="bg-dropzone-icon-upload">
                                <svg class="dropzone-icon-upload" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <p class="instruction-upload">Browse your files here or drop</p>
                                <p class="text-sm text-slate-400">Support files: {{ supportedFiles }}</p>
                                <p class="text-xs text-slate-400">Max file size: {{ maxFileSize }}MB</p>
                                <p v-if="multiple" class="text-xs text-slate-400">Max files: {{ maxFiles }} files</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="mt-4 text-red-500 text-sm text-center">
                    {{ error }}
                </div>

                <!-- Uploaded Files List (Draggable) -->
                <div v-if="uploadedFiles.length > 0" class="mt-6 overflow-y-auto space-y-4">
                    <div v-for="(file, index) in uploadedFiles" :key="index" :class="['card-uploaded', { dragging: draggedIndex === index }]" draggable="true" @dragstart="dragStart(index, $event)" @dragover.prevent @drop="drop(index, $event)">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-card-uploaded">
                                    <svg class="icon-uploaded" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="card-uploaded-filename">{{ file.name }}</p>
                                    <p class="text-xs text-slate-400">{{ formatFileSize(file.size) }} • {{ file.type || 'Unknown type' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <button type="button" class="btn-close-card-uploaded" @click="removeFile(index)">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Clear All Button (only show if files exist) -->
                    <div class="mt-4">
                        <button type="button" @click="clearAll" class="btn-clear-upload">Clear All</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Mode -->
    <div v-else>
        <label class="block">
            <span class="sr-only">Choose file</span>
            <input type="file" :name="name" :multiple="multiple" @change="handleFileChange" ref="fileInput" class="simple-file-input" />
        </label>

        <!-- Error Message -->
        <div v-if="error" class="mt-2 text-red-500 text-sm">
            {{ error }}
        </div>

        <!-- Selected Files -->
        <div v-if="uploadedFiles.length > 0" class="mt-4">
            <h3 class="text-sm font-medium mb-2">Selected files:</h3>
            <ul class="simple-selected-files__list">
                <li v-for="(file, index) in uploadedFiles" :key="index" class="simple-selected-files__item">
                    <div class="simple-selected-files__info">
                        <span class="simple-selected-files__name">{{ file.name }}</span>
                        <span class="simple-selected-files__size">{{ formatFileSize(file.size) }}</span>
                    </div>
                    <button type="button" @click="removeFile(index)" class="simple-selected-files__remove">×</button>
                </li>
            </ul>
            <button type="button" @click="clearAll" class="simple-selected-files__clear">Clear all files</button>
        </div>
    </div>
</template>
