-- ============================================================
-- Script: Renumerar IDs de jornadas (36-46 → 1-11)
-- Versión phpMyAdmin-compatible (elimina FK temporalmente)
-- ============================================================

-- Paso 1: Eliminar la FK de partidas
ALTER TABLE partidas DROP FOREIGN KEY fk_partida_jornada;

-- Paso 2: Dar un desplazamiento temporal a las jornadas (evita conflictos de PK)
UPDATE jornadas SET id = id + 1000;
UPDATE partidas SET jornada_id = jornada_id + 1000;

-- Paso 3: Renumerar jornadas al rango 1-11
UPDATE jornadas SET id = 1  WHERE id = 1036;
UPDATE jornadas SET id = 2  WHERE id = 1037;
UPDATE jornadas SET id = 3  WHERE id = 1038;
UPDATE jornadas SET id = 4  WHERE id = 1039;
UPDATE jornadas SET id = 5  WHERE id = 1040;
UPDATE jornadas SET id = 6  WHERE id = 1041;
UPDATE jornadas SET id = 7  WHERE id = 1042;
UPDATE jornadas SET id = 8  WHERE id = 1043;
UPDATE jornadas SET id = 9  WHERE id = 1044;
UPDATE jornadas SET id = 10 WHERE id = 1045;
UPDATE jornadas SET id = 11 WHERE id = 1046;

-- Paso 4: Actualizar partidas al rango 1-11
UPDATE partidas SET jornada_id = 1  WHERE jornada_id = 1036;
UPDATE partidas SET jornada_id = 2  WHERE jornada_id = 1037;
UPDATE partidas SET jornada_id = 3  WHERE jornada_id = 1038;
UPDATE partidas SET jornada_id = 4  WHERE jornada_id = 1039;
UPDATE partidas SET jornada_id = 5  WHERE jornada_id = 1040;
UPDATE partidas SET jornada_id = 6  WHERE jornada_id = 1041;
UPDATE partidas SET jornada_id = 7  WHERE jornada_id = 1042;
UPDATE partidas SET jornada_id = 8  WHERE jornada_id = 1043;
UPDATE partidas SET jornada_id = 9  WHERE jornada_id = 1044;
UPDATE partidas SET jornada_id = 10 WHERE jornada_id = 1045;
UPDATE partidas SET jornada_id = 11 WHERE jornada_id = 1046;

-- Paso 5: Restaurar la FK
ALTER TABLE partidas ADD CONSTRAINT fk_partida_jornada 
  FOREIGN KEY (jornada_id) REFERENCES jornadas(id) ON DELETE CASCADE;

-- Paso 6: Resetear AUTO_INCREMENT
ALTER TABLE jornadas AUTO_INCREMENT = 12;

-- Verificación
SELECT id, numero FROM jornadas ORDER BY id;
SELECT MIN(jornada_id), MAX(jornada_id) FROM partidas;
