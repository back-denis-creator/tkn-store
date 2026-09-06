<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::current();

        return Inertia::render('Settings/Index', [
            'settings' => [
                'announcement_enabled' => $settings->announcement_enabled,
                'announcement_mode' => $settings->announcement_mode,
                'announcement_custom_text' => $settings->announcement_custom_text,
                'free_shipping_threshold' => $settings->free_shipping_threshold / 100,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'announcement_enabled' => 'required|boolean',
            'announcement_mode' => 'required|in:'.implode(',', [SiteSetting::MODE_FREE_SHIPPING, SiteSetting::MODE_CUSTOM]),
            'announcement_custom_text' => 'nullable|string|max:255|required_if:announcement_mode,'.SiteSetting::MODE_CUSTOM,
            'free_shipping_threshold' => 'required|numeric|min:0',
        ]);

        SiteSetting::current()->update([
            'announcement_enabled' => $validated['announcement_enabled'],
            'announcement_mode' => $validated['announcement_mode'],
            'announcement_custom_text' => $validated['announcement_custom_text'] ?? null,
            'free_shipping_threshold' => (int) round($validated['free_shipping_threshold'] * 100),
        ]);

        return back()->with('message', 'Налаштування збережено');
    }
}
