<script setup>
import { ExclamationCircleIcon } from '@heroicons/vue/16/solid'
import { ref } from 'vue';
import { errorMessages } from 'vue/compiler-sfc';
const props = defineProps({
  label: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Enter text...',
  },
  errorMessage: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: 'text',
  },
  max: {
    type: Number,
    default: null,
  },
  readOnly: {
    type: Boolean,
    default: false,
  },
});
const model = ref(null)

const emit = defineEmits(['update:modelValue'])

const updateInput = (newVal) => {
    emit('update:modelValue', newVal.target.value)
}
</script>
<template>
    <div class="my-2">
        <label for="email" class="block text-sm/6 font-medium text-gray-900">{{ label }}</label>
        <div :class="{'grid grid-cols-1' : errorMessage}">
          <input 
            :type="type" 
            v-model="model"
            @input="updateInput"
            :maxlength="max"
            :readonly="readOnly"
            class=" w-full rounded-md bg-white py-1.5 pl-3 border-1 border-gray-400 focus:ring-0 focus:border-black text-sm" 
            :class="{
                  'text-red-600 border-red-600 focus:border-red-600 placeholder:text-red-300 col-start-1 row-start-1 block pr-10': errorMessage,
                  'text-gray-900 placeholder:text-gray-400': !errorMessage,
                }"
            :placeholder="placeholder" 
          />
          <ExclamationCircleIcon v-if="errorMessage" class="pointer-events-none col-start-1 row-start-1 mr-3 size-5 self-center justify-self-end text-red-500 sm:size-4" aria-hidden="true" />
        </div>
        <p v-if="errorMessage" class="text-sm text-red-600" id="email-error">{{ errorMessage }}</p>
    </div>
</template>
  
  