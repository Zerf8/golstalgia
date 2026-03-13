<?php
require_once __DIR__ . '/config/Database.php';
$db = Database::connect();

$tables = ['usuarios', 'liga_usuarios', 'ligas', 'participantes'];

foreach ($tables as $t) {
    echo "--- TABLE: $t ---\n";
    try {
        $stmt = $db->query("DESCRIBE $t");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "{$row['Field']} ({$row['Type']})\n";
        }
    } catch (Exception $e) {
        echo "Table does not exist or error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
