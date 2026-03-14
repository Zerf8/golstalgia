-- Migration to add jornada_id to config_horarios
ALTER TABLE config_horarios ADD COLUMN jornada_id INT UNSIGNED NULL AFTER id;
ALTER TABLE config_horarios ADD CONSTRAINT fk_horario_jornada FOREIGN KEY (jornada_id) REFERENCES jornadas(id) ON DELETE CASCADE;
-- Add unique constraint to prevent duplicates within the same jornada (or global)
ALTER TABLE config_horarios ADD UNIQUE INDEX uq_horario_jornada (dia_semana, hora_inicio, jornada_id);
