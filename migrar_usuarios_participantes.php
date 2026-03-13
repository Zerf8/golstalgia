<?php
/**
 * MIGRACIÓN FINAL: SEPARACIÓN DE USUARIOS Y PARTICIPANTES (v3 - ROBUSTA E IDEMPOTENTE)
 */

require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::connect();
    $db->exec("SET NAMES utf8mb4");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
} catch (Exception $e) {
    die("Error conectando a la base de datos: " . $e->getMessage() . "\n");
}

try {
    echo "1. Estructura de tablas (DDL)...\n";
    
    // Tabla de Participantes
    $db->exec("CREATE TABLE IF NOT EXISTS participantes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        usuario_id INT UNSIGNED NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Tabla pivote Participantes - Ligas
    $db->exec("CREATE TABLE IF NOT EXISTS participantes_ligas (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        participante_id INT UNSIGNED NOT NULL,
        liga_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE,
        FOREIGN KEY (liga_id) REFERENCES ligas(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Ajuste en Clasificacion (si aun no se ha hecho)
    $colsClas = $db->query("SHOW COLUMNS FROM clasificacion")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('usuario_id', $colsClas)) {
        $db->exec("ALTER TABLE clasificacion CHANGE usuario_id participante_id INT UNSIGNED NOT NULL");
        echo "- Columna usuario_id cambiada a participante_id en clasificacion.\n";
    }

    echo "2. Migración de Datos (DML)...\n";
    
    // Obtener participantes actuales si ya se crearon en el intento fallido
    $participantesExistentes = $db->query("SELECT id, nombre FROM participantes")->fetchAll(PDO::FETCH_ASSOC);
    $mapeoNombreToNewId = [];
    foreach ($participantesExistentes as $p) {
        $mapeoNombreToNewId[strtolower($p['nombre'])] = $p['id'];
    }

    // Obtener usuarios "jugadores" que aún queden
    $usuariosJugadores = $db->query("SELECT id, nombre FROM usuarios WHERE nivel = 'participante'")->fetchAll(PDO::FETCH_ASSOC);
    $mapeoOldIdToNewId = [];

    foreach ($usuariosJugadores as $u) {
        $normNombre = strtolower($u['nombre']);
        if (!isset($mapeoNombreToNewId[$normNombre])) {
            $stmt = $db->prepare("INSERT INTO participantes (nombre) VALUES (?)");
            $stmt->execute([$u['nombre']]);
            $newId = $db->lastInsertId();
            $mapeoNombreToNewId[$normNombre] = $newId;
            $mapeoOldIdToNewId[$u['id']] = $newId;
            echo "- Creado participante: {$u['nombre']} (ID: $newId)\n";
        } else {
            $mapeoOldIdToNewId[$u['id']] = $mapeoNombreToNewId[$normNombre];
        }
    }

    echo "3. Sincronización de Ligas y Partidas...\n";
    $ligaId = $db->query("SELECT id FROM ligas WHERE activa = 1 LIMIT 1")->fetchColumn();
    
    if ($ligaId) {
        foreach ($mapeoNombreToNewId as $pId) {
            $exists = $db->query("SELECT 1 FROM participantes_ligas WHERE participante_id = $pId AND liga_id = $ligaId")->fetchColumn();
            if (!$exists) {
                $db->prepare("INSERT INTO participantes_ligas (participante_id, liga_id) VALUES (?, ?)")->execute([$pId, $ligaId]);
            }
        }
    }

    // Actualizar partidas con los nuevos IDs de participante
    // (Este paso es vital y debe hacerse con cuidado si ya se hizo a medias)
    // Buscamos si hay IDs de partidas que NO coincidan con los IDs de participantes (los de usuarios suelen ser bajos 1-20)
    // Pero como borramos la tabla usuarios al final, si no lo hacemos ahora perderemos la referencia.
    
    $partidas = $db->query("SELECT id, local_id, visitante_id FROM partidas")->fetchAll(PDO::FETCH_ASSOC);
    $stmtUpdPartida = $db->prepare("UPDATE partidas SET local_id = ?, visitante_id = ? WHERE id = ?");
    
    foreach ($partidas as $p) {
        $newLocal = $mapeoOldIdToNewId[$p['local_id']] ?? null;
        $newVisitante = $mapeoOldIdToNewId[$p['visitante_id']] ?? null;
        if ($newLocal && $newVisitante) {
            $stmtUpdPartida->execute([$newLocal, $newVisitante, $p['id']]);
        }
    }

    echo "4. Reseteo de Usuarios y Carga de Administradores...\n";
    
    // Limpieza de usuarios (EXCEPTO los nuevos admins si ya se crearon)
    $db->exec("DELETE FROM usuarios WHERE email NOT IN ('josep@golstalgia.com', 'sagra@golstalgia.com', 'zerf@golstalgia.com')");
    
    $admins = [
        ['Josep', 'josep@golstalgia.com'],
        ['Sagra', 'sagra@golstalgia.com'],
        ['Zerf', 'zerf@golstalgia.com']
    ];

    $passHash = password_hash('G0lstalgiaAdmin', PASSWORD_DEFAULT);
    foreach ($admins as $adm) {
        $exists = $db->query("SELECT 1 FROM usuarios WHERE email = '{$adm[1]}'")->fetchColumn();
        if (!$exists) {
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password, activo, nivel) VALUES (?, ?, ?, 1, 'admin')");
            $stmt->execute([$adm[0], $adm[1], $passHash]);
            echo "- Admin creado: {$adm[0]}\n";
        }
    }

    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "¡MIGRACIÓN FINALIZADA SIN ERRORES!\n";

} catch (Exception $e) {
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    die("ERROR EN MIGRACIÓN: " . $e->getMessage() . "\n");
}
