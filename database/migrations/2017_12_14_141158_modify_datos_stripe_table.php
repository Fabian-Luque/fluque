<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDatosStripeTable extends Migration {

    public function up() {
        if (!Schema::hasTable('datos_stripe')) {
            Schema::create(
                'datos_stripe', 
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('plan_id')->nullable();
                    $table->string('cliente_id')->nullable();
                    $table->string('subs_id')->nullable();
                    $table->timestamps();
                }
            );
        }
    }

    public function down() {
        if (Schema::hasTable('datos_stripe')) {
            Schema::drop('datos_stripe');
        }
    }
}
