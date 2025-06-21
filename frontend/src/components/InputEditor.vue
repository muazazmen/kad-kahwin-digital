<template>
  <div class="vscode-editor" @click="focusEditor">
    <div class="editor-container">
      <div class="line-numbers">
        <div v-for="(line, index) in lines" :key="index" class="line-number">
          {{ index + 1 }}
        </div>
      </div>
      <div class="editor-content" ref="editorContent">
        <pre class="editor-backdrop"><code>{{ highlightedContent }}</code></pre>
        <textarea
          ref="textarea"
          class="editor-input"
          v-model="content"
          @input="handleInput"
          @keydown="handleKeyDown"
          @scroll="syncScroll"
          spellcheck="false"
        ></textarea>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  language: {
    type: String,
    default: 'javascript'
  }
})

const emit = defineEmits(['update:modelValue'])

const content = ref(props.modelValue)
const textarea = ref(null)
const editorContent = ref(null)

// Update content when modelValue changes
watch(() => props.modelValue, (newVal) => {
  if (newVal !== content.value) {
    content.value = newVal
  }
})

// Update parent when content changes
watch(content, (newVal) => {
  emit('update:modelValue', newVal)
})

const lines = computed(() => {
  return content.value.split('\n')
})

// Basic syntax highlighting (simplified)
const highlightedContent = computed(() => {
  if (props.language === 'javascript') {
    return highlightJavaScript(content.value)
  }
  return escapeHtml(content.value)
})

function escapeHtml(text) {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
}

function highlightJavaScript(code) {
  // Very basic JavaScript highlighting
  const keywords = ['function', 'const', 'let', 'var', 'return', 'if', 'else', 'for', 'while', 'switch', 'case', 'break', 'continue', 'try', 'catch', 'class', 'extends', 'import', 'export', 'default', 'async', 'await', 'new', 'this']
  const special = ['true', 'false', 'null', 'undefined']
  
  return escapeHtml(code)
    .replace(/(\/\/.*)/g, '<span class="comment">$1</span>')
    .replace(/(\/\*[\s\S]*?\*\/)/g, '<span class="comment">$1</span>')
    .replace(new RegExp(`\\b(${keywords.join('|')})\\b`, 'g'), '<span class="keyword">$1</span>')
    .replace(new RegExp(`\\b(${special.join('|')})\\b`, 'g'), '<span class="special">$1</span>')
    .replace(/(\b\d+\b)/g, '<span class="number">$1</span>')
    .replace(/(["'].*?["'])/g, '<span class="string">$1</span>')
}

function handleInput() {
  resizeTextarea()
}

function handleKeyDown(e) {
  // Handle tab key for indentation
  if (e.key === 'Tab') {
    e.preventDefault()
    const start = textarea.value.selectionStart
    const end = textarea.value.selectionEnd
    
    if (e.shiftKey) {
      // Unindent
      const before = content.value.substring(0, start)
      const after = content.value.substring(end)
      const prevChars = before.substring(before.lastIndexOf('\n') + 1)
      
      if (prevChars.startsWith('  ')) {
        content.value = before.slice(0, -2) + after
        textarea.value.selectionStart = start - 2
        textarea.value.selectionEnd = end - 2
      }
    } else {
      // Indent
      content.value = content.value.substring(0, start) + '  ' + content.value.substring(end)
      textarea.value.selectionStart = start + 2
      textarea.value.selectionEnd = end + 2
    }
  }
}

function syncScroll() {
  if (editorContent.value && textarea.value) {
    editorContent.value.scrollTop = textarea.value.scrollTop
    editorContent.value.scrollLeft = textarea.value.scrollLeft
  }
}

function focusEditor() {
  textarea.value.focus()
}

onMounted(() => {
  resizeTextarea()
})

function resizeTextarea() {
  nextTick(() => {
    if (textarea.value) {
      textarea.value.style.height = 'auto'
      textarea.value.style.height = `${textarea.value.scrollHeight}px`
    }
  })
}
</script>

<style scoped>
.vscode-editor {
  font-family: 'Consolas', 'Courier New', monospace;
  font-size: 14px;
  line-height: 1.5;
  color: #d4d4d4;
  background-color: #1e1e1e;
  border-radius: 4px;
  overflow: hidden;
  position: relative;
  height: 100%;
}

.editor-container {
  display: flex;
  height: 100%;
  position: relative;
}

.line-numbers {
  background-color: #1e1e1e;
  color: #858585;
  text-align: right;
  padding: 8px 12px;
  user-select: none;
  border-right: 1px solid #333;
  min-width: 40px;
}

.line-number {
  min-height: 18px;
}

.editor-content {
  position: relative;
  flex-grow: 1;
  overflow: hidden;
}

.editor-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: 0;
  padding: 8px;
  pointer-events: none;
  overflow: hidden;
  white-space: pre;
  z-index: 1;
}

.editor-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  padding: 8px;
  margin: 0;
  background: transparent;
  color: transparent;
  caret-color: #d4d4d4;
  border: none;
  resize: none;
  font-family: inherit;
  font-size: inherit;
  line-height: inherit;
  outline: none;
  overflow: auto;
  white-space: pre;
  tab-size: 2;
  z-index: 2;
}

/* Syntax highlighting classes */
.editor-backdrop .keyword {
  color: #569cd6;
}

.editor-backdrop .string {
  color: #ce9178;
}

.editor-backdrop .number {
  color: #b5cea8;
}

.editor-backdrop .comment {
  color: #6a9955;
}

.editor-backdrop .special {
  color: #4fc1ff;
}
</style>