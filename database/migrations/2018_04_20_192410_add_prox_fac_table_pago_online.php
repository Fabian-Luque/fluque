<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProxFacTablePagoOnline extends Migration {
    public function up() {
        if (!Schema::hasColumn('pagos_online', 'prox_fac')) {
            Schema::table(
                'pagos_online', 
                function ($table) {
                    $table->timestamp('prox_fac')->after('plan_id');
                }
            );
        }
    }

    public function down() {
    }
}
