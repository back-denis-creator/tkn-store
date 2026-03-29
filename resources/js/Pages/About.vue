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
            <title>{{ $t('About Us') }}</title>
            <meta name="description" content="Casanel: Textile art with soul and 15 years of experience. Discover the story of our brand and our passion for quality.">
            
            <meta property="og:title" :content="$t('About Us')">
            <meta property="og:description" content="The story of Casanel: From a cozy home idea to professional textile production.">
            <meta property="og:image" content="/images/about-bg.png">
            
            <meta name="twitter:title" :content="$t('About Us')">
            <meta name="twitter:description" content="Textile art with soul and experience.">
            <meta name="twitter:image" content="/images/about-bg.png">
        </Head>

        <template #header>
            <div class="relative">
                <img
                    class="w-full object-cover brightness-50 filter lg:h-[600px]"
                    src="/images/about-bg.png"
                    alt="Casanel Textile Workshop"
                />
                <div
                    class="absolute top-1/2 left-1/2 mx-auto flex w-11/12 max-w-[1200px] -translate-x-1/2 -translate-y-1/2 flex-col text-center text-white"
                >
                    <h1 class="text-3xl font-bold sm:text-5xl lg:text-6xl mb-4">{{ $t('About_Title', 'Casanel: Мистецтво текстилю з душею та 15-річним досвідом') }}</h1>
                    <p class="text-lg sm:text-xl max-w-3xl mx-auto font-light leading-relaxed">
                        {{ $t('About_Intro', 'Історія бренду Casanel — це шлях від ідеї затишного дому до професійного виробництва текстилю, де кожен виріб має свій характер') }}
                    </p>
                </div>
            </div>
        </template>

        <section class="mx-auto max-w-[1000px] px-5 py-20">
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                        {{ $t('Our Roots', 'Наше коріння') }}
                    </h2>
                    <p class="mb-6">
                        {{ $t('About_Roots_1', 'Бренд був заснований у 2021 році, але новий етап його життя розпочався у 2023-му, коли Casanel перейшов до рук людини, для якої шиття — це покликання.') }}
                    </p>
                    <p>
                        {{ $t('About_Roots_2', 'Я — Оксана, нинішня власниця бренду, і мій шлях у світі професійного пошиття розпочався ще у 2011 році. Понад 10 років я створювала складні індивідуальні замовлення, вивчаючи тонкощі роботи з різними тканинами та формами.') }}
                    </p>
                </div>

                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                        {{ $t('From Craft to Brand', 'Від майстерності до бренду') }}
                    </h2>
                    <p class="mb-8">
                        {{ $t('About_Craft_1', 'Сьогодні Casanel — це поєднання багаторічного досвіду та сучасного погляду на домашній затишок. Коли я очолила бренд, моєю головною метою стало збереження традицій якості та впровадження професійного підходу до кожної деталі.') }}
                    </p>
                    
                    <div class="bg-amber-50/50 p-8 rounded-3xl border border-amber-100 mb-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 text-center italic">{{ $t('Personal Control', 'Я особисто контролюю кожен етап створення ваших замовлень:') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="flex flex-col gap-3">
                                <div class="w-10 h-10 bg-amber-400 rounded-full flex items-center justify-center text-white shrink-0 font-bold">1</div>
                                <div>
                                    <h4 class="font-bold mb-2 text-gray-900">{{ $t('Fabric Selection', 'Ретельний підбір тканин') }}</h4>
                                    <p class="text-sm text-gray-600">{{ $t('Fabric_Desc', 'ми обираємо лише ті матеріали, які служать роками (зокрема, наші відомі водовідштовхувальні полотна).') }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-3">
                                <div class="w-10 h-10 bg-amber-400 rounded-full flex items-center justify-center text-white shrink-0 font-bold">2</div>
                                <div>
                                    <h4 class="font-bold mb-2 text-gray-900">{{ $t('Professional Cut', 'Професійний крій') }}</h4>
                                    <p class="text-sm text-gray-600">{{ $t('Cut_Desc', 'завдяки досвіду в індивідуальному пошитті, ми створюємо вироби, що ідеально лягають на ваші меблі.') }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-3">
                                <div class="w-10 h-10 bg-amber-400 rounded-full flex items-center justify-center text-white shrink-0 font-bold">3</div>
                                <div>
                                    <h4 class="font-bold mb-2 text-gray-900">{{ $t('Attention to Detail', 'Увага до деталей') }}</h4>
                                    <p class="text-sm text-gray-600">{{ $t('Detail_Desc', 'кожен шов — це результат майстерності, відточеної роками.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                        {{ $t('Our Mission') }}
                    </h2>
                    <p class="mb-6">
                        {{ $t('Mission_Text_New', 'Ми не просто шиємо скатертини чи подушки. Ми створюємо атмосферу, в яку хочеться повертатися. Casanel сьогодні — це невелике, але амбітне українське виробництво. Кожне ваше замовлення допомагає нам розвиватися та наближати мрію: зробити український текстиль впізнаваним та улюбленим у кожному домі.') }}
                    </p>
                </div>

                <div class="text-center bg-gray-900 text-white p-10 rounded-3xl shadow-xl">
                    <p class="text-xl md:text-2xl font-medium leading-relaxed italic">
                        "{{ $t('About_Closing', 'Обираючи Casanel, ви обираєте досвід, перевірений часом, та якість, створену з любов’ю') }}"
                    </p>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
