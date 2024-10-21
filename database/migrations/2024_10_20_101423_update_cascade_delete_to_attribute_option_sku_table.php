<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attribute_option_sku', function (Blueprint $table) {
            $table->dropForeign('attribute_option_sku_sku_id_foreign');
            $table->dropForeign('attribute_option_sku_attribute_option_id_foreign');
            $table->foreign('sku_id')->references('id')->on('skus')->onDelete('cascade');
            $table->foreign('attribute_option_id')->references('id')->on('attribute_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_option_sku', function (Blueprint $table) {
            $table->dropForeign('attribute_option_sku_sku_id_foreign');
            $table->dropForeign('attribute_option_sku_attribute_option_id_foreign');
            $table->foreign('sku_id')->references('id')->on('skus');
            $table->foreign('attribute_option_id')->references('id');
        });
    }
};
