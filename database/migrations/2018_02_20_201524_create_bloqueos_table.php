<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBloqueosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if (!Schema::hasTable('bloqueos')) {
            Schema::create('bloqueos', function (Blueprint $table) {
                $table->increments('id');
                $table->date('fecha_inicio');
                $table->date('fecha_fin');
                $table->integer('noches');
                $table->integer('habitacion_id')->unsigned();
                $table->foreign('habitacion_id')->references('id')->on('habitaciones')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
