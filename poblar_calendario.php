<?php
require_once 'config/app.php';

$db = Database::connect();

$mapping = [
    'Javi'    => 3,
    'Zerf'    => 4,
    'Ferri'   => 5,
    'Toni'    => 6,
    'Óscar'   => 7,
    'Fabián'  => 8,
    'Gerard'  => 9,
    'Jordi'   => 10,
    'Carles'  => 11,
    'Jorge'   => 12,
    'Juanjo'  => 13,
    'Gustavo' => 14
];

$calendarioRaw = [
    2 => [['Carles','Fabián'], ['Zerf','Ferri'], ['Gustavo','Javi'], ['Jordi','Juanjo'], ['Óscar','Gerard'], ['Toni','Jorge']],
    3 => [['Gerard','Fabián'], ['Ferri','Gustavo'], ['Jorge','Zerf'], ['Juanjo','Óscar'], ['Javi','Jordi'], ['Toni','Carles']],
    4 => [['Carles','Gerard'], ['Fabián','Juanjo'], ['Zerf','Toni'], ['Gustavo','Jorge'], ['Jordi','Ferri'], ['Óscar','Javi']],
    5 => [['Zerf','Carles'], ['Ferri','Óscar'], ['Jorge','Jordi'], ['Juanjo','Gerard'], ['Javi','Fabián'], ['Toni','Gustavo']],
    6 => [['Carles','Juanjo'], ['Fabián','Ferri'], ['Gerard','Javi'], ['Gustavo','Zerf'], ['Jordi','Toni'], ['Óscar','Jorge']],
    7 => [['Zerf','Jordi'], ['Ferri','Gerard'], ['Gustavo','Carles'], ['Jorge','Fabián'], ['Javi','Juanjo'], ['Toni','Óscar']],
    8 => [['Carles','Javi'], ['Fabián','Toni'], ['Gerard','Jorge'], ['Jordi','Gustavo'], ['Juanjo','Ferri'], ['Óscar','Zerf']],
    9 => [['Zerf','Fabián'], ['Ferri','Javi'], ['Gustavo','Óscar'], ['Jordi','Carles'], ['Jorge','Juanjo'], ['Toni','Gerard']],
    10 => [['Fabián','Gustavo'], ['Gerard','Zerf'], ['Ferri','Carles'], ['Juanjo','Toni'], ['Óscar','Jordi'], ['Javi','Jorge']],
    11 => [['Carles','Óscar'], ['Zerf','Juanjo'], ['Gustavo','Gerard'], ['Jordi','Fabián'], ['Jorge','Ferri'], ['Toni','Javi']]
];

foreach ($calendarioRaw as $num => $partidas) {
    // Crear jornada
    $stmtJ = $db->prepare("INSERT IGNORE INTO jornadas (liga_id, numero) VALUES (1, ?)");
    $stmtJ->execute([$num]);
    $idJornada = $db->query("SELECT id FROM jornadas WHERE liga_id = 1 AND numero = $num")->fetchColumn();
    
    foreach ($partidas as $p) {
        $idL = $mapping[$p[0]];
        $idV = $mapping[$p[1]];
        
        $db->prepare("INSERT IGNORE INTO partidas (jornada_id, local_id, visitante_id, estado) VALUES (?, ?, ?, 'pendiente')")
           ->execute([$idJornada, $idL, $idV]);
    }
}

echo "Calendario oficial (Jornadas 2-11) cargado con éxito.";
?>
