<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  showPreviewToggle: {
    type: Boolean,
    default: true
  },
  previewMode: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits([
  'bold',
  'italic',
  'heading',
  'link',
  'code',
  'code-block',
  'list',
  'quote',
  'update:previewMode'
])

const isPreview = computed({
  get: () => props.previewMode,
  set: (val) => emit('update:previewMode', val)
})

const showLanguageDropdown = ref(false)
const dropdownRef = ref(null)

const languages = [
  { value: '', label: 'Plain Text' },
  { value: 'javascript', label: 'JavaScript' },
  { value: 'typescript', label: 'TypeScript' },
  { value: 'python', label: 'Python' },
  { value: 'java', label: 'Java' },
  { value: 'cpp', label: 'C++' },
  { value: 'csharp', label: 'C#' },
  { value: 'php', label: 'PHP' },
  { value: 'ruby', label: 'Ruby' },
  { value: 'go', label: 'Go' },
  { value: 'rust', label: 'Rust' },
  { value: 'swift', label: 'Swift' },
  { value: 'kotlin', label: 'Kotlin' },
  { value: 'html', label: 'HTML' },
  { value: 'css', label: 'CSS' },
  { value: 'scss', label: 'SCSS' },
  { value: 'json', label: 'JSON' },
  { value: 'xml', label: 'XML' },
  { value: 'yaml', label: 'YAML' },
  { value: 'sql', label: 'SQL' },
  { value: 'bash', label: 'Bash' },
  { value: 'shell', label: 'Shell' },
  { value: 'markdown', label: 'Markdown' },
  { value: 'vue', label: 'Vue' },
  { value: 'react', label: 'React/JSX' }
]

const togglePreview = () => {
  isPreview.value = !isPreview.value
}

const toggleLanguageDropdown = () => {
  showLanguageDropdown.value = !showLanguageDropdown.value
}

const selectLanguage = (language) => {
  emit('code-block', language)
  showLanguageDropdown.value = false
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    showLanguageDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="flex items-center gap-1 p-2 bg-gray-50 dark:bg-[#18181b] border-b border-gray-300">
    <button @click="emit('bold')" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" title="Bold" type="button">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6zM6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z" />
      </svg>
    </button>

    <button @click="emit('italic')" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" title="Italic"
      type="button">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-4 16h4M14 4l-4 16" />
      </svg>
    </button>

    <button @click="emit('heading')" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors font-bold" title="Heading"
      type="button">
      H
    </button>

    <div class="w-px h-6 bg-gray-300 mx-1"></div>

    <button @click="emit('link')" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" title="Link" type="button">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
      </svg>
    </button>

    <button @click="emit('code')" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" title="Inline Code"
      type="button">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
      </svg>
    </button>

    <div class="relative" ref="dropdownRef">
      <button @click="toggleLanguageDropdown"
        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors flex items-center gap-1" title="Code Block"
        type="button">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <div v-if="showLanguageDropdown"
        class="absolute top-full left-0 mt-1 bg-white dark:bg-black border border-gray-300 rounded-lg shadow-lg z-50 w-48 max-h-64 overflow-y-auto">
        <button v-for="lang in languages" :key="lang.value" @click="selectLanguage(lang.value)"
          class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm" type="button">
          {{ lang.label }}
        </button>
      </div>
    </div>

    <div class="w-px h-6 bg-gray-300 mx-1"></div>

    <button @click="emit('list')" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" title="List" type="button">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <button @click="emit('quote')" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" title="Quote" type="button">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
      </svg>
    </button>

    <div v-if="showPreviewToggle" class="ml-auto flex items-center gap-2">
      <button @click="togglePreview" :class="[
        'px-3 py-1 rounded text-sm font-medium transition-colors',
        isPreview
          ? 'bg-blue-500 text-white'
          : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
      ]" type="button">
        {{ isPreview ? 'Edit' : 'Preview' }}
      </button>
    </div>
  </div>
</template>