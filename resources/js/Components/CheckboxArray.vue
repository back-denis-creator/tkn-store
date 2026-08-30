<script setup>
import { ref } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    items: {
        type: [Array],
        required: true,
    }
});

const checkboxes = ref([...props.items])

const handleChange = () => {
    emit('update:checked', checkboxes.value);
}
</script>

<template>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
        <label
            v-for="item in checkboxes"
            :key="item.id"
            class="flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm transition-colors"
            :class="item.value ? 'border-amber-400 bg-amber-50 text-amber-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
        >
            <Checkbox v-model="item.value" :binary="true" @change="handleChange" />
            <span>{{ item.name }}</span>
        </label>
    </div>
</template>
