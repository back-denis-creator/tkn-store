<template>
    <Head title="Головний банер" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Головний банер
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
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

                        <div v-else class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            <div
                                v-for="(slide, index) in localSlides"
                                :key="slide.id"
                                draggable="true"
                                @dragstart="onDragStart(index)"
                                @dragover.prevent
                                @drop="onDrop(index)"
                                class="group relative cursor-move overflow-hidden rounded-lg border border-gray-200"
                            >
                                <img :src="slide.preview_url || slide.url" alt="" class="aspect-video w-full object-cover" />
                                <button
                                    type="button"
                                    @click="destroy(slide.id)"
                                    class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/60 text-white opacity-0 transition-opacity hover:bg-red-600 group-hover:opacity-100"
                                    title="Видалити"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <span class="absolute left-2 top-2 rounded bg-black/60 px-1.5 py-0.5 text-xs text-white">{{ index + 1 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FileUpload from 'primevue/fileupload';
import { Head, router, useForm } from "@inertiajs/vue3";
import { ref, watch } from 'vue';

const props = defineProps({
    slides: {
        type: Array,
        default: () => ([]),
    },
});

const localSlides = ref([...props.slides]);
watch(() => props.slides, (value) => { localSlides.value = [...value]; });

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
