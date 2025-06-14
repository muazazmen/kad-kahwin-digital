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
    default: 5 // Default maximum number of files allowed
  },
  supportedFiles: {
    type: String,
    default: 'JPG, PNG'
  },
  maxFileSize: {
    type: Number,
    default: 10
  },
  uploadLabelBtn: {
    type: String,
    default: 'Upload'
  },
  uploadIconBtn: {
    type: String,
    default: '' // Optional uploadIconBtn class (e.g., "pi pi-upload")
  },
  loading: {
    type: Boolean,
    default: false
  },
  simple: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['files-selected', 'upload-clicked']);

// File upload state
const uploadedFiles = ref([]);
const draggedIndex = ref(null);
const error = ref(''); // Error message state

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
};

// Handle upload button click
const handleUploadClick = () => {
  if (uploadedFiles.value.length === 0) {
    error.value = 'Please select files before uploading.';
    return;
  }
  error.value = ''; // Clear any error message
  emit('upload-clicked', uploadedFiles.value);

  // Clear the uploadedFiles array after uploading
  clearAll();
};

// Handle file selection
const handleFileChange = (event) => {
  const files = event.target.files;
  validateAndAddFiles(files);
};

// Validate and add files
const validateAndAddFiles = (files) => {
  const supportedExtensions = props.supportedFiles.split(',').map(type => type.trim().toLowerCase());
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
  };
  const supportedTypes = supportedExtensions.map(ext => fileTypeMapping[ext]);

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

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const fileExtension = file.name.split('.').pop().toLowerCase(); // Get the file extension
    const mimeType = file.type.toLowerCase(); // Get the MIME type

    // Check if the file type is supported (either by extension or MIME type)
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

    // Clear the uploadedFiles array if multiple is false
    if (!props.multiple) {
      uploadedFiles.value = []; // Reset the array to allow only one file
    }

    // Add the file to the uploadedFiles array
    uploadedFiles.value.push(file);
  }

  // Emit the selected files to the parent component
  if (!hasError) {
    emit('files-selected', uploadedFiles.value);
  }

  // Reset the file input element on error
  if (hasError) {
    if (fileInput.value) {
      fileInput.value.value = '';
    }
  } else if (!props.multiple) {
    // Clear the file input after adding a single file
    if (fileInput.value) {
      fileInput.value.value = '';
    }
  }
};

// Drag-and-drop logic
const dragStart = (index, event) => {
  draggedIndex.value = index;
  event.dataTransfer.setData('text/plain', index);
  event.dataTransfer.effectAllowed = 'move';
};

const drop = (targetIndex, event) => {
  event.preventDefault();
  const draggedIdx = event.dataTransfer.getData('text/plain');

  if (draggedIdx === targetIndex) return;

  // Reorder the files array
  const draggedItem = uploadedFiles.value.splice(draggedIdx, 1)[0];
  uploadedFiles.value.splice(targetIndex, 0, draggedItem);
  draggedIndex.value = null;
};

// Remove file logic
const removeFile = (index) => {
  uploadedFiles.value.splice(index, 1); // Remove the file at the specified index
};

