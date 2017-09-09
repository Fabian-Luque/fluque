<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('ALTER TABLE reservas MODIFY precio_habitacion DOUBLE(10,2)');
        DB::statement('ALTER TABLE reservas MODIFY monto_alojamiento DOUBLE(10,2)');
        DB::statement('ALTER TABLE reservas MODIFY monto_consumo DOUBLE(10,2)');
        DB::statement('ALTER TABLE reservas MODIFY monto_total DOUBLE(10,2)');
        DB::statement('ALTER TABLE reservas MODIFY monto_por_pagar DOUBLE(10,2)');
        DB::statement('ALTER TABLE huesped_reserva_servicio MODIFY precio_total DOUBLE(10,2)');
        DB::statement('ALTER TABLE metodo_pago_propiedad_servicio MODIFY precio_total DOUBLE(10,2)');
        DB::statement('ALTER TABLE pagos MODIFY monto_pago DOUBLE(10,2)');
        DB::statement('ALTER TABLE cliente_propiedad_servicio MODIFY precio_total DOUBLE(10,2)');

            
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
