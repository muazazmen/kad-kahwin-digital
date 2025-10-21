<script setup>
import { ref, computed } from 'vue'
import MarkdownToolbar from './MarkdownToolbar.vue'
import MarkdownPreview from './MarkdownPreview.vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Write your markdown here...'
  },
  showPreview: {
    type: Boolean,
    default: true
  },
  minHeight: {
    type: String,
    default: '300px'
  }
})

const emit = defineEmits(['update:modelValue'])

const textarea = ref(null)
const previewMode = ref(false)

const content = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const insertText = (before, after = '') => {
  const el = textarea.value
  const start = el.selectionStart
  const end = el.selectionEnd
  const selectedText = content.value.substring(start, end)
  const newText = content.value.substring(0, start) + before + selectedText + after + content.value.substring(end)

  content.value = newText

  setTimeout(() => {
    el.focus()
    el.setSelectionRange(start + before.length, end + before.length)
  }, 0)
}

const insertBold = () => insertText('**', '**')
const insertItalic = () => insertText('*', '*')
const insertHeading = () => insertText('## ')
const insertLink = () => insertText('[', '](url)')
const insertCode = () => insertText('`', '`')
const insertCodeBlock = (language = '') => {
  const el = textarea.value
  const start = el.selectionStart
  const end = el.selectionEnd
  const selectedText = content.value.substring(start, end)

  // If there's selected text, wrap it in code block
  if (selectedText) {
    const lang = language ? language : ''
    insertText(`\`\`\`${lang}\n`, '\n\`\`\`')
  } else {
    // If no selection, check if the entire content should be wrapped
    const currentContent = content.value.trim()
    if (currentContent && !currentContent.startsWith('```')) {
      // Wrap entire content in code block
      const lang = language ? language : ''
      content.value = `\`\`\`${lang}\n${currentContent}\n\`\`\``
      setTimeout(() => {
        el.focus()
        el.setSelectionRange(content.value.length, content.value.length)
      }, 0)
    } else {
      // Empty or already has code block, insert normally
      const lang = language ? language : ''
      insertText(`\`\`\`${lang}\n`, '\n\`\`\`')
    }
  }
}
const insertList = () => insertText('- ')
const insertQuote = () => insertText('> ')

defineExpose({
  insertBold,
  insertItalic,
  insertHeading,
  insertLink,
  insertCode,
  insertCodeBlock,
  insertList,
  insertQuote
})
</script>

<template>
  <div class="w-full border border-gray-600 hover:border-gray-500 rounded-lg overflow-hidden bg-white">
    <MarkdownToolbar @bold="insertBold" @italic="insertItalic" @heading="insertHeading" @link="insertLink"
      @code="insertCode" @code-block="insertCodeBlock" @list="insertList" @quote="insertQuote"
      :show-preview-toggle="showPreview" v-model:preview-mode="previewMode" />

    <div class="relative">
      <textarea v-if="!previewMode" ref="textarea" v-model="content" :placeholder="placeholder" :style="{ minHeight }"
        class="w-full px-4 py-3 text-gray-800 dark:text-white dark:bg-black focus:outline-none resize-none font-mono text-sm" />

      <MarkdownPreview v-if="previewMode && showPreview" :content="content" :min-height="minHeight" />
    </div>
  </div>
</template>