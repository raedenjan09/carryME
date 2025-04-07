<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_state')) {
                $table->string('shipping_state')->nullable()->after('shipping_city');
            }
            if (!Schema::hasColumn('orders', 'shipping_zipcode')) {
                $table->string('shipping_zipcode')->nullable()->after('shipping_state');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('credit_card')->after('shipping_zipcode');
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('pending')->after('payment_method');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_state', 'shipping_zipcode', 'payment_method', 'status']);
        });
    }
};