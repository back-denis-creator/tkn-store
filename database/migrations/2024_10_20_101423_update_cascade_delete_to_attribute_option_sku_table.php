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
            // Column-array form, not the named-string form — SQLite can only
            // drop a foreign key this way (it rebuilds the table under the
            // hood), and this already-applied migration only needs to stay
            // replayable for a fresh environment (tests, local, CI).
            $table->dropForeign(['sku_id']);
            $table->dropForeign(['attribute_option_id']);
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
            $table->dropForeign(['sku_id']);
            $table->dropForeign(['attribute_option_id']);
            $table->foreign('sku_id')->references('id')->on('skus');
            $table->foreign('attribute_option_id')->references('id');
        });
    }
};
