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
            ON SCHEDULE EVERY 720 MINUTE STARTS CURRENT_TIMESTAMP 
            DO
            BEGIN
                DECLARE fin INT DEFAULT 0;
                DECLARE pid INT;
                DECLARE dias INT;
                DECLARE state INT;
                DECLARE created timestamp;  
                DECLARE cur CURSOR FOR 
                    SELECT id, estado, created_at 
                    FROM `estado_cuenta`;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin = 1;

                OPEN cur;
                mi_loop: LOOP
 
                    FETCH cur INTO pid, state, created;
                    SELECT DATEDIFF(now(), created ) INTO dias;
 
                    IF fin = 1 THEN 
                        LEAVE mi_loop;
                    END IF;
                    
                    IF dias = 15 THEN
                        IF state = 0 THEN
                            UPDATE `estado_cuenta` 
                            SET estado = 2 
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