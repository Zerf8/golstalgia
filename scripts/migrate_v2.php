<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/Database.php';

Auth::requireAdmin();

$db = Database::connect();

$queries = [
    "CREATE TABLE IF NOT EXISTS resultados (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `partida_id` INT UNSIGNED NOT NULL UNIQUE,
      `ganador_id` INT UNSIGNED DEFAULT NULL,
      `puntos_local` TINYINT UNSIGNED NOT NULL DEFAULT 0,
      `puntos_visitante` TINYINT UNSIGNED NOT NULL DEFAULT 0,
      `notas` TEXT DEFAULT NULL,
      `registrado_por` INT UNSIGNED DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "CREATE TABLE IF NOT EXISTS clasificacion (
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
      UNIQUE KEY `uk_clas_liga_usuario` (`liga_id`, `usuario_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

echo "<h1>Ejecutando migración V2 (Resultados y Clasificación)...</h1>";

foreach ($queries as $sql) {
    try {
        $db->exec($sql);
        echo "<p style='color:green;'>✅ Consulta ejecutada correctamente.</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

echo "<hr><p>Proceso finalizado. Ya puedes borrar este archivo o volver al panel admin.</p>";
