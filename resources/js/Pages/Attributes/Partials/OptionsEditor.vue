<script setup>
import FileUpload from 'primevue/fileupload';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    options: {
        type: Array,
        required: true,
    },
    deletedIds: {
        type: Array,
        default: null,
    },
    isColor: {
        type: Boolean,
        default: false,
    },
    colorGroups: {
        type: Array,
        default: () => ([]),
    },
    error: {
        type: String,
        default: null,
    },
});

const newOptionValue = ref('');

const addOption = () => {
    const value = newOptionValue.value.trim();
    if (!value) return;
    props.options.push({ id: 'new', value, new_file: null, new_preview: null, src: null, meta: {}, _pendingDelete: false });
    newOptionValue.value = '';
};

// A brand-new, not-yet-saved option can just be dropped outright. An existing one is only
// marked for deletion (not spliced out) — on success the whole page navigates away anyway,
// and if the server refuses (e.g. it's in use on a product) the row stays visible instead of
// disappearing from the form while still existing in the database (see the error watcher below).
const toggleDelete = (index) => {
    const option = props.options[index];
    if (option.id === 'new') {
        props.options.splice(index, 1);
        return;
    }
    option._pendingDelete = !option._pendingDelete;
    if (!props.deletedIds) return;
    const idx = props.deletedIds.indexOf(option.id);
    if (option._pendingDelete && idx === -1) {
        props.deletedIds.push(option.id);
    } else if (!option._pendingDelete && idx !== -1) {
        props.deletedIds.splice(idx, 1);
    }
};

// Kept as a real File (not base64) so it reaches the server as an ordinary multipart
// upload — Spatie MediaLibrary preserves the original name/extension from an UploadedFile,
// unlike addMediaFromBase64() which has no filename to work with. It also avoids the ~33%
// size inflation base64 adds, which made real photos more likely to hit the host's POST
// size limit and silently drop the whole request.
const onFileSelect = (index, event) => {
    const file = event.files[0];
    const option = props.options[index];
    if (option.new_preview) URL.revokeObjectURL(option.new_preview);
    option.new_file = file;
    option.new_preview = URL.createObjectURL(file);
};

// A validation error means at least one deletion was refused server-side — since we can't
// tell here which ones actually went through, play it safe and un-mark everything so nothing
// looks deleted when it might not be. Resubmitting is harmless for options already deleted.
watch(() => props.error, (value) => {
    if (!value) return;
    props.options.forEach((option) => { option._pendingDelete = false; });
    if (props.deletedIds) props.deletedIds.splice(0, props.deletedIds.length);
});
</script>

<template>
    <div>
        <InputLabel value="Варіанти значень" />

        <p v-if="error" class="mt-2 rounded-md bg-red-50 p-3 text-sm text-red-600">{{ error }}</p>

        <p v-if="!options.length" class="mt-3 text-sm text-gray-400">Ще немає жодного варіанту.</p>

        <div v-else class="mt-3 flex flex-col gap-3">
            <div
                v-for="(option, index) in options"
                :key="option.id === 'new' ? `new-${index}` : option.id"
                class="flex items-start gap-3 rounded-md border p-3 transition-opacity"
                :class="option._pendingDelete ? 'border-red-200 bg-red-50/40 opacity-60' : 'border-gray-200'"
            >
                <img
                    v-if="isColor && (option.new_preview || option.src)"
                    :src="option.new_preview || option.src"
                    alt=""
                    class="h-12 w-12 shrink-0 rounded-full border border-gray-200 object-cover"
                />

                <div class="flex flex-1 flex-col gap-2">
                    <TextInput v-model="option.value" class="w-full" :disabled="option._pendingDelete" />

                    <template v-if="isColor">
                        <Select v-model="option.meta" :options="colorGroups" optionLabel="name" placeholder="Обрати групу" class="w-full" :disabled="option._pendingDelete" />
                        <FileUpload v-if="!option._pendingDelete" mode="basic" chooseLabel="Завантажити фото" @select="onFileSelect(index, $event)" customUpload auto severity="secondary" class="p-button-outlined w-fit" />
                    </template>

                    <p v-if="option._pendingDelete" class="text-xs text-red-500">Буде видалено після збереження</p>
                </div>

                <button
                    type="button"
                    @click="toggleDelete(index)"
                    class="shrink-0"
                    :class="option._pendingDelete ? 'text-amber-600 hover:text-amber-700' : 'text-gray-400 hover:text-red-600'"
                    :title="option._pendingDelete ? 'Відновити варіант' : 'Видалити варіант'"
                >
                    <svg v-if="option._pendingDelete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M9.53 2.47a.75.75 0 010 1.06L4.81 8.25H15a6.75 6.75 0 010 13.5h-3a.75.75 0 010-1.5h3a5.25 5.25 0 100-10.5H4.81l4.72 4.72a.75.75 0 11-1.06 1.06l-6-6a.75.75 0 010-1.06l6-6a.75.75 0 011.06 0z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path
                            fill-rule="evenodd"
                            d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <div class="mt-4 flex items-end gap-3">
            <div class="flex-1">
                <InputLabel for="new_option" value="Новий варіант" />
                <TextInput
                    id="new_option"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="newOptionValue"
                    @keydown.enter.prevent="addOption"
                />
            </div>
            <PrimaryButton type="button" @click="addOption">Додати варіант</PrimaryButton>
        </div>
    </div>
</template>
