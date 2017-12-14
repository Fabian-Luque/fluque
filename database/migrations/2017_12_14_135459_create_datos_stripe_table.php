<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatosStripeTable extends Migration {

    public function up() {
        if (!Schema::hasTable('datos_stripe')) {
            Schema::create(
                'datos_stripe', 
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('plan_id');
                    $table->string('cliente_id');
                    $table->string('subs_id');
                    $table->timestamps();
                }
            );
        }
    }

    public function down() {
        Schema::drop('datos_stripe');
    }
}