<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import axios from 'axios';
import { trans, currentLocale } from 'laravel-vue-i18n';
import { EnvelopeIcon, PhoneIcon, MapPinIcon } from '@heroicons/vue/24/outline';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Labels are translated for display, but each value is kept in Ukrainian —
// that's what lands in the Telegram message the (Ukrainian-speaking) team
// reads, regardless of which locale the visitor filled the form in.
const categories = computed(() => {
    currentLocale.value; // eslint-disable-line no-unused-expressions -- track locale so this recomputes on change
    return [
        { label: trans('Category_Order', 'Замовлення та товари'), value: 'Замовлення та товари' },
        { label: trans('Category_Delivery', 'Доставка'), value: 'Доставка' },
        { label: trans('Category_Sewing', 'Індивідуальний пошив'), value: 'Індивідуальний пошив' },
        { label: trans('Category_Partnership', 'Співпраця / HoReCa'), value: 'Співпраця / HoReCa' },
        { label: trans('Category_Other', 'Інше'), value: 'Інше' },
    ];
});

const form = reactive({
    email: '',
    name: '',
    category: null,
    message: '',
    agreed: false,
});

const loading = ref(false);
const success = ref(false);
const error = ref(false);

const submitForm = async () => {
    loading.value = true;
    error.value = false;
    success.value = false;

    try {
        await axios.post(route('contact.store'), {
            name: form.name,
            email: form.email,
            category: form.category,
            message: form.message,
        });
        success.value = true;
        form.email = '';
        form.name = '';
        form.category = null;
        form.message = '';
        form.agreed = false;
    } catch (err) {
        console.error('Contact form submission failed:', err);
        error.value = true;
    } finally {
        loading.value = false;
    }
};
</script>
<template>
    <GuestLayout>
        <Head>
            <title>{{ $t('Contacts') }}</title>
            <meta name="description" content="Зв'яжіться з майстернею Casanel: контакти, доставка Новою Поштою та умови співпраці для індивідуального пошиття текстилю.">
            <link rel="canonical" :href="route('contacts')">

            <meta property="og:url" :content="route('contacts')">
            <meta property="og:type" content="website">
            <meta property="og:title" :content="$t('Contacts')">
            <meta property="og:description" content="Зв'яжіться з майстернею Casanel: контакти, доставка Новою Поштою та умови співпраці для індивідуального пошиття текстилю.">
            <meta property="og:image" content="/images/casanel-logo.png">

            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" :content="$t('Contacts')">
            <meta name="twitter:description" content="Зв'яжіться з майстернею Casanel: контакти, доставка Новою Поштою та умови співпраці для індивідуального пошиття текстилю.">
            <meta name="twitter:image" content="/images/casanel-logo.png">
        </Head>
        <template #header>
            <div class="relative">
                <img
                    class="w-full object-cover brightness-50 filter lg:h-[400px]"
                    src="/images/contact-bg.jpeg"
                    alt="Casanel textile workshop"
                />
                <div
                    class="absolute top-1/2 left-1/2 mx-auto flex w-11/12 max-w-[1200px] -translate-x-1/2 -translate-y-1/2 flex-col text-center text-white"
                >
                    <h1 class="text-4xl font-bold sm:text-5xl">{{ $t('Contacts_Title', "Зв'яжіться з нами") }}</h1>
                    <p class="mx-auto pt-3 max-w-2xl text-sm lg:pt-5 lg:text-lg font-light">
                        {{ $t('Contacts_Subtitle', 'Маєте запитання щодо замовлення чи індивідуального пошиття? Ми завжди раді допомогти.') }}
                    </p>
                </div>
            </div>
        </template>

        <!-- Contact info -->
        <section class="w-full flex-grow">
            <section class="mx-auto w-full max-w-[700px] px-5 pb-10 pt-10 lg:pt-16">
                <div class="bg-gray-50 rounded-3xl border border-gray-100 p-8 lg:p-10">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">{{ $t('Contacts_Info_Title', 'Наші контакти') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-full bg-amber-100 shrink-0">
                                <MapPinIcon class="h-5 w-5 text-amber-600" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">{{ $t('FooterAddressLabel') }}</p>
                                <p class="text-gray-700">{{ $t('FooterAddress') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-full bg-amber-100 shrink-0">
                                <EnvelopeIcon class="h-5 w-5 text-amber-600" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">{{ $t('Email') }}</p>
                                <a href="mailto:casanel.connect@gmail.com" class="text-gray-700 hover:text-amber-600 transition-colors">casanel.connect@gmail.com</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-full bg-amber-100 shrink-0">
                                <PhoneIcon class="h-5 w-5 text-amber-600" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Phone</p>
                                <a :href="'tel:' + $t('FooterPhone')" class="text-gray-700 hover:text-amber-600 transition-colors font-medium">{{ $t('FooterPhone') }}</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex gap-3 mt-1">
                                <a href="https://t.me/YafLa1GM5sYxNmRi" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-all hover:scale-110">
                                    <img class="h-6 w-6" src="/images/telegram.svg" alt="telegram" />
                                </a>
                                <a href="https://www.instagram.com/casanel.ua?igsh=OHQybTZiYzBhNnVu&utm_source=qr" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-all hover:scale-110">
                                    <img class="h-6 w-6" src="/images/instagram.svg" alt="instagram" />
                                </a>
                                <a href="https://www.facebook.com/share/18Bk66SNbU/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-all hover:scale-110">
                                    <img class="h-6 w-6" src="/images/facebook.svg" alt="facebook" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact form -->
            <section class="mx-auto my-5 text-center px-5">
                <h2 class="text-3xl font-bold text-gray-900">{{ $t('Contacts_Form_Title', 'Залишились питання?') }}</h2>
                <p class="text-gray-500 mt-2">{{ $t('Contacts_Form_Subtitle', 'Заповніть форму нижче — ми відповімо найближчим часом') }}</p>
            </section>

            <form class="mx-auto my-5 max-w-[600px] px-5 pb-10" @submit.prevent="submitForm">
                <div class="mx-auto">
                    <div class="my-3 flex flex-col sm:flex-row w-full gap-3">
                        <InputText v-model="form.email" class="w-full" type="email" :placeholder="$t('Email')" required />
                        <InputText v-model="form.name" class="w-full" type="text" :placeholder="$t('Full Name', 'Ім’я')" required />
                    </div>
                </div>

                <Select
                    v-model="form.category"
                    :options="categories"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="$t('Choose Category', 'Оберіть тему звернення')"
                    class="mb-3 w-full"
                />

                <Textarea
                    v-model="form.message"
                    class="w-full"
                    rows="5"
                    :placeholder="$t('Write Message', 'Напишіть повідомлення...')"
                    required
                />

                <div class="lg:items-center container mt-4 flex flex-col justify-between lg:flex-row gap-3">
                    <div class="flex items-center">
                        <Checkbox v-model="form.agreed" :binary="true" inputId="agree-terms" class="mr-3" required />
                        <label for="agree-terms">
                            {{ $t('I have read and agree with') }}
                            <a href="#" class="text-amber-500">{{ $t('terms & conditions') }}</a>
                        </label>
                    </div>
                    <Button
                        type="submit"
                        :label="$t('Send Message', 'Надіслати')"
                        :loading="loading"
                        :disabled="!form.agreed"
                        class="!bg-amber-400 !border-none !text-black hover:!bg-amber-500"
                    />
                </div>

                <transition name="fade">
                    <Message v-if="success" severity="success" icon="pi pi-check-circle" class="mt-4">{{ $t('Contacts_Form_Success', 'Дякуємо! Ваше повідомлення надіслано.') }}</Message>
                </transition>
                <transition name="fade">
                    <Message v-if="error" severity="error" icon="pi pi-exclamation-triangle" class="mt-4">{{ $t('Contacts_Form_Error', 'Щось пішло не так. Спробуйте ще раз або напишіть нам напряму.') }}</Message>
                </transition>
            </form>
            <!-- /Form  -->
        </section>
    </GuestLayout>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
