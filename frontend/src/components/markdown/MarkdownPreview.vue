<script setup>
import { computed } from 'vue'

const props = defineProps({
  content: {
    type: String,
    default: ''
  },
  minHeight: {
    type: String,
    default: '300px'
  }
})

const parseMarkdown = (text) => {
  if (!text) return '<p class="text-gray-400 italic">Nothing to preview</p>'

  let html = text

  // Code blocks with language
  html = html.replace(/```(\w+)?\n([\s\S]*?)```/g, (match, lang, code) => {
    const language = lang ? `<span class="text-xs text-gray-500 dark:text-gray-300 dark:bg-gray-800 mb-2 block">${lang}</span>` : ''
    return `<pre class="bg-gray-100 dark:bg-gray-800 p-4 rounded my-4 overflow-auto whitespace-pre-wrap break-words">${language}<code class="block">${code.trim()}</code></pre>`
  })

  // Headings
  html = html.replace(/^### (.*$)/gim, '<h3 class="text-xl font-bold mt-6 mb-3">$1</h3>')
  html = html.replace(/^## (.*$)/gim, '<h2 class="text-2xl font-bold mt-6 mb-3">$1</h2>')
  html = html.replace(/^# (.*$)/gim, '<h1 class="text-3xl font-bold mt-6 mb-4">$1</h1>')

  // Bold
  html = html.replace(/\*\*(.+?)\*\*/g, '<strong class="font-bold">$1</strong>')

  // Italic
  html = html.replace(/\*(.+?)\*/g, '<em class="italic">$1</em>')

  // Links
  html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-blue-500 hover:underline">$1</a>')

  // Inline code
  html = html.replace(/`([^`]+)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm font-mono">$1</code>')

  // Lists
  html = html.replace(/^\- (.+)$/gim, '<li class="ml-6">$1</li>')
  html = html.replace(/(<li.*<\/li>)/s, '<ul class="list-disc my-4">$1</ul>')

  // Blockquotes
  html = html.replace(/^&gt; (.+)$/gim, '<blockquote class="border-l-4 border-gray-300 pl-4 italic my-4">$1</blockquote>')
  html = html.replace(/^> (.+)$/gim, '<blockquote class="border-l-4 border-gray-300 pl-4 italic my-4">$1</blockquote>')

  // Paragraphs
  html = html.split('\n\n').map(para => {
    if (para.match(/^<(h[1-6]|ul|pre|blockquote)/)) return para
    return `<p class="my-3">${para}</p>`
  }).join('')

  // Line breaks
  html = html.replace(/\n/g, '<br>')

  return html
}

const renderedHtml = computed(() => parseMarkdown(props.content))
</script>

<template>
  <div :style="{ minHeight }" class="px-4 py-3 text-gray-800 dark:text-white dark:bg-black max-w-none overflow-y-auto" v-html="renderedHtml" />
</template>

<style scoped>
</style>