<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('announcement_enabled')->default(true);
            $table->string('announcement_mode')->default('free_shipping'); // 'free_shipping' | 'custom'
            $table->string('announcement_custom_text')->nullable();
            $table->unsignedInteger('free_shipping_threshold')->default(350000); // kopecks, matches orders.total_amount's unit
            $table->timestamps();
        });

        // Seeded here so behavior is unchanged right after migrating — the
        // site keeps showing the same free-shipping banner it always has
        // until an admin actually edits it in /settings.
        DB::table('site_settings')->insert([
            'announcement_enabled' => true,
            'announcement_mode' => 'free_shipping',
            'free_shipping_threshold' => 350000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
