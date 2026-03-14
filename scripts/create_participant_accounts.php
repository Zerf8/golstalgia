<?php
// Configuración manual para coincidir con el contenedor Docker
$host = 'golstalgia_db'; // El nombre del servicio en docker-compose.yml
$user = 'root';
$pass = 'root_secret';
$dbName = 'golstalgia';

try {
    $pdo = new PDO("mysql:host=db;dbname=$dbName;charset=utf8mb4", 'root', 'root_secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener participantes sin usuario vinculado
    $stmt = $pdo->query("SELECT id, nombre FROM participantes WHERE usuario_id IS NULL");
    $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($participantes) . " participants to process.\n";

    foreach ($participantes as $p) {
        $nombreOriginal = $p['nombre'];
        
        // Normalización básica para el email
        // Convertir caracteres especiales a ASCII (aproximación para email)
        $cleanName = strtolower($nombreOriginal);
        $cleanName = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], ['a', 'e', 'i', 'o', 'u', 'n', 'u'], $cleanName);
        $cleanName = preg_replace('/[^a-z0-9]/', '', $cleanName);
        
        $email = $cleanName . "@golstalgia.com";
        $passwordHash = password_hash('123456', PASSWORD_BCRYPT);
        
        echo "Creating user for $nombreOriginal ($email)... ";

        try {
            // Insertar usuario
            $stmtUser = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, nivel, activo, created_at, updated_at) VALUES (?, ?, ?, 'participante', 1, NOW(), NOW())");
            $stmtUser->execute([$nombreOriginal, $email, $passwordHash]);
            $userId = $pdo->lastInsertId();

            // Vincular participante
            $stmtLink = $pdo->prepare("UPDATE participantes SET usuario_id = ? WHERE id = ?");
            $stmtLink->execute([$userId, $p['id']]);

            echo "OK (ID: $userId)\n";
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    echo "\nAll done!\n";

} catch (Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage() . "\n";
}
