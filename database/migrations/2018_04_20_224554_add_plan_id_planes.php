<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlanIdPlanes extends Migration {
    public function up() {
        if (!Schema::hasColumn('planes', 'plan_id')) {
            Schema::table(
                'planes', 
                function (Blueprint $table) {
                    $table->integer('plan_id');
                }
            );

            DB::table('planes')
                ->where('facturacion', 'mensual')
            ->update(['plan_id' => 1]);

            DB::table('planes')
                ->where('facturacion', 'semestral')
            ->update(['plan_id' => 2]);

            DB::table('planes')
                ->where('facturacion', 'anual')
            ->update(['plan_id' => 3]);
        }
    }

    public function down(){
    }
}
