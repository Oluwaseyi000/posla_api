<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->uuid('freelancer_id');
            $table->foreign('freelancer_id')->references('id')->on('users');
            $table->uuid('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('reference')->comment('generated locally');
            $table->string('ext_reference')->comment('reference from payment channel');
            $table->string('transaction_channel')->comment('channel type: paystack, paypal, bitpay');
            $table->string('transaction_metadata')->comment('json encode response');
            $table->string('amount_paid');
            $table->string('amount_expected');
            $table->string('payment_type')->nullable();
            $table->string('currency');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_transactions');
    }
}
