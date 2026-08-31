<script setup>
const emit = defineEmits(['update:checked']);

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    depth: {
        type: Number,
        default: 0,
    },
});

const handleChange = () => {
    emit('update:checked');
}
</script>

<template>
    <div class="flex flex-col gap-2" :class="{ 'ml-3 mt-2 border-l border-gray-200 pl-4': depth > 0 }">
        <div v-for="item in items" :key="item.id">
            <label
                class="flex w-fit cursor-pointer items-center gap-2 rounded-md border px-3 py-1.5 text-sm transition-colors"
                :class="item.value ? 'border-amber-400 bg-amber-50 text-amber-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
            >
                <Checkbox v-model="item.value" :binary="true" @change="handleChange" />
                <span :class="{ 'font-medium': depth === 0 }">{{ item.name }}</span>
            </label>
            <CheckboxArray
                v-if="item.children.length"
                :items="item.children"
                :depth="depth + 1"
                @update:checked="handleChange"
            />
        </div>
    </div>
</template>
