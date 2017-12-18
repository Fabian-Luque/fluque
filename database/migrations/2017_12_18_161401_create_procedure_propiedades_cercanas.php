<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedurePropiedadesCercanas extends Migration {
    /**
     * Procedimiento almacenado que retorna las propiedades cercanas
        a partir de un punto(latitud , longitud) y en un radio determinado 
        en kilometros cuadrados
     *
     * @return void
     */
    public function up() {
       $sql = "
            DELIMITER ;;
            DROP PROCEDURE IF EXISTS propiedades_cercanas ;;
            CREATE PROCEDURE propiedades_cercanas(pos_lat DOUBLE, pos_lng DOUBLE, radio_km DOUBLE)
                BEGIN
                    DECLARE fin INT;
                    DECLARE idd INT DEFAULT 0;
                    DECLARE prop_idd INT DEFAULT 0;
                    DECLARE latitudd DOUBLE DEFAULT 0;
                    DECLARE longitudd DOUBLE DEFAULT 0;

                    DECLARE cur1 CURSOR
                    FOR
                    SELECT 
                        id,
                        prop_id,
                        X(`location`) as latitud,
                        Y(`location`) as longitud
                    FROM `ubicacion_propiedad`
                    WHERE ( 
                        6372.795477598 *
                        ACOS(
                            SIN ( RADIANS(x(location)) ) * SIN( RADIANS((pos_lat)) ) + 
                            COS ( RADIANS(x(location)) ) * COS( RADIANS((pos_lat)) ) * 
                            COS ( RADIANS(y(location)) - RADIANS(pos_lng) )
                        )
                    ) < radio_km;

                    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET fin=1;    

                    OPEN cur1;
                    ubicaciones: LOOP
                    FETCH cur1 INTO idd, prop_idd, latitudd, longitudd;
 
                        IF fin = 1 THEN 
                            LEAVE ubicaciones;
                        END IF;

                        SELECT idd as id, prop_idd as prop_id, latitudd as latitud, longitudd as longitud;
 
                    END LOOP ubicaciones;
                    CLOSE cur1;
                END;;
            DELIMITER ;;
        "; 
        DB::connection()->getPdo()->exec($sql); 
    }

    public function down(){
        $sql = "
            DELIMITER ;;
                DROP PROCEDURE IF EXISTS propiedades_cercanas ;;
            DELIMITER ;;
        ";
        DB::connection()->getPdo()->exec($sql);
    }
}