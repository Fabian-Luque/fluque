<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAtributosPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
        $table->string('numero_operacion')->after('tipo')->nullable();
        $table->integer('tipo_comprobante_id')->after('numero_operacion')->nullable()->unsigned();
        $table->foreign('tipo_comprobante_id')->references('id')->on('tipo_comprobante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagos', function (Blueprint $table) {
        $table->dropColumn('numero_operacion');
        $table->dropColumn('tipo_comprobante_id');
        });
    }
}
