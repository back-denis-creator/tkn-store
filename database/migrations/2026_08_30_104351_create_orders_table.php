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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('customer_name');
            $table->string('customer_surname')->nullable();
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('delivery_method');
            $table->string('np_city_ref')->nullable();
            $table->string('np_city_name')->nullable();
            $table->string('np_warehouse_ref')->nullable();
            $table->string('np_warehouse_name')->nullable();
            $table->tinyInteger('payment_method');
            $table->tinyInteger('status')->default(1);
            $table->integer('total_amount');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
