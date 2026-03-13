<?php
require_once __DIR__ . '/config/app.php';

$userModel = new UsuarioModel();
$partidaModel = new PartidaModel();

// Participantes
$nombres = ['Javi', 'Zerf', 'Ferri', 'Toni', 'Óscar', 'Fabián', 'Gerard', 'Jordi', 'Carles', 'Jorge', 'Juanjo', 'Gustavo'];
$users = [];

foreach ($nombres as $nombre) {
    $u = $userModel->findByEmail(strtolower($nombre) . '@golstalgia.com');
    if ($u) {
        $users[$nombre] = $u['id'];
    } else {
        // Por si acaso no se crearon bien antes
        $id = $userModel->create([
            'nombre' => $nombre,
            'email' => strtolower($nombre) . '@golstalgia.com',
            'password' => 'placeholder',
            'nivel' => 'participante'
        ]);
        $users[$nombre] = $id;
    }
}

// Asegurar que están en la liga 1 (suponiendo liga 1 es la activa)
$db = Database::connect();
foreach ($users as $id) {
    if (!$id) continue;
    $db->prepare("INSERT IGNORE INTO liga_usuarios (liga_id, usuario_id) VALUES (1, ?)")->execute([$id]);
}

// Crear Jornada 1
$stmt = $db->prepare("INSERT IGNORE INTO jornadas (liga_id, numero, activa) VALUES (1, 1, 1)");
$stmt->execute();
$idJornada = $db->query("SELECT id FROM jornadas WHERE liga_id = 1 AND numero = 1")->fetchColumn();

// Resultados (Local, Visitante, Goles L, Goles V)
$resultados = [
    ['Javi',   'Zerf',    0, 1],
    ['Ferri',  'Toni',    1, 0],
    ['Óscar',  'Fabián',  0, 1],
    ['Gerard', 'Jordi',   2, 1],
    ['Carles', 'Jorge',   4, 0],
    ['Juanjo', 'Gustavo', 1, 1]
];

foreach ($resultados as $res) {
    $idLocal = $users[$res[0]];
    $idVis   = $users[$res[1]];
    
    // Crear partida
    $db->prepare("INSERT INTO partidas (jornada_id, local_id, visitante_id, estado) VALUES (?, ?, ?, 'jugada')")
       ->execute([$idJornada, $idLocal, $idVis]);
    
    $idPartida = $db->lastInsertId();
    
    // Calcular ganador
    $ganadorId = null;
    if ($res[2] > $res[3]) $ganadorId = $idLocal;
    elseif ($res[3] > $res[2]) $ganadorId = $idVis;

    // Guardar resultado
    $partidaModel->saveResultado($idPartida, [
        'puntos_local' => $res[2],
        'puntos_visitante' => $res[3],
        'ganador_id' => $ganadorId,
        'registrado_por' => 1 // Admin
    ]);
}

echo "Jornada 1 cargada con éxito.";
?>
