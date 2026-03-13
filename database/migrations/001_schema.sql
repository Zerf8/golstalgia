-- =============================================
-- GOLSTALGIA LIGA TRIVIAL - Database Schema
-- =============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET time_zone = '+00:00';

-- ----------------------------
-- Tabla: usuarios
-- ----------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nivel` ENUM('participante', 'admin') NOT NULL DEFAULT 'participante',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: ligas
-- ----------------------------
CREATE TABLE IF NOT EXISTS `ligas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `temporada` INT UNSIGNED NOT NULL DEFAULT 1,
  `descripcion` TEXT DEFAULT NULL,
  `activa` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_inicio` DATE DEFAULT NULL,
  `fecha_fin` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: liga_usuarios (relación N:N)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `liga_usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `liga_id` INT UNSIGNED NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `inscrito_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_liga_usuario` (`liga_id`, `usuario_id`),
  CONSTRAINT `fk_lu_liga` FOREIGN KEY (`liga_id`) REFERENCES `ligas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lu_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: jornadas
-- ----------------------------
CREATE TABLE IF NOT EXISTS `jornadas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `liga_id` INT UNSIGNED NOT NULL,
  `numero` INT UNSIGNED NOT NULL,
  `fecha_inicio` DATE DEFAULT NULL,
  `fecha_fin` DATE DEFAULT NULL,
  `activa` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_jornada_liga` (`liga_id`, `numero`),
  CONSTRAINT `fk_jornada_liga` FOREIGN KEY (`liga_id`) REFERENCES `ligas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: partidas
-- ----------------------------
CREATE TABLE IF NOT EXISTS `partidas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jornada_id` INT UNSIGNED NOT NULL,
  `local_id` INT UNSIGNED NOT NULL,
  `visitante_id` INT UNSIGNED NOT NULL,
  `estado` ENUM('pendiente', 'acordada', 'jugada', 'cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_acordada` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_partida_jornada` FOREIGN KEY (`jornada_id`) REFERENCES `jornadas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_partida_local` FOREIGN KEY (`local_id`) REFERENCES `usuarios`(`id`),
  CONSTRAINT `fk_partida_visitante` FOREIGN KEY (`visitante_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: disponibilidad (Fase 2)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `disponibilidad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `partida_id` INT UNSIGNED NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `franja_inicio` DATETIME NOT NULL,
  `franja_fin` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_disp_partida` FOREIGN KEY (`partida_id`) REFERENCES `partidas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_disp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: resultados
-- ----------------------------
CREATE TABLE IF NOT EXISTS `resultados` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `partida_id` INT UNSIGNED NOT NULL UNIQUE,
  `ganador_id` INT UNSIGNED DEFAULT NULL,
  `puntos_local` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `puntos_visitante` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `notas` TEXT DEFAULT NULL,
  `registrado_por` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_res_partida` FOREIGN KEY (`partida_id`) REFERENCES `partidas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_res_ganador` FOREIGN KEY (`ganador_id`) REFERENCES `usuarios`(`id`),
  CONSTRAINT `fk_res_registrado` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: clasificacion (calculada/cache)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `clasificacion` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `liga_id` INT UNSIGNED NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `partidas_jugadas` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `victorias` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `derrotas` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `puntos_favor` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `puntos_contra` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `puntos_clasificacion` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_clas_liga_usuario` (`liga_id`, `usuario_id`),
  CONSTRAINT `fk_clas_liga` FOREIGN KEY (`liga_id`) REFERENCES `ligas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_clas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Tabla: estadisticas (Fase 3)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `estadisticas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `partida_id` INT UNSIGNED NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `tema` VARCHAR(100) NOT NULL,
  `preguntas_total` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `preguntas_acertadas` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_est_partida` FOREIGN KEY (`partida_id`) REFERENCES `partidas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_est_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Datos iniciales
-- ----------------------------

-- Admin por defecto (password: Admin1234!)
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `nivel`) VALUES
('Josep (Admin)', 'admin@golstalgia.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TiGc2L8BXfP9p0Nqhd7Q8mYKo4Ea', 'admin');

-- Liga 1 - Temporada 2026
INSERT INTO `ligas` (`nombre`, `temporada`, `descripcion`, `activa`, `fecha_inicio`) VALUES
('Liga Golstalgia', 1, 'Primera temporada de la liga de trivial para patreons de Golstalgia', 1, '2026-03-01');
