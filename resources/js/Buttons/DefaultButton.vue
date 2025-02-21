<script setup>
const props = defineProps({
    type: {
        type: String,
        default: "submit",
    }
})
</script>
<template>
    <button :type="type" class="w-fit px-4 py-2 bg-black text-white font-bold text-sm rounded-xl shadow-xl">
        <slot />
    </button>
</template>