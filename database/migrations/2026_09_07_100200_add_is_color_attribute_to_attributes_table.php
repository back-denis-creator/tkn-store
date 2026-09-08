<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->boolean('is_color_attribute')->default(false)->after('name');
        });

        // Replaces the old hardcoded name === 'Колір' check — flip on for the
        // existing attribute so behavior is unchanged right after deploying,
        // even if it gets renamed later.
        DB::table('attributes')->where('name', 'Колір')->update(['is_color_attribute' => true]);
    }

    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn('is_color_attribute');
        });
    }
};
