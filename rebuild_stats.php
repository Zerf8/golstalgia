<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/Database.php';

// Mock minimal environment if needed, but here we just need DB
$ligaModel = new LigaModel();
$liga = $ligaModel->activa();

if ($liga) {
    echo "Reconstruyendo clasificación para la liga: " . $liga['nombre'] . " (ID: " . $liga['id'] . ")\n";
    $db = Database::connect();
    var_dump(get_class($db));
    var_dump(method_exists($db, 'prepare'));
    
    $stmt = $db->prepare("SELECT id FROM participantes WHERE liga_id = ?");
    $stmt->execute([$liga['id']]);
    echo "¡Consulta exitosa!\n";
} else {
    echo "No hay ninguna liga activa.\n";
}
