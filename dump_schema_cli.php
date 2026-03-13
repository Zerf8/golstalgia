<?php
require_once __DIR__ . '/config/Database.php';
$db = Database::connect();

$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $t) {
    echo "[$t]\n";
    $cols = $db->query("SHOW COLUMNS FROM $t")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "- {$c['Field']}\n";
    }
    echo "\n";
}
