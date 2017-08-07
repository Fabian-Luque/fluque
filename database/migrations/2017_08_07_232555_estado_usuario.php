<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EstadoUsuario extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('estado_cuenta', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->unsigned();
            $table->smallInteger('estado')->default(1);
            $table->foreign('usuario_id')
                ->references('id')
                ->on('users')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('estado_cuenta');
    }
}