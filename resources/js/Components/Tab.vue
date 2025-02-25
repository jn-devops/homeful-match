<template>
    <div class="flex flex-col items-center justify-center">
        <div class="bg-gray-200 rounded-xl px-2 py-2">
            
            <div class="sm:block">
              <nav class="flex space-x-0" aria-label="Tabs">
                <button v-for="tab in tabs" :key="tab.name" type="button" @click="switchTabFunc(tab.name)" :class="[tab.current ? 'bg-gray-100 text-gray-700' : 'text-gray-500 hover:text-gray-700', 'rounded-md px-2 py-1 text-sm font-medium']" :aria-current="tab.current ? 'page' : undefined">{{ tab.name }}</button>
              </nav>
            </div>
        </div>
    </div>
</template>
  
<script setup>
import { ChevronDownIcon } from '@heroicons/vue/16/solid'

const props = defineProps({
    tabs: Object,
})

const emit = defineEmits(['switchTab'])

const switchTabFunc = (tabName) => {
    emit('switchTab', tabName)
}
</script>