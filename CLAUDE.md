# Casanel Online Project Overview

Casanel.online is a modern, high-performance e-commerce platform developed using the Laravel framework and Vue.js. The project aims to provide a seamless shopping experience with a robust backend and a dynamic frontend.

## Technical Stack

### Backend
- **Framework**: [Laravel 11.x](https://laravel.com/) (PHP 8.2+)
- **Database**: Likely MySQL/PostgreSQL (Standard Laravel drivers)
- **File Management**: [Spatie MediaLibrary](https://spatie.be/docs/laravel-medialibrary/v11/introduction)
- **State/Routing**: [Inertia.js](https://inertiajs.com/) (Standard for Laravel-Vue SSR/SPA hybrid)
- **Auth**: [Laravel Breeze](https://laravel.com/docs/11.x/starter-kits#laravel-breeze) (Sanctum-based authentication)

### Frontend
- **Framework**: [Vue.js 3.4+](https://vuejs.org/) (Composition API, `<script setup>`)
- **UI Components**: [PrimeVue v4](https://primevue.org/) & [PrimeIcons](https://primeicons.org/)
- **Styling**: [Tailwind CSS v3.2+](https://tailwindcss.com/)
- **Build Tool**: [Vite 5.0+](https://vitejs.dev/)
- **I18n**: [laravel-vue-i18n](https://github.com/xiCO2k/laravel-vue-i18n)
- **Rich Text Editor**: [Quill](https://quilljs.com/)
- **Icons**: [Heroicons Vue](https://github.com/tailwindlabs/heroicons)
- **Utilities**: [Headless UI Vue](https://headlessui.com/vue/menu)

## Key Integrations
- **Nova Poshta**: [daaner/novaposhta](https://github.com/daaner/novaposhta) for delivery services in Ukraine.
- **Ziggy**: [tightenco/ziggy](https://github.com/tighten/ziggy) for sharing Laravel routes with JavaScript.

## Project Structure Highlights
- **Models**: Includes `Product`, `Category`, `Sku`, `Attribute`, `AttributeOption`, `Blog`, `Delivery`, `Storage`, and `User`.
- **Pages**: Core shop logic resides in `resources/js/Pages/` (`Catalog.vue`, `Product.vue`, `Cart.vue`, `Checkout.vue`).
- **Styles**: Tailwind CSS is used alongside PrimeVue themes.

## Getting Started
- **Install dependencies**: `composer install` & `npm install`
- **Environment**: Copy `.env.example` to `.env` and configure accordingly.
- **Run development server**: `npm run dev` and `php artisan serve`
- **Production build**: `npm run build`
