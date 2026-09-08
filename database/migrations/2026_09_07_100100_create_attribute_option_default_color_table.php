<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_option_default_color', function (Blueprint $table) {
            $table->foreignId('attribute_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('default_color_id')->constrained()->cascadeOnDelete();
            $table->primary(['attribute_option_id', 'default_color_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_option_default_color');
    }
};
