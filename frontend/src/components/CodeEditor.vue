<template>
  <div class="code-editor-container" :class="{ 'read-only': readOnly }">
    <div class="editor-header">
      <select 
        v-model="selectedLanguage" 
        @change="handleLanguageChange"
        class="language-select"
        :disabled="readOnly"
      >
        <option 
          v-for="lang in supportedLanguages" 
          :key="lang.value" 
          :value="lang.value"
        >
          {{ lang.label }}
        </option>
      </select>
      
      <div class="editor-actions">
        <button 
          v-if="copyButton"
          @click="copyToClipboard"
          class="action-btn"
          title="Copy code"
        >
          📋
        </button>
        <button 
          v-if="formatButton && canFormat"
          @click="formatCode"
          class="action-btn"
          title="Format code"
        >
          ✨
        </button>
      </div>
    </div>
    
    <div ref="editorContainer" class="editor-wrapper"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick, computed } from 'vue'
import * as monacoLoader from '@monaco-editor/loader'

// Props
interface Props {
  modelValue?: string
  language?: string
  height?: string
  readOnly?: boolean
  theme?: string
  copyButton?: boolean
  formatButton?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  language: 'plaintext',
  height: '400px',
  readOnly: false,
  theme: 'vs-dark',
  copyButton: true,
  formatButton: true
})

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: string]
  'update:language': [language: string]
  'change': [value: string, language: string]
}>()

// Reactive data
const editorContainer = ref<HTMLElement>()
const editor = ref<any>(null)
const selectedLanguage = ref(props.language)
const monaco = ref<any>(null)

// Supported languages
const supportedLanguages = [
  { value: 'plaintext', label: 'Plain Text' },
  { value: 'json', label: 'JSON' },
  { value: 'typescript', label: 'TypeScript' },
  { value: 'javascript', label: 'JavaScript' },
  { value: 'sql', label: 'SQL' }
]

// Computed
const canFormat = computed(() => 
  ['json', 'typescript', 'javascript'].includes(selectedLanguage.value)
)

// Initialize editor
onMounted(async () => {
  await initializeEditor()
})

// Cleanup
onUnmounted(() => {
  if (editor.value) {
    editor.value.dispose()
  }
})

// Watch for value changes from parent
watch(() => props.modelValue, (newValue) => {
  if (editor.value && newValue !== editor.value.getValue()) {
    editor.value.setValue(newValue)
  }
})

// Watch for language changes from parent
watch(() => props.language, (newLanguage) => {
  if (editor.value && newLanguage !== selectedLanguage.value) {
    selectedLanguage.value = newLanguage
    updateEditorLanguage(newLanguage)
  }
})

// Watch for theme changes
watch(() => props.theme, (newTheme) => {
  if (monaco.value && editor.value) {
    monaco.value.editor.setTheme(newTheme)
  }
})

// Methods
const initializeEditor = async () => {
  try {
    monaco.value = await monacoLoader.default.init()
    
    await nextTick()
    
    if (editorContainer.value) {
      editor.value = monaco.value.editor.create(editorContainer.value, {
        value: props.modelValue,
        language: selectedLanguage.value,
        theme: props.theme,
        readOnly: props.readOnly,
        automaticLayout: true,
        minimap: { enabled: false },
        scrollBeyondLastLine: false,
        fontSize: 14,
        lineNumbers: 'on',
        folding: true,
        lineDecorationsWidth: 10,
        lineNumbersMinChars: 3,
        renderLineHighlight: 'all',
        scrollbar: {
          vertical: 'visible',
          horizontal: 'visible',
          useShadows: false
        }
      })

      // Listen for content changes
      editor.value.onDidChangeModelContent(() => {
        const value = editor.value.getValue()
        emit('update:modelValue', value)
        emit('change', value, selectedLanguage.value)
      })

      // Adjust container height
      if (editorContainer.value.parentElement) {
        editorContainer.value.style.height = props.height
      }
    }
  } catch (error) {
    console.error('Failed to initialize Monaco Editor:', error)
  }
}

const handleLanguageChange = () => {
  updateEditorLanguage(selectedLanguage.value)
  emit('update:language', selectedLanguage.value)
  emit('change', editor.value?.getValue() || '', selectedLanguage.value)
}

const updateEditorLanguage = (language: string) => {
  if (editor.value) {
    const model = editor.value.getModel()
    if (model) {
      monaco.value.editor.setModelLanguage(model, language)
    }
  }
}

const copyToClipboard = async () => {
  const code = editor.value?.getValue() || ''
  try {
    await navigator.clipboard.writeText(code)
    // You could add a toast notification here
    console.log('Code copied to clipboard!')
  } catch (err) {
    console.error('Failed to copy code:', err)
  }
}

const formatCode = () => {
  if (!editor.value || !canFormat.value) return

  const action = editor.value.getAction('editor.action.formatDocument')
  if (action) {
    action.run().catch((err: any) => {
      console.warn('Formatting failed:', err)
    })
  }
}
</script>

<style scoped>
.code-editor-container {
  @apply border border-gray-300 rounded-lg overflow-hidden bg-white;
}

.code-editor-container.read-only {
  @apply bg-gray-50;
}

.editor-header {
  @apply flex justify-between items-center px-4 py-2 bg-gray-100 border-b border-gray-300;
}

.language-select {
  @apply px-3 py-1 border border-gray-300 rounded text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
}

.language-select:disabled {
  @apply bg-gray-100 cursor-not-allowed;
}

.editor-actions {
  @apply flex gap-2;
}

.action-btn {
  @apply p-2 rounded hover:bg-gray-200 transition-colors duration-200 text-sm;
}

.editor-wrapper {
  @apply w-full;
}

/* Dark theme support */
:deep(.monaco-editor) {
  --vscode-editor-background: white;
}

:deep(.monaco-editor.vs-dark) {
  --vscode-editor-background: #1e1e1e;
}
</style>