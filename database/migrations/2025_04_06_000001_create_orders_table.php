<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending');
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_country')->default('Philippines');
            $table->string('shipping_postal_code')->nullable();
            $table->string('payment_method')->default('credit_card');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};