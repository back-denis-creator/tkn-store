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
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import CheckboxArray from '@/Components/CheckboxArray.vue';
import { ref, computed, onMounted } from 'vue';
import { usePrimeVue } from 'primevue/config';

const $primevue = usePrimeVue();

const props = defineProps({
    product: {
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
})

const form = useForm({
    id: props.product.id,
    name: props.product.name,
    slug: props.product.slug,
    description: props.product.description,
    category_ids: [...props.categories.map((category) => {
        if(!!(props.product.categories.findIndex(({id}) => id === category.id) >= 0)) {
            return category.id
        }
    })].filter(Number),
    delete_variations_ids: [],
    variations: [...props.product.skus.map((variation, index) => {
        return {
            id: variation.id,
            code: variation.code,
            price: variation.price.toString(),
            show: index === 0,
            images: variation.media,
            new_images: [],
            attributes: props.attributes.map((attribute, key) => {
                let findedOption = variation.attribute_options.find(({attribute_id}) => attribute_id === attribute.id)
                return {
                    name: attribute.name,
                    value: findedOption ? findedOption.value : '',
                    unit: findedOption ? findedOption.pivot.unit : '',
                    id: attribute.id,
                    attribute_options: attribute.attribute_options.map(option => {
                        return {
                            id: option.id,
                            value: option.value,
                        }
                    }),
                    search: (e) => { 
                        searchOptionVariations(key)
                    }
                }
            })
        }
    })]
})

onMounted(() => {
    if(!form.variations.length) addVariation('new', true)
})

const selectedVariation = computed(() => form.variations.findIndex((({show}) => show)))

const searchOptionVariations = (index) => {
    form.variations[selectedVariation.value].attributes[index].attribute_options = form.variations[selectedVariation.value].attributes[index].attribute_options.map((option, index) => {
        return {
            id: option.id,
            value: option.value,
        }
    })
}

const submit = () => {
    form.post(route("products.update", props.product.id));
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
        value: !!(props.product.categories.findIndex(({id}) => id === category.id) >= 0)
    }
})])

const selectVariation = (index) => {
    form.variations = form.variations.map((variation, i) => {
        variation.show = i === index
        return variation
    })
}

const deleteVariation = (index) => {
    if(index > 0) {
        if(form.variations[index].show) form.variations[0].show = true
        if(form.variations[index].id !== 'new') {
            form.delete_variations_ids.push(form.variations[index].id)
        }
        form.variations.splice(index, 1)
    }
}

const addVariation = (id, show = false) => {
    form.variations.push({
        id,
        code: "",
        price: "",
        show: show,
        images: [],
        new_images: [],
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
}

const formatSize = (bytes) => {
    const k = 1024
    const dm = 3
    const sizes = $primevue.config.locale.fileSizeTypes
    if (bytes === 0) {
        return `0 ${sizes[0]}`
    }
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm))
    return `${formattedSize} ${sizes[i]}`
}

const onFilesVariation = (e) => {
    form.variations[selectedVariation.value].new_images = e.files
}

const deleteUploadedFileCallback = (index) => {
    form.variations[selectedVariation.value].images = [...form.variations[selectedVariation.value].images.filter((v, i) => i !== index)]
}
</script>

