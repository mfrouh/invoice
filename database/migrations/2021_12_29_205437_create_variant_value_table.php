<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariantValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variant_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('value_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('variant_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['variant_id', 'value_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variant_value');
    }
}