// Clear all files logic
const clearAll = () => {
  uploadedFiles.value = []; // Reset the uploadedFiles array
  error.value = ''; // Clear any error message
  if (fileInput.value) {
    fileInput.value.value = ''; // Reset the file input element
  }
};
</script>
<template>
  <div v-if="!simple" class="group relative">
    <div :class="['card-upload', { 'simple-mode': simple }]">
      <!-- File Upload Section -->
      <div class="relative p-6">
        <h3 v-if="!simple" class="title-upload">{{ title }}</h3>

        <!-- Dropzone -->
        <div class="group/dropzone mt-6">
          <div :class="['dropzone', { 'simple-dropzone': simple }]">
            <input type="file" :multiple="multiple" class="absolute inset-0 z-50 h-full w-full cursor-pointer opacity-0"
              @change="handleFileChange" ref="fileInput" />
            <div class="space-y-6 text-center">
              <div :class="['bg-dropzone-icon-upload', { 'simple-icon': simple }]">
                <svg class="dropzone-icon-upload" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
              </div>
              <div class="space-y-2">
                <p :class="['instruction-upload', { 'simple-instruction': simple }]">
                  {{ simple ? 'Click to upload' : 'Browse your files here or drop' }}
                </p>
                <p v-if="!simple" class="text-sm text-slate-400">Support files: {{ supportedFiles }}</p>
                <p v-if="!simple" class="text-xs text-slate-400">Max file size: {{ maxFileSize }}MB</p>
                <p v-if="!simple && multiple" class="text-xs text-slate-400">Max files: {{ maxFiles }} files</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mt-4 text-red-500 text-sm text-center">
          {{ error }}
        </div>

        <!-- Uploaded Files List (Draggable) -->
        <div v-if="!simple" class="mt-6 overflow-y-auto space-y-4">
          <div v-for="(file, index) in uploadedFiles" :key="index"
            :class="['card-uploaded', { dragging: draggedIndex === index }]"
            draggable="true" @dragstart="dragStart(index, $event)" @dragover.prevent @drop="drop(index, $event)">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="bg-card-uploaded">
                  <svg v-if="file.status === 'complete'" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <svg v-else class="icon-uploaded" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                  </svg>
                </div>
                <div>
                  <p class="card-uploaded-filename">{{ file.name }}</p>
                  <p class="text-xs text-slate-400">{{ file.size }} • {{ file.type }}</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <button class="btn-close-card-uploaded" @click="removeFile(index)">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Simple mode file list (just names with remove buttons) -->
        <div v-if="simple && uploadedFiles.length > 0" class="mt-4 space-y-2">
          <div v-for="(file, index) in uploadedFiles" :key="index" class="flex items-center justify-between py-1">
            <span class="text-sm truncate max-w-[80%]">{{ file.name }}</span>
            <button @click="removeFile(index)" class="text-red-500 hover:text-red-700">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Action Buttons -->
        <div v-if="!simple" class="mt-4 grid grid-cols-2 gap-4">
          <button @click="handleUploadClick" :disabled="loading" class="btn-upload">
            <span class="text-white dark:text-black">
              <!-- Conditional uploadIconBtn Rendering -->
              <i v-if="uploadIconBtn && !loading" :class="uploadIconBtn"></i>
              <!-- Loading Spinner -->
              <i v-if="loading" class="pi pi-spinner pi-spin"></i>
              {{ loading ? 'Uploading' : uploadLabelBtn }}
            </span>
          </button>
          <button @click="clearAll" :disabled="loading" class="btn-clear-upload">
            Clear All
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-else>
      <!-- File Input -->
      <label class="block">
        <span class="sr-only">Choose file</span>
        <input 
          type="file"
          :multiple="multiple"
          @change="handleFileChange"
          ref="fileInput"
          class="block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4
            file:rounded-md file:border-0
            file:text-sm file:font-semibold
            file:bg-blue-50 file:text-blue-700
            hover:file:bg-blue-100"
        />
      </label>

      <!-- Error Message -->
      <div v-if="error" class="mt-2 text-red-500 text-sm">
        {{ error }}
      </div>

      <!-- Selected Files -->
      <div v-if="uploadedFiles.length > 0" class="mt-4">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Selected files:</h3>
        <ul class="space-y-2">
          <li 
            v-for="(file, index) in uploadedFiles" 
            :key="index"
            class="flex items-center justify-between p-2 bg-gray-50 rounded"
          >
            <span class="text-sm truncate">{{ file.name }}</span>
            <button 
              @click="removeFile(index)"
              class="text-red-500 hover:text-red-700"
            >
              ×
            </button>
          </li>
        </ul>
      </div>
    </div>
</template>
<style>

</style>