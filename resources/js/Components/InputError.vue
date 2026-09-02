<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <p v-show="message" class="text-sm text-red-600 mt-1 mb-2">
        {{ message }}
    </p>
</template>
