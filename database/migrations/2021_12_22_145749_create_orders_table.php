<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->startingValue(1996000);
            $table->uuid('invoice_qr_code')->unique();
            $table->foreignId('customer_id')->index()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('tax')->nullable();
            $table->decimal('ship')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('total');
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
        Schema::dropIfExists('orders');
    }
}
