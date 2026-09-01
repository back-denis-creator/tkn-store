<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head :title="$t('Email Verification', 'Підтвердження email')">
            <meta name="robots" content="noindex, nofollow" />
        </Head>

        <!-- Verify email card  -->
        <div class="container mx-auto rounded-lg border border-gray-200 px-5 py-5 shadow-sm md:w-1/2 mt-10 mb-10">
            <div class="">
                <p class="text-4xl font-bold uppercase">{{ $t('Verify Email', 'Підтвердіть Email') }}</p>
                <p>{{ $t('Almost there', 'Залишилось трохи') }}! 📧</p>
            </div>

            <div class="mt-6 text-sm text-gray-600">
                {{ $t(
                    "Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.",
                    'Дякуємо за реєстрацію! Перш ніж почати, підтвердіть, будь ласка, вашу email-адресу, перейшовши за посиланням у листі, який ми щойно надіслали. Якщо лист не прийшов, ми з радістю надішлемо новий.'
                ) }}
            </div>

            <div class="mt-4 font-medium text-sm text-green-600" v-if="verificationLinkSent">
                {{ $t('A new verification link has been sent to the email address you provided during registration.', 'Нове посилання для підтвердження надіслано на email, вказаний під час реєстрації.') }}
            </div>

            <form class="mt-4" @submit.prevent="submit">
                <div class="flex items-center justify-between">
                    <Button
                        type="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="h-10 !border-none !bg-amber-400 uppercase !text-black hover:!bg-yellow-300"
                    >
                        {{ $t('Resend Verification Email', 'Надіслати лист повторно') }}
                    </Button>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="underline text-sm text-amber-600 hover:text-amber-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                        >{{ $t('Log Out', 'Вийти') }}</Link
                    >
                </div>
            </form>
        </div>
        <!-- /Verify email card  -->
    </GuestLayout>
</template>
