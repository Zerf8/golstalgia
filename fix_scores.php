<?php
require_once __DIR__ . '/config/app.php';

$db = Database::connect();

// 1. Limpiar tabla clasificación
$db->exec("DELETE FROM clasificacion");

// 2. Obtener todas las partidas jugadas
$stmt = $db->query("SELECT id FROM partidas WHERE estado = 'jugada'");
$partidas = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 3. Forzar recalculo usando el modelo (que ya tiene la nueva lógica)
$partidaModel = new PartidaModel();
foreach ($partidas as $id) {
    // El método recalcularClasificacion es privado, pero saveResultado lo llama.
    // Como queremos solo recalcular sin guardar de nuevo el resultado,
    // usaremos Reflection para hacerlo público temporalmente o crearemos un método de ayuda.
    // Opción limpia: llamamos a un nuevo método público que acabo de añadir (o voy a añadir).
    
    // Voy a hacer el recalculo manual aquí mismo para este script de mantenimiento:
    $stmtP = $db->prepare("
        SELECT p.jornada_id, p.local_id, p.visitante_id, j.liga_id,
               r.puntos_local, r.puntos_visitante, r.ganador_id
        FROM partidas p
        JOIN jornadas j ON j.id = p.jornada_id
        JOIN resultados r ON r.partida_id = p.id
        WHERE p.id = ?
    ");
    $stmtP->execute([$id]);
    $row = $stmtP->fetch();
    
    $ligaId = $row['liga_id'];
    foreach (['local_id', 'visitante_id'] as $side) {
        $userId = $row[$side];
        $esLocal = $side === 'local_id';
        $pFavor  = $esLocal ? $row['puntos_local'] : $row['puntos_visitante'];
        $pContra = $esLocal ? $row['puntos_visitante'] : $row['puntos_local'];
        $vic     = ($row['ganador_id'] == $userId) ? 1 : 0;
        $der     = ($row['ganador_id'] != $userId && $row['ganador_id'] !== null) ? 1 : 0;
        $emp     = ($row['ganador_id'] === null) ? 1 : 0;
        $pts     = ($vic * 3) + ($emp * 1);

        $db->prepare("
            INSERT INTO clasificacion (liga_id, usuario_id, partidas_jugadas, victorias, derrotas, puntos_favor, puntos_contra, puntos_clasificacion)
            VALUES (?,?,1,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
               partidas_jugadas = partidas_jugadas + 1,
               victorias = victorias + VALUES(victorias),
               derrotas = derrotas + VALUES(derrotas),
               puntos_favor = puntos_favor + VALUES(puntos_favor),
               puntos_contra = puntos_contra + VALUES(puntos_contra),
               puntos_clasificacion = puntos_clasificacion + VALUES(puntos_clasificacion)
        ")->execute([$ligaId, $userId, $vic, $der, $pFavor, $pContra, $pts]);
    }
}

echo "Clasificación recalculada con éxito.";
?>
