DELIMITER //
CREATE PROCEDURE cambia_estado()
 BEGIN
  DECLARE fin INT DEFAULT 0;
    DECLARE pid INT;
    DECLARE dias INT;
    DECLARE state INT;
    DECLARE created timestamp;  
    DECLARE cur CURSOR FOR 
        SELECT id, estado_cuenta_id, created_at 
        FROM `propiedades` 
        WHERE estado_cuenta_id=1;
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
 END //
DELIMITER ;

DELIMITER |
CREATE EVENT Evento
ON SCHEDULE EVERY 1 MINUTE STARTS CURRENT_TIMESTAMP 
DO
BEGIN
    CALL `cambia_estado`();
END |
DELIMITER ;