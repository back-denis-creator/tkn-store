<script setup>
import { computed, ref } from 'vue';
import Checkbox from 'primevue/checkbox';

const props = defineProps({
    attribute: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['change']);

// An attribute like "Розмір" carries dozens of values, and printing them all
// buried the filters below it under a wall of checkboxes. The first twenty
// answer almost every search; the rest are one click away.
const COLLAPSED_COUNT = 20;

const expanded = ref(false);

const visibleOptions = computed(() => (
    expanded.value
        ? props.attribute.attribute_options
        : props.attribute.attribute_options.slice(0, COLLAPSED_COUNT)
));

const hiddenCount = computed(() => props.attribute.attribute_options.length - COLLAPSED_COUNT);
</script>

<template>
    <div class="flex flex-col gap-2">
        <div v-for="option in visibleOptions" :key="option.id" class="flex items-center gap-2">
            <Checkbox
                v-model="attribute.checked"
                @update:modelValue="emit('change', $event)"
                :inputId="String(option.id)"
                name="category"
                :value="option.value"
            />
            <label :for="option.id" class="text-sm text-gray-700">{{ option.value }}</label>
        </div>

        <button
            v-if="hiddenCount > 0"
            type="button"
            class="mt-1 self-start text-sm font-semibold text-amber-600 hover:text-amber-700"
            @click="expanded = !expanded"
        >
            {{ expanded ? $t('Filters_ShowLess') : `${$t('Filters_ShowMore')} (${hiddenCount})` }}
        </button>
    </div>
</template>
