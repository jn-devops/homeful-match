<script setup>
import { ref, watch } from 'vue';
import { ExclamationCircleIcon } from '@heroicons/vue/16/solid';

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
    // Add the modelValue prop for v-model binding
    modelValue: {
        type: [String, Number],
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

// Use a local ref that is initialized with the prop's value.
const internalValue = ref(props.modelValue);

// Watch for changes to update internalValue when modelValue changes externally.
watch(() => props.modelValue, (newVal) => {
    internalValue.value = newVal;
});

// Emit update when the internal value changes.
function updateInput(event) {
    emit('update:modelValue', event.target.value);
}
</script>

<template>
    <div class="my-2">
        <label class="block text-sm/6 font-medium text-gray-900">{{ label }}</label>
        <div :class="{'grid grid-cols-1': errorMessage}">
            <input
                :type="type"
                v-bind="$attrs"
                v-model="internalValue"
                @input="updateInput"
                :maxlength="max"
                :readonly="readOnly"
                class="w-full rounded-md bg-white py-1.5 pl-3 border-1 border-gray-400 focus:ring-0 focus:border-black text-sm"
                :class="{
          'text-red-600 border-red-600 focus:border-red-600 placeholder:text-red-300': errorMessage,
          'text-gray-900 placeholder:text-gray-400': !errorMessage,
        }"
                :placeholder="placeholder"
            />
            <ExclamationCircleIcon
                v-if="errorMessage"
                class="pointer-events-none mr-3 self-center justify-self-end text-red-500"
                aria-hidden="true"
            />
        </div>
        <p v-if="errorMessage" class="text-sm text-red-600">{{ errorMessage }}</p>
    </div>
</template>
