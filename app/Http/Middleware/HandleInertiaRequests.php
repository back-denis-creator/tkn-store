<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => fn () => app()->getLocale(),
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            // Shared globally (not per-page) so the cart icon badge in NavBar
            // stays accurate no matter which page is currently open.
            'cartCount' => fn () => collect($request->session()->get('cart', []))->sum('quantity'),
            // Same reason — AnnouncementBar renders on every public page via
            // GuestLayout/Welcome.vue, so it can't rely on any one controller
            // passing this through.
            'announcement' => function () {
                $settings = SiteSetting::current();

                return [
                    'enabled' => $settings->announcement_enabled,
                    'mode' => $settings->announcement_mode,
                    'custom_text' => $settings->announcement_custom_text,
                    'threshold' => $settings->free_shipping_threshold / 100,
                ];
            },
        ]);
    }

    // public function handle(Request $request, \Closure $next)
    // {
    //     // Установка локали из сессии
    //     $locale = session('locale', config('app.locale'));
    //     app()->setLocale($locale);
    //     return parent::handle($request, $next);
    // }
}
