<?php
require_once __DIR__ . '/config/Database.php';
$db = Database::connect();
try {
    $db->exec("ALTER TABLE clasificacion ADD COLUMN empates INT DEFAULT 0 AFTER victorias");
    echo "Columna 'empates' añadida con éxito.\n";
} catch (Exception $e) {
    echo "Error o ya existe: " . $e->getMessage() . "\n";
}