<template>
    <Head title="Редагування товару" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Редагування товару
            </h2>
        </template>
        <div class="py-12">
            <Card class="max-w-7xl mx-auto">
                <template #content>
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Імʼя" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div class="my-6">
                            <InputLabel for="slug" value="URL Імʼя" />
                            <TextInput
                                id="slug"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.slug"
                            />
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>
                        <div class="my-6">
                            <label for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Опис</label>
                            <Editor id="description" v-model="form.description" editorStyle="height: 320px" />

                            <div v-if="form.errors.description" class="text-sm text-red-600">
                                {{ form.errors.description }}
                            </div>
                        </div>
                        <div class="my-6">
                            <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Категорії</span>
                            <CheckboxArray :items="categoryItems" @update:checked="handleUpdateCategories" />
                        </div>
                        <div class="my-6">
                            <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Варіації</span>
                            <div class="flex overflow-x-auto whitespace-nowrap">
                                <div v-for="(variation, index) in form.variations" :key="index" @click.prevent="selectVariation(index)" :class="variation.show ? 'border border-b-0 border-gray-300 sm:text-base dark:border-gray-500 rounded-t-md dark:text-white whitespace-nowrap focus:outline-none' : 'bg-transparent border-b border-gray-300 sm:text-base dark:border-gray-500 dark:text-white whitespace-nowrap cursor-base focus:outline-none hover:border-gray-400 dark:hover:border-gray-300'" class="inline-flex items-center h-12 px-4 py-2 text-sm text-center text-gray-700">
                                    <span>{{ `№${index}` }}</span>
                                    <Button :disabled="index === 0" @click.stop="deleteVariation(index)" icon="pi pi-times" class="ml-2" severity="danger" text size="small" rounded aria-label="Cancel" />
                                </div>
                                <div @click.prevent="addVariation('new')" class="inline-flex items-center h-12 px-4 py-2 text-sm text-center text-gray-700 bg-transparent border-b border-gray-300 sm:text-base dark:border-gray-500 dark:text-white whitespace-nowrap cursor-base focus:outline-none hover:border-gray-400 dark:hover:border-gray-300">
                                    <i class="pi pi-plus" style="font-size: 0.9rem"></i>
                                </div>
                                <div class="border-b inline-flex w-full border-gray-300 dark:border-gray-500"></div>
                            </div>
                            <div v-for="(variation, index) in form.variations" :key="index" v-show="variation.show">
                                <div class="flex flex-row">
                                    <div class="basis-1/2 pr-2">
                                        <div class="my-6 ml-2">
                                            <InputLabel for="code" value="Артикул" />
                                            <TextInput
                                                id="code"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="variation.code"
                                            />
                                            <!-- <InputError
                                                class="mt-2"
                                                :message="form.errors.code"
                                            /> -->
                                        </div>
                                        <div class="my-6 ml-2">
                                            <InputLabel for="price" value="Ціна" />
                                            <TextInput
                                                id="price"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="variation.price"
                                            />
                                            <!-- <InputError
                                                class="mt-2"
                                                :message="form.errors.price"
                                            /> -->
                                        </div>
                                        <div class="ml-2">
                                            <div v-for="attribute in variation.attributes" :key="`${attribute.id}${variation.id}`" class="my-6">
                                                <!-- <InputLabel :for="attribute.id + variation.id" :value="attribute.name" /> -->
                                                <!-- <AutoComplete class="w-full" :dataKey="`${attribute.id}${variation.id}`" :inputId="`${attribute.id}${variation.id}`" v-model="attribute.value" optionLabel="value" dropdown :suggestions="attribute.attribute_options" @complete="attribute.search" /> -->
                                                <div class="flex gap-4">
                                                    <div class="w-full">
                                                        <InputLabel :value="attribute.name" />
                                                        <AutoComplete class="w-full" :dataKey="`${attribute.id}${variation.id}`" :inputId="`${attribute.id}${variation.id}`" v-model="attribute.value" optionLabel="value" dropdown :suggestions="attribute.attribute_options" @complete="attribute.search" />
                                                    </div>
                                                    <div>
                                                        <InputLabel value="Розхід" />
                                                        <InputText v-model="attribute.unit" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="basis-1/2 pl-2 pt-12">
                                        <FileUpload @select="onFilesVariation($event)" multiple accept="image/*">
                                            <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                                                <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                                                    <div class="flex gap-2">
                                                        <Button @click="chooseCallback()" icon="pi pi-images" rounded outlined severity="secondary"></Button>
                                                        <Button @click="clearCallback()" icon="pi pi-times" rounded outlined severity="danger" :disabled="!files || files.length === 0"></Button>
                                                    </div>
                                                </div>
                                            </template>
                                            <template #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback, messages }">
                                                <div class="flex flex-col gap-8 pt-4">
                                                    <div v-if="files.length > 0">
                                                        <h5>Нові</h5>
                                                        <div class="flex gap-4 flex-col">
                                                            <div v-for="(file, index) of files" :key="file.name + file.type + file.size" class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                                                <div>
                                                                    <img role="presentation" :alt="file.name" :src="file.objectURL" width="100" height="50" />
                                                                </div>
                                                                <span class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                                <div>{{ formatSize(file.size) }}</div>
                                                                <Button icon="pi pi-times" @click="removeFileCallback(index)" outlined rounded severity="danger" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div v-if="variation.images.length > 0">
                                                        <h5>Завантаженні</h5>
                                                        <div class="flex gap-4 flex-col">
                                                            <div v-for="(file, index) of variation.images" :key="file.name + file.type + file.size" class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                                                <div>
                                                                    <img role="presentation" :alt="file.name" :src="file.original_url" width="100" height="50" />
                                                                </div>
                                                                <span class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                                <div>{{ formatSize(file.size) }}</div>
                                                                <Button icon="pi pi-times" @click="deleteUploadedFileCallback(index)" outlined rounded severity="danger" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </FileUpload>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Зберегти
                        </PrimaryButton>
                    </form>
                </template>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
