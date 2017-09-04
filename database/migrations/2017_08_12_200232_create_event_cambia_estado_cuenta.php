<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventCambiaEstadoCuenta extends Migration {
    /**
     * Run the migrations.
     *
        Evento revisa cada 12 horas(720min), si la cuenta es de prueba y han
        pasado 15 dias desde que se creo, setea el estado a 2, que 
        significa cuenta inactiva  
     * @return void
     */
    public function up() {
        DB::unprepared('
            SET GLOBAL event_scheduler = ON;
            DELIMITER |

            CREATE EVENT Evento
            ON SCHEDULE EVERY 1 MINUTE STARTS CURRENT_TIMESTAMP 
            DO
            BEGIN
                DECLARE fin INT DEFAULT 0;
                DECLARE pid INT;
                DECLARE dias INT;
                DECLARE state INT;
                DECLARE created timestamp;  
                DECLARE cur CURSOR FOR 
                    SELECT id, estado_cuenta_id, created_at 
                    FROM `propiedades`;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin = 1;

                OPEN cur;
                mi_loop: LOOP
 
                    FETCH cur INTO pid, state, created;
                    SELECT DATEDIFF(now(), created ) INTO dias;
 
                    IF fin = 1 THEN 
                        LEAVE mi_loop;
                    END IF;
                    
                    IF dias = 0 THEN
                        IF state = 1 THEN
                            UPDATE `propiedades` 
                            SET estado_cuenta_id = 3 
                            WHERE id = pid; 
                        END IF;
                    END IF;
                  
                END LOOP mi_loop; 
                CLOSE cur;
            END |
            DELIMITER ;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('
            DELIMITER |
            DROP EVENT IF EXISTS Evento_estado_cuentas |
            | DELIMITER ;
        ');
    }
}