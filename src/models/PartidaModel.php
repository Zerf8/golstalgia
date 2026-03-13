<?php

class PartidaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function allByJornada(int $jornadaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    ul.nombre AS nombre_local,
                    uv.nombre AS nombre_visitante,
                    r.puntos_local, r.puntos_visitante, r.ganador_id
             FROM partidas p
             JOIN participantes ul ON ul.id = p.local_id
             JOIN participantes uv ON uv.id = p.visitante_id
             LEFT JOIN resultados r ON r.partida_id = p.id
             WHERE p.jornada_id = ?
             ORDER BY p.id"
        );
        $stmt->execute([$jornadaId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    ul.nombre AS nombre_local,
                    uv.nombre AS nombre_visitante
             FROM partidas p
             JOIN participantes ul ON ul.id = p.local_id
             JOIN participantes uv ON uv.id = p.visitante_id
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO partidas (jornada_id, local_id, visitante_id, estado) VALUES (?,?,?,?)"
        );
        $stmt->execute([
            $data['jornada_id'],
            $data['local_id'],
            $data['visitante_id'],
            $data['estado'] ?? 'pendiente',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateEstado(int $id, string $estado, ?string $fechaAcordada = null): bool
    {
        $stmt = $this->db->prepare("UPDATE partidas SET estado=?, fecha_acordada=? WHERE id=?");
        return $stmt->execute([$estado, $fechaAcordada, $id]);
    }

    public function saveResultado(int $partidaId, array $data): bool
    {
        // Upsert resultado
        $stmt = $this->db->prepare(
            "INSERT INTO resultados (partida_id, ganador_id, puntos_local, puntos_visitante, registrado_por)
             VALUES (?,?,?,?,?)
             ON DUPLICATE KEY UPDATE
               ganador_id=VALUES(ganador_id),
               puntos_local=VALUES(puntos_local),
               puntos_visitante=VALUES(puntos_visitante),
               registrado_por=VALUES(registrado_por)"
        );
        $ok = $stmt->execute([
            $partidaId,
            $data['ganador_id'] ?? null,
            $data['puntos_local'],
            $data['puntos_visitante'],
            $data['registrado_por'],
        ]);

        if ($ok) {
            $this->updateEstado($partidaId, 'jugada');
            $this->recalcularClasificacion($partidaId);
        }

        return $ok;
    }

    private function recalcularClasificacion(int $partidaId): void
    {
        // Get partida and result
        $stmt = $this->db->prepare(
            "SELECT p.jornada_id, p.local_id, p.visitante_id, j.liga_id,
                    r.puntos_local, r.puntos_visitante, r.ganador_id
             FROM partidas p
             JOIN jornadas j ON j.id = p.jornada_id
             JOIN resultados r ON r.partida_id = p.id
             WHERE p.id = ?"
        );
        $stmt->execute([$partidaId]);
        $row = $stmt->fetch();
        if (!$row) return;

        $ligaId = $row['liga_id'];

        foreach (['local_id', 'visitante_id'] as $side) {
            $userId = $row[$side];
            $esLocal = $side === 'local_id';
            $puntosFavor  = $esLocal ? $row['puntos_local']    : $row['puntos_visitante'];
            $puntosContra = $esLocal ? $row['puntos_visitante'] : $row['puntos_local'];
            $victoria     = ($row['ganador_id'] == $userId) ? 1 : 0;
            $derrota      = ($row['ganador_id'] != $userId && $row['ganador_id'] !== null) ? 1 : 0;
            $empate       = ($row['ganador_id'] === null) ? 1 : 0;
            $pts          = ($victoria * 3) + ($empate * 1);

            $stmt2 = $this->db->prepare(
                "INSERT INTO clasificacion (liga_id, participante_id, partidas_jugadas, victorias, derrotas, puntos_favor, puntos_contra, puntos_clasificacion)
                 VALUES (?,?,1,?,?,?,?,?)
                 ON DUPLICATE KEY UPDATE
                   partidas_jugadas = partidas_jugadas + 1,
                   victorias = victorias + VALUES(victorias),
                   derrotas = derrotas + VALUES(derrotas),
                   puntos_favor = puntos_favor + VALUES(puntos_favor),
                   puntos_contra = puntos_contra + VALUES(puntos_contra),
                   puntos_clasificacion = puntos_clasificacion + VALUES(puntos_clasificacion)"
            );
            $stmt2->execute([$ligaId, $userId, $victoria, $derrota, $puntosFavor, $puntosContra, $pts]);
        }
    }

    public function getClasificacion(int $ligaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, p.nombre
             FROM clasificacion c
             JOIN participantes p ON p.id = c.participante_id
             WHERE c.liga_id = ?
             ORDER BY c.puntos_clasificacion DESC, c.puntos_favor DESC, (CAST(c.puntos_favor AS SIGNED) - CAST(c.puntos_contra AS SIGNED)) DESC"
        );
        $stmt->execute([$ligaId]);
        return $stmt->fetchAll();
    }
}
