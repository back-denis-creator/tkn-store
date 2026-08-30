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
- **Pages**: Core shop logic resides in `resources/js/Pages/` (`Catalog.vue`, `Product.vue`, `Cart.vue`, `Checkout.vue`), plus admin CRUD pages (`Products/`, `Categories/`, `Attributes/`, `Blogs/` — each with `Create/Edit/Index`) gated by `App\Http\Middleware\Admin`.
- **Styles**: Tailwind CSS is used alongside PrimeVue themes.
- **Business domain**: Ukrainian online store (default locale `uk`, `en` fallback, `config('app.locales')`), Nova Poshta as the only delivery carrier. Also serves a HORECA (hospitality/food-service B2B) landing page (`/horeca`).
- **Product model**: Products → SKUs (variants) → AttributeOptions (e.g. size/color) → Attributes. Categories are self-referential via `parent_id` (nested categories). `Storage` tracks stock per `attribute_option_id`.
- **Cart/Checkout**: session-based cart (no `Cart` model/table — stored in `session()->get('cart')`), Nova Poshta city/warehouse lookups via AJAX (`NPController`, `np_cities`/`np_warehouses` tables).
- **Vendor override trick**: `composer.json` autoload maps `Illuminate\\` → `app/Overrides/` and excludes `vendor/daaner/novaposhta/.../Address.php` from the classmap, replaced by `app/Overrides/Address.php` — a patched Nova Poshta Address model. Re-run `composer dump-autoload` if this override stops taking effect after a vendor update.
- **Routes**: all in `routes/web.php`, no per-module route groups. Note `products.update` is registered as `POST`, not `PUT/PATCH` (custom split from the resource route).

## Testing
- PHPUnit (`phpunit.xml`). Only Breeze-default coverage exists (`tests/Feature/Auth/*`, `ProfileTest.php`) plus placeholder `ExampleTest.php` files.
- **No tests exist yet for the shop domain** (Product, Cart, Checkout, Category, Sku, Storage) — write Feature tests for these areas when touching them, there's no existing pattern to break.

## Deployment
- No Docker/CI config in the repo — deployed directly to a VPS/shared host, not containerized.
- Production path: `/home/casanel/casanel.online/www`, running as `www-data`.
- SSR worker managed by Supervisor (`laravel-worker.cnf`): runs `php artisan inertia:start-ssr`, logs to `/home/casanel/casanel.online/laravel-worker.log`. `INERTIA_SSR_PORT=13715` is deliberately non-default to avoid port conflicts on the host.
- Local dev DB is SQLite (`DB_CONNECTION=sqlite`); production likely MySQL/Postgres (driver-agnostic migrations) — confirm actual prod driver before assuming SQLite semantics matter.
- `FILESYSTEM_DISK=local` currently — AWS S3 env vars exist but are blank, so S3 is not actively wired up for media storage yet.

## Getting Started
- **Install dependencies**: `composer install` & `npm install`
- **Environment**: Copy `.env.example` to `.env` and configure accordingly.
- **Run development server**: `npm run dev` and `php artisan serve`
- **Production build**: `npm run build`
