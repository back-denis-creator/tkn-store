<template>
    <div>
        <h3 class="font-bold mb-4">Головний банер</h3>

        <p class="mb-6 text-sm text-gray-500">
            Фото головного банера на сайті. Якщо їх декілька, вони автоматично
            перелистуються. Перетягніть картку, щоб змінити порядок показу.
            Кожне фото автоматично стискається у webp при завантаженні.
        </p>

        <FileUpload
            mode="basic"
            :chooseLabel="uploading ? 'Завантаження…' : 'Додати фото'"
            :disabled="uploading"
            accept="image/*"
            :maxFileSize="10000000"
            customUpload
            auto
            severity="secondary"
            class="p-button-outlined w-fit"
            @select="onFileSelect"
        />

        <p v-if="uploadForm.errors.image" class="mt-2 text-sm text-red-600">
            {{ uploadForm.errors.image }}
        </p>

        <p v-if="!localSlides.length" class="mt-6 text-sm text-gray-400">
            Ще немає жодного фото. Зараз показується фото за замовчуванням.
        </p>

        <div v-else class="mt-6 space-y-4">
            <div
                v-for="(slide, index) in localSlides"
                :key="slide.id"
                draggable="true"
                @dragstart="onDragStart(index)"
                @dragover.prevent
                @drop="onDrop(index)"
                class="flex flex-col gap-4 rounded-lg border border-gray-200 p-4 sm:flex-row"
            >
                <div class="relative w-full shrink-0 sm:w-56">
                    <img :src="slide.preview_url || slide.url" alt="" class="aspect-video w-full cursor-move rounded object-cover" />
                    <span class="absolute left-2 top-2 rounded bg-black/60 px-1.5 py-0.5 text-xs text-white">{{ index + 1 }}</span>
                    <button
                        type="button"
                        @click="destroy(slide.id)"
                        class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/60 text-white transition-colors hover:bg-red-600"
                        title="Видалити"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="min-w-0 flex-1">
                    <InputLabel :for="`slide_title_${slide.id}`" value="Заголовок" />
                    <TextInput
                        :id="`slide_title_${slide.id}`"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Залиште порожнім, щоб показати текст за замовчуванням"
                        v-model="slide.title"
                    />

                    <InputLabel :for="`slide_description_${slide.id}`" value="Опис" class="mt-4" />
                    <textarea
                        :id="`slide_description_${slide.id}`"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                        placeholder="Кожен рядок показується окремим рядком на банері"
                        v-model="slide.description"
                    ></textarea>

                    <div class="mt-4 flex items-center gap-2">
                        <input
                            :id="`slide_show_button_${slide.id}`"
                            type="checkbox"
                            v-model="slide.show_button"
                            class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                        />
                        <label :for="`slide_show_button_${slide.id}`" class="text-sm font-medium text-gray-900">
                            Показувати жовту кнопку
                        </label>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <PrimaryButton
                            type="button"
                            :disabled="!isDirty(slide) || savingId === slide.id"
                            :class="{ 'opacity-25': !isDirty(slide) || savingId === slide.id }"
                            @click="save(slide)"
                        >
                            Зберегти слайд
                        </PrimaryButton>
                        <span v-if="savedId === slide.id" class="text-sm text-gray-500">Збережено</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import FileUpload from 'primevue/fileupload';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    slides: {
        type: Array,
        default: () => ([]),
    },
});

// Editable copies: the text inputs write straight into these, and the saved
// snapshot below is what tells an edited slide from an untouched one.
const localSlides = ref([]);
const savedSnapshot = ref({});

const snapshotOf = (slide) => JSON.stringify([slide.title, slide.description, slide.show_button]);

const isDirty = (slide) => savedSnapshot.value[slide.id] !== snapshotOf(slide);

// A reorder or an upload reloads the whole slide list, so text typed into
// another card and not saved yet has to survive that round trip — only the
// server's own values for untouched cards are taken over.
const syncFromServer = (slides) => {
    const unsaved = Object.fromEntries(
        localSlides.value.filter(isDirty).map((slide) => [slide.id, slide])
    );

    localSlides.value = slides.map((slide) => ({
        ...slide,
        title: unsaved[slide.id] ? unsaved[slide.id].title : (slide.title ?? ''),
        description: unsaved[slide.id] ? unsaved[slide.id].description : (slide.description ?? ''),
        show_button: unsaved[slide.id] ? unsaved[slide.id].show_button : slide.show_button,
    }));

    savedSnapshot.value = Object.fromEntries(slides.map((slide) => [
        slide.id,
        snapshotOf({ ...slide, title: slide.title ?? '', description: slide.description ?? '' }),
    ]));
};

syncFromServer(props.slides);
watch(() => props.slides, syncFromServer);

const savingId = ref(null);
const savedId = ref(null);

const save = (slide) => {
    savingId.value = slide.id;
    router.patch(route('hero-slides.update', slide.id), {
        title: slide.title,
        description: slide.description,
        show_button: slide.show_button,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            savedId.value = slide.id;
            setTimeout(() => { if (savedId.value === slide.id) savedId.value = null; }, 2000);
        },
        onFinish: () => { savingId.value = null; },
    });
};

const uploading = ref(false);
const uploadForm = useForm({ image: null });

const onFileSelect = (event) => {
    const file = event.files[0];
    if (!file) return;
    uploadForm.image = file;
    uploading.value = true;
    uploadForm.post(route('hero-slides.store'), {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            uploading.value = false;
            uploadForm.reset();
        },
    });
};

const destroy = (id) => {
    if (!confirm('Видалити це фото?')) return;
    router.delete(route('hero-slides.destroy', id), { preserveScroll: true });
};

// Native HTML5 drag-and-drop — no extra library needed for reordering a
// handful of slides. Reordered locally first for instant feedback, then
// persisted; the watcher above resyncs from the server's own order once
// the request completes.
const draggedIndex = ref(null);
const onDragStart = (index) => { draggedIndex.value = index; };
const onDrop = (index) => {
    if (draggedIndex.value === null || draggedIndex.value === index) return;
    const items = [...localSlides.value];
    const [moved] = items.splice(draggedIndex.value, 1);
    items.splice(index, 0, moved);
    localSlides.value = items;
    draggedIndex.value = null;

    router.patch(route('hero-slides.reorder'), { ids: items.map((s) => s.id) }, { preserveScroll: true });
};
</script>
