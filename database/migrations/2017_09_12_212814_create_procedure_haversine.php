<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureHaversine extends Migration {
    public function up() {
        $sql = "DROP PROCEDURE IF EXISTS haversine";
        DB::unprepared($sql);
        $sql = "
            CREATE FUNCTION `haversine`(location GEOMETRY, pos_lat DOUBLE, pos_lng DOUBLE) RETURNS DOUBLE
                NO SQL
                DETERMINISTIC
                BEGIN
                    RETURN (
                        6372.795477598 *
                        ACOS(
                            SIN ( RADIANS(x(location)) ) * SIN( RADIANS((pos_lat)) ) + 
                            COS ( RADIANS(x(location)) ) * COS( RADIANS((pos_lat)) ) * 
                            COS ( RADIANS(y(location)) - RADIANS(pos_lng) )
                        )
                    );
                END
        ";
        DB::unprepared($sql);
    }

    public function down() {
        $sql = "DROP PROCEDURE IF EXISTS haversine";
        DB::unprepared($sql);
    }
}