-- Script de corrección local: remap desde 1001-1011 → 1-11

ALTER TABLE partidas DROP FOREIGN KEY fk_partida_jornada;

UPDATE jornadas SET id = 1  WHERE id = 1001;
UPDATE jornadas SET id = 2  WHERE id = 1002;
UPDATE jornadas SET id = 3  WHERE id = 1003;
UPDATE jornadas SET id = 4  WHERE id = 1004;
UPDATE jornadas SET id = 5  WHERE id = 1005;
UPDATE jornadas SET id = 6  WHERE id = 1006;
UPDATE jornadas SET id = 7  WHERE id = 1007;
UPDATE jornadas SET id = 8  WHERE id = 1008;
UPDATE jornadas SET id = 9  WHERE id = 1009;
UPDATE jornadas SET id = 10 WHERE id = 1010;
UPDATE jornadas SET id = 11 WHERE id = 1011;

UPDATE partidas SET jornada_id = 1  WHERE jornada_id = 1001;
UPDATE partidas SET jornada_id = 2  WHERE jornada_id = 1002;
UPDATE partidas SET jornada_id = 3  WHERE jornada_id = 1003;
UPDATE partidas SET jornada_id = 4  WHERE jornada_id = 1004;
UPDATE partidas SET jornada_id = 5  WHERE jornada_id = 1005;
UPDATE partidas SET jornada_id = 6  WHERE jornada_id = 1006;
UPDATE partidas SET jornada_id = 7  WHERE jornada_id = 1007;
UPDATE partidas SET jornada_id = 8  WHERE jornada_id = 1008;
UPDATE partidas SET jornada_id = 9  WHERE jornada_id = 1009;
UPDATE partidas SET jornada_id = 10 WHERE jornada_id = 1010;
UPDATE partidas SET jornada_id = 11 WHERE jornada_id = 1011;

ALTER TABLE partidas ADD CONSTRAINT fk_partida_jornada 
  FOREIGN KEY (jornada_id) REFERENCES jornadas(id) ON DELETE CASCADE;

ALTER TABLE jornadas AUTO_INCREMENT = 12;

SELECT id, numero FROM jornadas ORDER BY id;
SELECT MIN(jornada_id), MAX(jornada_id) FROM partidas;
