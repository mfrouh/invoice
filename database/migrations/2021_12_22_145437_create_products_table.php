<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->unique();
            $table->boolean('status')->index();
            $table->decimal('price');
            $table->string('image')->default('/images/products/1.png');
            $table->text('description');
            $table->foreignId('seller_id')->index()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('category_id')->index()->constrained()->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('products');
    }
}
