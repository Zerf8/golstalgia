<?php
require_once __DIR__ . '/config/Database.php';
$db = Database::connect();
try {
    echo "--- ESTRUCTURA TABLA PARTIDAS ---\n";
    $stmt = $db->query("SHOW CREATE TABLE partidas");
    print_r($stmt->fetchColumn());
    
    echo "\n\n--- ESTRUCTURA TABLA PARTICIPANTES ---\n";
    $stmt = $db->query("SHOW CREATE TABLE participantes");
    print_r($stmt->fetchColumn());
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
