<script setup>
import { onMounted, ref, computed, useAttrs } from 'vue';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';

defineOptions({ inheritAttrs: false });

const model = defineModel({
    type: String,
    required: true,
});

const attrs = useAttrs();
const input = ref(null);
const showPassword = ref(false);

const isPassword = computed(() => attrs.type === 'password');
const inputType = computed(() => (showPassword.value ? 'text' : 'password'));
// Margin classes (e.g. "mt-1") come through attrs.class from each page. Left
// on the <input> itself they inflate the relative wrapper's box (the margin
// renders as a gap above the input but inside the wrapper), which throws off
// the eye button's inset-0 vertical centering. Only the margin utilities are
// moved to the wrapper (same visual spacing, no internal gap) — everything
// else (colors, focus overrides) stays on the input so per-page styling
// (e.g. a page's own focus border/ring color) still applies.
const isMarginClass = (c) => /^-?m[trblxy]?-/.test(c);
const attrClasses = computed(() => (attrs.class || '').split(/\s+/).filter(Boolean));
const wrapperMarginClass = computed(() => attrClasses.value.filter(isMarginClass).join(' '));
const inputAttrsNoClass = computed(() => {
    const { class: _omit, ...rest } = attrs;
    return rest;
});
const inputClassFromAttrs = computed(() => attrClasses.value.filter((c) => !isMarginClass(c)).join(' '));

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div v-if="isPassword" class="relative w-full" :class="wrapperMarginClass">
        <input
            v-bind="inputAttrsNoClass"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pr-10"
            :class="inputClassFromAttrs"
            v-model="model"
            ref="input"
            :type="inputType"
        />
        <button
            type="button"
            tabindex="-1"
            class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-400 hover:text-gray-600"
            @click="showPassword = !showPassword"
        >
            <EyeSlashIcon v-if="showPassword" class="h-5 w-5" />
            <EyeIcon v-else class="h-5 w-5" />
        </button>
    </div>
    <input
        v-else
        v-bind="attrs"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        v-model="model"
        ref="input"
    />
</template>
