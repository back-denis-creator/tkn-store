<script setup>
import Editor from 'primevue/editor';
// Same patch as the product form: PrimeVue's Editor otherwise re-renders the
// value on every keystroke and puts the caret back at the start.
Editor.methods.renderValue = function renderValue(value) {
    if (this.quill) {
        if (value) {
            const delta = this.quill.clipboard.convert({ html: value });
            this.quill.setContents(delta, 'silent');
        } else {
            this.quill.setText('');
        }
    }
};
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    currentCoverUrl: {
        type: String,
        default: null,
    },
    submitLabel: {
        type: String,
        default: 'Зберегти',
    },
});

const emit = defineEmits(['submit']);

// The chosen file is previewed from the browser, so the admin sees what the
// post will carry before saving rather than after.
const preview = ref(null);

const onCoverSelect = (event) => {
    const file = event.target.files?.[0] ?? null;
    props.form.cover = file;
    props.form.remove_cover = false;
    preview.value = file ? URL.createObjectURL(file) : null;
};

const removeCover = () => {
    props.form.cover = null;
    props.form.remove_cover = true;
    preview.value = null;
};
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Основне</h3>
            </div>
            <div class="space-y-6 p-6">
                <div>
                    <InputLabel for="title" value="Заголовок" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                    <InputError :message="form.errors.title" />
                </div>

                <div>
                    <InputLabel for="slug" value="URL ім'я" />
                    <TextInput id="slug" type="text" class="mt-1 block w-full" v-model="form.slug" placeholder="Залиште порожнім — створиться із заголовка" />
                    <InputError :message="form.errors.slug" />
                </div>

                <div>
                    <InputLabel for="excerpt" value="Короткий опис" />
                    <textarea
                        id="excerpt"
                        rows="3"
                        maxlength="500"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                        placeholder="Кілька речень, які видно на картці допису і в пошуку Google"
                        v-model="form.excerpt"
                    ></textarea>
                    <InputError :message="form.errors.excerpt" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Зображення</h3>
            </div>
            <div class="p-6">
                <div v-if="preview || (currentCoverUrl && !form.remove_cover)" class="mb-4">
                    <img :src="preview || currentCoverUrl" alt="" class="aspect-video w-full max-w-md rounded-lg object-cover" />
                    <button type="button" class="mt-2 text-sm text-red-600 hover:text-red-700" @click="removeCover">
                        Прибрати зображення
                    </button>
                </div>

                <input
                    id="cover"
                    type="file"
                    accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-amber-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-700 hover:file:bg-amber-100"
                    @change="onCoverSelect"
                />
                <p class="mt-2 text-sm text-gray-500">Горизонтальне фото виглядає найкраще. До 10 МБ.</p>
                <InputError :message="form.errors.cover" />
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Текст допису</h3>
            </div>
            <div class="p-6">
                <Editor v-model="form.content" editorStyle="height: 420px" />
                <InputError :message="form.errors.content" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ submitLabel }}
            </PrimaryButton>
            <Link :href="route('blogs.index')" class="text-sm text-gray-500 hover:text-gray-700">Скасувати</Link>
        </div>
    </form>
</template>
