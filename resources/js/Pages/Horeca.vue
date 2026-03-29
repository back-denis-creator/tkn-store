<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

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
            <title>{{ $t('HoReCa') }}</title>
            <meta name="description" content="Professional custom textile for restaurants, hotels, and brands. HoReCa & B2B solutions by Casanel.">
            
            <meta property="og:title" :content="$t('HoReCa')">
            <meta property="og:description" content="Professional textile for your business. Custom sewing for HoReCa and B2B.">
            <meta property="og:image" content="/images/horeca-bg.png">
            
            <meta name="twitter:title" :content="$t('HoReCa')">
            <meta name="twitter:description" content="Textile solutions for restaurants and brands.">
            <meta name="twitter:image" content="/images/horeca-bg.png">
        </Head>

        <template #header>
            <div class="relative overflow-hidden group">
                <img
                    class="w-full object-cover brightness-50 filter lg:h-[600px] transition-all duration-700 group-hover:scale-105"
                    src="/images/horeca-bg.png"
                    alt="HoReCa Textile Solutions"
                />
                <div
                    class="absolute top-1/2 left-1/2 mx-auto flex w-11/12 max-w-[1200px] -translate-x-1/2 -translate-y-1/2 flex-col text-center text-white"
                >
                    <h1 class="text-3xl font-bold sm:text-5xl lg:text-6xl mb-4">{{ $t('Horeca_Title', 'HoReCa & B2B текстиль на замовлення') }}</h1>
                    <p class="text-lg sm:text-xl max-w-3xl mx-auto font-light leading-relaxed">
                        {{ $t('Horeca_Subtitle', 'Професійний текстиль для ресторанів, готелів і брендів') }}
                    </p>
                </div>
            </div>
        </template>

        <section class="mx-auto max-w-[1000px] px-5 py-20">
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed text-center mb-20">
                <p class="text-xl italic text-gray-600 mb-8">
                    {{ $t('Horeca_Intro_1', 'У сфері HoReCa текстиль — це не декор, а частина сервісу. Те, як виглядає стіл, серветка чи пакування, формує перше враження і напряму впливає на сприйняття вашого бренду.') }}
                </p>
                <p>
                    {{ $t('Horeca_Intro_2', 'Casanel займається пошиттям текстилю на замовлення для HoReCa та B2B сегменту. Ми створюємо вироби, які не тільки виглядають естетично, а й витримують щоденне інтенсивне використання.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-20">
                <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100 flex flex-col gap-6">
                    <h3 class="text-2xl font-bold text-gray-900 border-b border-gray-200 pb-4">{{ $t('Production for HoReCa', 'Виробництво текстилю для HoReCa') }}</h3>
                    <p class="text-gray-600">{{ $t('Horeca_Production_Desc', 'Пропонуємо пошиття текстилю для ресторанів, кафе, готелів і заміських комплексів:') }}</p>
                    <ul class="space-y-3 font-medium text-gray-800">
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Tablecloths', 'скатертини') }}</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Runners', 'раннери') }}</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Napkins', 'серветки') }}</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Aprons', 'фартухи та уніформа персоналу') }}</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Terrace Textile', 'текстиль для залу та літніх терас') }}</li>
                    </ul>
                    <p class="text-sm text-gray-500 italic">{{ $t('Horeca_Production_Footer', 'Кожен виріб адаптується під формат закладу, стиль інтер’єру та рівень навантаження.') }}</p>
                </div>

                <div class="bg-amber-50/30 p-10 rounded-3xl border border-amber-100 flex flex-col gap-6">
                    <h3 class="text-2xl font-bold text-gray-900 border-b border-amber-200 pb-4">{{ $t('Textile for Brands', 'Текстиль для брендів та B2B клієнтів') }}</h3>
                    <p class="text-gray-600">{{ $t('B2B_Desc', 'Працюємо з брендами та онлайн-магазинами, яким потріген якісний текстиль під продаж або пакування:') }}</p>
                    <ul class="space-y-3 font-medium text-gray-800">
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Textile Packaging', 'текстильні мішечки, конверти, чохли') }}</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Clothing Packaging', 'пакування для одягу, білизни та аксесуарів') }}</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Shoppers', 'шопери (брендовані тканинні сумки)') }}</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-amber-400 rounded-full"></span> {{ $t('Serial Production', 'серійне виробництво під бренд') }}</li>
                    </ul>
                    <p class="text-sm text-gray-500 italic">{{ $t('B2B_Footer', 'Такий текстиль підсилює продукт і додає цінності без здешевлення вигляду.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-20">
                <div class="flex flex-col gap-6 p-8">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $t('Corporate Textile', 'Корпоративний текстиль і подарунки') }}</h3>
                    <p class="text-gray-600">{{ $t('Corporate_Desc', 'Рішення для компаній, які працюють з клієнтським досвідом і брендом:') }}</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2">✓ {{ $t('Textile Gifts', 'текстильні подарунки') }}</li>
                        <li class="flex items-center gap-2">✓ {{ $t('Branded Merch', 'брендований мерч') }}</li>
                        <li class="flex items-center gap-2">✓ {{ $t('Gift Packaging', 'подарункове пакування') }}</li>
                        <li class="flex items-center gap-2">✓ {{ $t('Corporate Shoppers', 'брендовані шопери для корпоративних наборів') }}</li>
                    </ul>
                </div>

                <div class="flex flex-col gap-6 p-8 bg-gray-900 text-white rounded-3xl">
                    <h3 class="text-2xl font-bold">{{ $t('Custom Under Projects', 'Індивідуальне пошиття під проєкти') }}</h3>
                    <p class="text-gray-400">{{ $t('Projects_Desc', 'Співпрацюємо з дизайн-студіями та івент-агенціями:') }}</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2">✓ {{ $t('Specific Project', 'вироби під конкретний проєкт') }}</li>
                        <li class="flex items-center gap-2">✓ {{ $t('Unstandard Shapes', 'нестандартні форми та складний крій') }}</li>
                        <li class="flex items-center gap-2">✓ {{ $t('Technical Task', 'робота за технічним завданням') }}</li>
                    </ul>
                </div>
            </div>

            <div class="mb-20">
                <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center uppercase tracking-wider">{{ $t('Why Benefit for Business', 'Чому це вигідно для бізнесу') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-4 border-b pb-2">{{ $t('Wear-Resistant', 'Зносостійкість') }}</h4>
                        <p class="text-sm text-gray-600">{{ $t('Wear_Desc', 'Тканини витримують часте прання, зберігають форму і вигляд навіть у високому навантаженні.') }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-4 border-b pb-2">{{ $t('Savings', 'Економія') }}</h4>
                        <p class="text-sm text-gray-600">{{ $t('Savings_Desc', 'Водовідштовхувальні тканини менше вбирають бруд, потребують рідшого прання і служать довше.') }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-4 border-b pb-2">{{ $t('Branding', 'Брендування') }}</h4>
                        <p class="text-sm text-gray-600">{{ $t('Branding_Desc', 'Наносимо логотип машинною вишивкою: не стирається, виглядає професійно і підсилює айдентику.') }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-4 border-b pb-2">{{ $t('Quality Control', 'Контроль якості') }}</h4>
                        <p class="text-sm text-gray-600">{{ $t('Quality_Desc', 'Кожне замовлення контролюється: точні розміри, правильна геометрія та міцні шви.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-amber-400 text-gray-900 p-12 rounded-[3rem] mb-20">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-3xl font-bold mb-6 italic">{{ $t('Experience in Sewing') }}</h2>
                    <p class="text-xl leading-relaxed">
                        {{ $t('Horeca_Experience_Text', '15+ років індивідуального пошиття дозволяють нам реалізовувати складні проєкти, працювати з різними типами тканин та досягати стабільної якості в кожному виробі.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-20">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">{{ $t('How to Order', 'Як замовити текстиль') }}</h3>
                    <div class="space-y-6">
                        <div class="flex gap-4 items-center">
                            <span class="text-3xl font-black text-amber-400">01</span>
                            <p class="pt-2 font-medium">{{ $t('Step_1', 'Ви описуєте потребу або проєкт') }}</p>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="text-3xl font-black text-amber-400">02</span>
                            <p class="pt-2 font-medium">{{ $t('Step_2', 'Підбираємо матеріали і формат виробів') }}</p>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="text-3xl font-black text-amber-400">03</span>
                            <p class="pt-2 font-medium">{{ $t('Step_3', 'Запускаємо виготовлення') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-10 rounded-3xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ $t('Contacts') }}</h3>
                    <p class="mb-8 text-gray-600">{{ $t('Horeca_Contact_Intro', 'Щоб замовити текстиль для HoReCa або B2B, напишіть нам або зателефонуйте:') }}</p>
                    <div class="space-y-4">
                        <p class="flex items-center gap-3">
                            <i class="pi pi-instagram text-amber-500"></i>
                            <span>Instagram / Direct</span>
                        </p>
                        <p class="flex items-center gap-3">
                            <i class="pi pi-send text-amber-500"></i>
                            <span>Telegram / Viber</span>
                        </p>
                        <p class="flex items-center gap-3">
                            <i class="pi pi-phone text-amber-500"></i>
                            <span>{{ $t('FooterPhone') }}</span>
                        </p>
                        <p class="flex items-center gap-3">
                            <i class="pi pi-envelope text-amber-500"></i>
                            <span>Email: casanel.connect@gmail.com</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center font-bold text-xl text-gray-900 px-10">
                {{ $t('Horeca_Final_CTA', 'Опишіть задачу, обсяги і формат використання — запропонуємо рішення, яке буде вигідним у роботі, а не тільки на вигляд.') }}
            </div>
        </section>
    </GuestLayout>
</template>
