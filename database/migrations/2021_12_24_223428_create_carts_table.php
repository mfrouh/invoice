<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->index()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('customer_id')->index()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name')->index();
            $table->decimal('price');
            $table->json('details')->nullable();
            $table->integer('quantity');
            $table->decimal('total_price');
            $table->unique(['product_id', 'customer_id']);
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
        Schema::dropIfExists('carts');
    }
}
