<template>
    <div class="max-w-sm mx-auto p-4">
      <!-- File Input -->
      <label class="block">
        <span class="sr-only">Choose file</span>
        <input 
          type="file"
          @change="handleFileSelect"
          class="block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4
            file:rounded-md file:border-0
            file:text-sm file:font-semibold
            file:bg-blue-50 file:text-blue-700
            hover:file:bg-blue-100"
        />
      </label>
  
      <!-- Selected Files -->
      <div v-if="files.length > 0" class="mt-4">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Selected files:</h3>
        <ul class="space-y-2">
          <li 
            v-for="(file, index) in files" 
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
  
        <!-- Upload Button -->
        <button
          @click="uploadFiles"
          :disabled="uploading"
          class="mt-4 w-full py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-blue-300"
        >
          {{ uploading ? 'Uploading...' : 'Upload' }}
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const files = ref([]);
  const uploading = ref(false);
  
  const handleFileSelect = (e) => {
    files.value = Array.from(e.target.files);
  };
  
  const removeFile = (index) => {
    files.value.splice(index, 1);
  };
  
  const uploadFiles = async () => {
    if (files.value.length === 0) return;
    
    uploading.value = true;
    
    try {
      // Replace with actual upload logic
      console.log('Uploading files:', files.value);
      await new Promise(resolve => setTimeout(resolve, 1000));
      alert('Files uploaded successfully!');
      files.value = [];
    } catch (error) {
      console.error('Upload failed:', error);
      alert('Upload failed');
    } finally {
      uploading.value = false;
    }
  };
  </script>