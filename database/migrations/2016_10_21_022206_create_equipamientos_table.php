<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipamientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('equipamiento', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('bano')->default(0);
            $table->boolean('tv')->default(0);
            $table->boolean('wifi')->default(0);
            $table->boolean('frigobar')->default(0);
            $table->integer('habitacion_id')->unsigned()->index();
            $table->foreign('habitacion_id')->references('id')->on('habitaciones')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('equipamiento');

    }
}
