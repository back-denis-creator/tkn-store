<script setup>
import Editor from 'primevue/editor';
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
import FileUpload from 'primevue/fileupload';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import CheckboxArray from '@/Components/CheckboxArray.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    },
    attributes: {
        type: Object,
        default: () => ({}),
    }
});

const form = useForm({
    name: "",
    slug: "",
    description: "",
    category_ids: [],
    variations: []
});

const submit = () => {
    form.variations = variations.value
    form.post(route("products.store"))
}

const handleUpdateCategories = (categories) => {
    form.category_ids = categories.reduce((filtered, category) => {
        if (category.value) filtered.push(category.id)
        return filtered
    }, [])
}

const categoryItems = ref([...props.categories.map((category) => {
    return {
        id: category.id,
        name: category.name,
        value: false
    }
})])

const deleteVariation = (index) => {
    if(index > 0) {
        if(variations.value[index].show) variations.value[0].show = true
        variations.value.splice(index, 1)
    }
}

const addVariation = (id) => {
    variations.value.push({
        id,
        code: "",
        price: "",
        show: true,
        images: [],
        attributes: props.attributes.map((attribute, index) => {
            return {
                name: attribute.name,
                value: "",
                unit: "",
                id: attribute.id,
                attribute_options: attribute.attribute_options.map((option) => {
                    return {
                        id: option.id,
                        value: option.value
                    }
                }),
                search: (e) => {
                    searchOptionVariations(index)
                }
            }
        })
    })
    selectVariation(variations.value.length - 1)
}

const selectVariation = (index) => {
    variations.value = variations.value.map((variation, i) => {
        variation.show = i === index
        return variation
    })
}

const selectedVariation = computed(() => variations.value.findIndex((({show}) => show)))

const searchOptionVariations = (index) => {
    variations.value[selectedVariation.value].attributes[index].attribute_options = variations.value[selectedVariation.value].attributes[index].attribute_options.map((option, index) => {
        return {
            id: option.id,
            value: option.value,
        }
    })
}

const variations = ref([{
    id: 1,
    code: "",
    price: "",
    show: true,
    images: [],
    attributes: props.attributes.map((attribute, index) => {
        return {
            name: attribute.name,
            value: "",
            unit: "",
            id: attribute.id,
            attribute_options: attribute.attribute_options.map((option, index) => {
                return {
                    id: option.id,
                    value: option.value,
                }
            }),
            search: (e) => {
                searchOptionVariations(index)
            }
        }
    })
}])

const onFilesVariation = (e) => {
    variations.value[selectedVariation.value].images = e.files
}

const removeVariationImage = (index) => {
    variations.value[selectedVariation.value].images = variations.value[selectedVariation.value].images.filter((_, i) => i !== index)
}
</script>

<template>
    <Head title="Створення товару" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Створення товару
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic info -->
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Основна інформація</h3>
                        </div>
                        <div class="space-y-6 p-6">
                            <div>
                                <InputLabel for="name" value="Імʼя" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>
                            <div>
                                <InputLabel for="slug" value="URL Імʼя" />
                                <TextInput
                                    id="slug"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.slug"
                                />
                                <InputError class="mt-2" :message="form.errors.slug" />
                            </div>
                            <div>
                                <InputLabel value="Опис" />
                                <Editor v-model="form.description" editorStyle="height: 320px" class="mt-1" />
                                <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Категорії</h3>
                        </div>
                        <div class="p-6">
                            <CheckboxArray :items="categoryItems" @update:checked="handleUpdateCategories" />
                        </div>
                    </div>

                    <!-- Variations -->
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Варіації товару</h3>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 px-6 pt-4">
                            <button
                                v-for="(variation, index) in variations"
                                :key="index"
                                type="button"
                                @click="selectVariation(index)"
                                class="group inline-flex items-center gap-2 rounded-t-md border border-b-0 px-4 py-2 text-sm transition-colors"
                                :class="variation.show ? 'border-amber-300 bg-amber-50 font-medium text-amber-700' : 'border-transparent text-gray-500 hover:bg-gray-50'"
                            >
                                {{ variation.code || `Варіація ${index + 1}` }}
                                <i
                                    v-if="index > 0"
                                    @click.stop="deleteVariation(index)"
                                    class="pi pi-times text-xs text-gray-400 group-hover:text-red-500"
                                ></i>
                            </button>
                            <button
                                type="button"
                                @click="addVariation(variations.length + 1)"
                                class="inline-flex items-center gap-1 rounded-t-md px-3 py-2 text-sm text-amber-600 hover:bg-amber-50"
                            >
                                <i class="pi pi-plus text-xs"></i>
                                Додати варіацію
                            </button>
                        </div>

                        <div v-for="(variation, vIndex) in variations" :key="variation.id" v-show="variation.show" class="grid gap-8 p-6 lg:grid-cols-2">
                            <div class="space-y-6">
                                <div>
                                    <InputLabel :for="`code-${vIndex}`" value="Артикул" />
                                    <TextInput
                                        :id="`code-${vIndex}`"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="variation.code"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors[`variations.${vIndex}.code`]"
                                    />
                                </div>
                                <div>
                                    <InputLabel :for="`price-${vIndex}`" value="Ціна" />
                                    <TextInput
                                        :id="`price-${vIndex}`"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="variation.price"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors[`variations.${vIndex}.price`]"
                                    />
                                </div>
                                <div v-for="attribute in variation.attributes" :key="`${attribute.id}${variation.id}`">
                                    <InputLabel :value="attribute.name" />
                                    <div class="mt-1 flex items-start gap-3">
                                        <AutoComplete class="w-full" :dataKey="`${attribute.id}${variation.id}`" :inputId="`${attribute.id}${variation.id}`" v-model="attribute.value" optionLabel="value" dropdown :suggestions="attribute.attribute_options" @complete="attribute.search" />
                                        <InputText v-model="attribute.unit" placeholder="Розхід" class="w-28 shrink-0" />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <InputLabel value="Зображення варіації" />
                                <FileUpload class="mt-1" @select="onFilesVariation($event)" multiple accept="image/*" :maxFileSize="1000000">
                                    <template #header="{ chooseCallback, clearCallback, files }">
                                        <div class="flex flex-wrap items-center justify-between gap-4 flex-1">
                                            <div class="flex gap-2">
                                                <Button @click="chooseCallback()" icon="pi pi-images" rounded outlined severity="secondary"></Button>
                                                <Button @click="clearCallback()" icon="pi pi-times" rounded outlined severity="danger" :disabled="!files || files.length === 0"></Button>
                                            </div>
                                        </div>
                                    </template>
                                    <template #content="{ files }">
                                        <div v-if="files.length" class="grid grid-cols-2 gap-3 pt-4 sm:grid-cols-3">
                                            <div v-for="(file, index) of files" :key="file.name + file.size" class="relative overflow-hidden rounded-md border border-gray-200">
                                                <img :alt="file.name" :src="file.objectURL" class="aspect-square w-full object-cover" />
                                                <button type="button" @click="removeVariationImage(index)" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white hover:bg-red-600">
                                                    <i class="pi pi-times text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    <template #empty>
                                        <span class="text-sm text-gray-400">Перетягніть зображення сюди.</span>
                                    </template>
                                </FileUpload>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton
                            type="submit"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Створити
                        </PrimaryButton>
                        <Link :href="route('products.index')" class="text-sm text-gray-500 hover:text-gray-700">
                            Скасувати
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
