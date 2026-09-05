<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { TruckIcon } from '@heroicons/vue/24/outline';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <GuestLayout>
        <Head>
            <title>{{ $t('Delivery') }}</title>
            <meta name="description" content="Delivery and payment terms for Casanel orders in Ukraine and abroad: Nova Poshta, Ukrposhta, shipping costs and tracking.">
            <link rel="canonical" :href="route('delivery')">

            <meta property="og:title" :content="$t('Delivery')">
            <meta property="og:description" content="Delivery and payment terms for Casanel orders in Ukraine and abroad.">
            <meta property="og:url" :content="route('delivery')">

            <meta name="twitter:title" :content="$t('Delivery')">
            <meta name="twitter:description" content="Delivery and payment terms for Casanel orders.">
        </Head>

        <template #header>
            <div class="relative overflow-hidden bg-gray-900 text-white">
                <!-- Decorative glow + watermark icon — no delivery photo available
                yet, so this stands in for it (see NavBar amber accent) without
                relying on a stock image we don't have rights to use. -->
                <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-amber-400/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-amber-400/10 blur-3xl"></div>
                <TruckIcon
                    class="pointer-events-none absolute -right-10 bottom-0 hidden h-64 w-64 text-amber-400/10 lg:block xl:h-80 xl:w-80"
                    aria-hidden="true"
                />

                <div class="relative mx-auto max-w-[1200px] px-5 py-16 lg:py-24 text-center">
                    <h1 class="text-3xl font-bold sm:text-5xl lg:text-6xl mb-4">{{ $t('Delivery') }}</h1>
                    <p class="text-lg sm:text-xl max-w-2xl mx-auto font-light leading-relaxed text-gray-300">
                        {{ $t('Delivery_Subtitle', 'Умови доставки замовлень по Україні та за кордон, а також порядок оплати') }}
                    </p>
                </div>
            </div>
        </template>

        <section class="mx-auto max-w-[1000px] px-5 py-20">
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">

                <!-- Delivery in Ukraine -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                        {{ $t('Delivery_Ukraine_Title', 'Доставка по Україні') }}
                    </h2>
                    <p class="mb-10">
                        {{ $t('Delivery_Ukraine_Intro', 'Ми здійснюємо доставку замовлень по Україні через «Нову пошту» та «Укрпошту».') }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 flex flex-col gap-4">
                            <h3 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-4">{{ $t('Nova Poshta', 'Нова пошта') }}</h3>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('NP_Item_1', 'Відправляємо замовлення у відділення, поштомат або за адресою.') }}</li>
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('NP_Item_2', 'Зазвичай доставка займає 2–3 робочі дні з моменту відправлення замовлення.') }}</li>
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('NP_Item_3', 'Звертаємо увагу, що в окремих випадках термін доставки може бути довшим через навантаження на логістичну мережу, погодні умови, повітряні тривоги або інші обставини, що впливають на роботу поштового оператора.') }}</li>
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('NP_Item_4', 'Вартість доставки оплачується покупцем відповідно до чинних тарифів «Нової пошти».') }}</li>
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('NP_Item_5', 'Після відправлення замовлення ми надамо номер накладної для відстеження посилки.') }}</li>
                            </ul>
                        </div>

                        <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 flex flex-col gap-4">
                            <h3 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-4">{{ $t('Ukrposhta', 'Укрпошта') }}</h3>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('UP_Item_1', 'Відправляємо замовлення у відділення або за адресою.') }}</li>
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('UP_Item_2', 'Термін доставки залежить від населеного пункту та обраного виду відправлення.') }}</li>
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('UP_Item_3', 'Вартість доставки — відповідно до тарифів «Укрпошти» та оплачується покупцем.') }}</li>
                                <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('UP_Item_4', 'Після відправлення замовлення ми надамо вам номер накладної для відстеження посилки.') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-amber-50/50 p-6 rounded-2xl border border-amber-100 text-sm">
                        <p class="m-0">
                            <strong class="text-gray-900">{{ $t('Important', 'Важливо') }}:</strong>
                            {{ $t('Delivery_Prepaid_Notice', 'якщо замовлення було передоплачене, отримати його може лише особа, зазначена в даних отримувача.') }}
                        </p>
                    </div>
                </div>

                <!-- International delivery -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                        {{ $t('Delivery_International_Title', 'Міжнародна доставка') }}
                    </h2>
                    <ul class="space-y-3">
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Intl_Item_1', 'Міжнародні замовлення відправляємо «Укрпоштою».') }}</li>
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Intl_Item_2', 'Вартість і термін доставки залежать від країни призначення, ваги та габаритів замовлення. Вартість доставки розраховується відповідно до чинних тарифів «Укрпошти» та оплачується покупцем.') }}</li>
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Intl_Item_3', 'Після відправлення ви отримаєте трек-номер для відстеження посилки.') }}</li>
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Intl_Item_4', 'Термін міжнародної доставки може змінюватися залежно від роботи поштових, митних та інших служб країни призначення.') }}</li>
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Intl_Item_5', 'У разі відмови від замовлення не через виробничий брак або помилку з нашого боку, витрати на зворотну доставку несе покупець.') }}</li>
                    </ul>
                </div>

                <!-- Receiving the order -->
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                        {{ $t('Delivery_Receiving_Title', 'Отримання замовлення') }}
                    </h2>
                    <ul class="space-y-3">
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Receiving_Item_1', 'Будь ласка, перевіряйте посилку під час отримання.') }}</li>
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Receiving_Item_2', 'Якщо упаковка пошкоджена або ви виявили пошкодження товару, рекомендуємо одразу зафіксувати це разом із представником поштового оператора та зв’язатися з нами.') }}</li>
                        <li class="flex gap-3"><span class="w-2 h-2 mt-2 bg-amber-400 rounded-full shrink-0"></span> {{ $t('Receiving_Item_3', 'Ми не несемо відповідальності за затримки доставки, спричинені роботою поштового оператора, але завжди готові допомогти у вирішенні проблеми.') }}</li>
                    </ul>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
