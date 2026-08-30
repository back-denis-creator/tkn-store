<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const activeTab = ref('info');
const tabLinkClass = (tab) => activeTab.value === tab
    ? 'text-amber-600 text-left'
    : 'text-gray-500 duration-100 hover:text-amber-600 text-left';
</script>

<template>
    <GuestLayout>
        <Head :title="$t('Profile Information')">
            <meta name="robots" content="noindex, nofollow" />
        </Head>

        <section
            class="container mx-auto w-full flex-grow max-w-[1200px] border-b py-5 lg:flex lg:flex-row lg:py-10"
        >
            <!-- sidebar  -->
            <section class="w-full flex-shrink-0 px-4 lg:w-[300px]">
                <div class="flex items-center border-b py-5">
                    <img
                        width="40px"
                        height="40px"
                        class="rounded-full object-cover"
                        src="/images/avatar-photo.png"
                        alt="Red woman portrait"
                    />
                    <div class="ml-5">
                        <p class="font-medium text-gray-500">{{ $t('Hello') }}</p>
                        <p class="font-bold">{{ user.name }}</p>
                    </div>
                </div>

                <div class="flex border-b py-5">
                    <div class="flex w-full">
                        <div class="flex flex-col gap-2">
                            <button type="button" @click="activeTab = 'info'" :class="tabLinkClass('info')">
                                {{ $t('Profile Information') }}
                            </button>
                            <button type="button" @click="activeTab = 'password'" :class="tabLinkClass('password')">
                                {{ $t('Change Password') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex border-b py-5">
                    <div class="flex w-full">
                        <div class="flex flex-col gap-2">
                            <Link
                                :href="route('orders.mine')"
                                class="flex items-center gap-2 font-medium hover:text-amber-600"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path
                                        d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375z"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        d="M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087zm6.163 3.75A.75.75 0 0110 12h4a.75.75 0 010 1.5h-4a.75.75 0 01-.75-.75z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                {{ $t('Order') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="flex py-5">
                    <div class="flex w-full">
                        <div class="flex flex-col gap-2">
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex items-center gap-2 font-medium hover:text-amber-600"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"
                                    />
                                </svg>
                                {{ $t('Log Out') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /sidebar  -->

            <!-- content  -->
            <section class="w-full max-w-[1200px] px-5 pb-10">
                <UpdateProfileInformationForm
                    v-if="activeTab === 'info'"
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />

                <UpdatePasswordForm v-else-if="activeTab === 'password'" class="max-w-xl" />
            </section>
            <!-- /content  -->
        </section>
    </GuestLayout>
</template>
