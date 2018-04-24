<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedenciaTablaPagosOnline extends Migration {
    public function up() {
        if (!Schema::hasColumn('pasarela_pago', 'procedencia')) {
            Schema::table(
                'pasarela_pago', 
                function (Blueprint $table) {
                    $table->string('procedencia');
                }
            );

            DB::table('pasarela_pago')
                ->where('nombre', 'pagofacil')
            ->update(['procedencia' => 'nacional']);
        }
    }

    public function down() {}
}
