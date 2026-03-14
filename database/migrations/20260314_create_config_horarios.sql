-- =============================================
-- GOLSTALGIA - Configuración de Horarios
-- =============================================

CREATE TABLE IF NOT EXISTS `config_horarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dia_semana` TINYINT NOT NULL COMMENT '1=LUN, 2=MAR, 3=MIE, 4=JUE, 5=VIE, 6=SAB, 7=DOM',
  `hora_inicio` TIME NOT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_dia_hora` (`dia_semana`, `hora_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Horarios por defecto: Lunes y Jueves de 18:00 a 22:00 (tramos de 30 min)
-- Tramos: 18:00, 18:30, 19:00, 19:30, 20:00, 20:30, 21:00, 21:30
INSERT IGNORE INTO `config_horarios` (`dia_semana`, `hora_inicio`) VALUES
(1, '18:00:00'), (1, '18:30:00'), (1, '19:00:00'), (1, '19:30:00'), (1, '20:00:00'), (1, '20:30:00'), (1, '21:00:00'), (1, '21:30:00'),
(4, '18:00:00'), (4, '18:30:00'), (4, '19:00:00'), (4, '19:30:00'), (4, '20:00:00'), (4, '20:30:00'), (4, '21:00:00'), (4, '21:30:00');
