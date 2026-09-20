<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each hero slide gets its own headline, subtext and call-to-action
     * switch. A slide that leaves them empty keeps showing the translated
     * default text, which is what every existing slide does today.
     */
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->text('description')->nullable()->after('title');
            $table->boolean('show_button')->default(true)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'show_button']);
        });
    }
};
