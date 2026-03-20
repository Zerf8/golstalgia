<?php

class PartidaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(?int $ligaId = null): array
    {
        $where = $ligaId ? "WHERE j.liga_id = ?" : "";
        $params = $ligaId ? [$ligaId] : [];
        
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    ul.nombre AS nombre_local,
                    uv.nombre AS nombre_visitante,
                    r.puntos_local, r.puntos_visitante, r.ganador_id,
                    j.numero AS jornada_numero,
                    l.nombre AS liga_nombre,
                    l.id AS liga_id
             FROM partidas p
             JOIN jornadas j ON j.id = p.jornada_id
             JOIN ligas l ON l.id = j.liga_id
             JOIN participantes ul ON ul.id = p.local_id
             JOIN participantes uv ON uv.id = p.visitante_id
             LEFT JOIN resultados r ON r.partida_id = p.id
             $where
             ORDER BY l.id DESC, j.numero DESC, p.id DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
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
                    uv.nombre AS nombre_visitante,
                    j.liga_id,
                    r.puntos_local, r.puntos_visitante
             FROM partidas p
             JOIN participantes ul ON ul.id = p.local_id
             JOIN participantes uv ON uv.id = p.visitante_id
             JOIN jornadas j ON j.id = p.jornada_id
             LEFT JOIN resultados r ON r.partida_id = p.id
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

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE partidas SET estado=?, fecha_acordada=? WHERE id=?");
        return $stmt->execute([
            $data['estado'],
            $data['fecha_acordada'],
            $id
        ]);
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
            
            // Get liga_id to rebuild classification
            $stmt = $this->db->prepare("SELECT j.liga_id FROM partidas p JOIN jornadas j ON j.id = p.jornada_id WHERE p.id = ?");
            $stmt->execute([$partidaId]);
            $ligaId = $stmt->fetchColumn();
            $this->rebuildClasificacion((int)$ligaId);
        }

        return $ok;
    }

    public function rebuildClasificacion(int $ligaId): void
    {
        // Limpiar clasificación actual de la liga
        $this->db->prepare("DELETE FROM clasificacion WHERE liga_id = ?")->execute([$ligaId]);
        
        // Obtener todos los resultados de la liga
        $stmt = $this->db->prepare(
            "SELECT p.local_id, p.visitante_id, r.puntos_local, r.puntos_visitante, r.ganador_id
             FROM partidas p
             JOIN resultados r ON r.partida_id = p.id
             JOIN jornadas j ON j.id = p.jornada_id
             WHERE j.liga_id = ? AND p.estado = 'jugada'"
        );
        $stmt->execute([$ligaId]);
        $partidas = $stmt->fetchAll();
        
        $stats = [];
        
        foreach ($partidas as $p) {
            foreach (['local', 'visitante'] as $side) {
                $id = ($side === 'local') ? $p['local_id'] : $p['visitante_id'];
                if (!isset($stats[$id])) {
                    $stats[$id] = [
                        'jugadas' => 0, 'victorias' => 0, 'derrotas' => 0, 
                        'favor' => 0, 'contra' => 0, 'puntos' => 0
                    ];
                }
                
                $stats[$id]['jugadas']++;
                $p_favor  = ($side === 'local') ? $p['puntos_local'] : $p['puntos_visitante'];
                $p_contra = ($side === 'local') ? $p['puntos_visitante'] : $p['puntos_local'];
                $stats[$id]['favor']  += $p_favor;
                $stats[$id]['contra'] += $p_contra;
                
                if ($p['ganador_id'] == $id) {
                    $stats[$id]['victorias']++;
                    $stats[$id]['puntos'] += 3;
                } elseif ($p['ganador_id'] !== null) {
                    $stats[$id]['derrotas']++;
                } else {
                    // Empate
                    $stats[$id]['puntos'] += 1;
                }
            }
        }
        
        // Insertar estadísticas
        $stmtInsert = $this->db->prepare(
            "INSERT INTO clasificacion (liga_id, usuario_id, partidas_jugadas, victorias, derrotas, puntos_favor, puntos_contra, puntos_clasificacion)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        foreach ($stats as $userId => $s) {
            $stmtInsert->execute([
                $ligaId, $userId, $s['jugadas'], $s['victorias'], $s['derrotas'], 
                $s['favor'], $s['contra'], $s['puntos']
            ]);
        }
    }

    public function getClasificacion(int $ligaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, p.nombre
             FROM clasificacion c
             JOIN participantes p ON p.id = c.usuario_id
             WHERE c.liga_id = ?
             ORDER BY c.puntos_clasificacion DESC, (CAST(c.puntos_favor AS SIGNED) - CAST(c.puntos_contra AS SIGNED)) DESC, c.puntos_favor DESC"
        );
        $stmt->execute([$ligaId]);
        return $stmt->fetchAll();
    }

    public function getDisponibilidad(int $partidaId, ?int $usuarioId = null): array
    {
        if ($usuarioId) {
            $stmt = $this->db->prepare("SELECT * FROM disponibilidad WHERE partida_id = ? AND usuario_id = ?");
            $stmt->execute([$partidaId, $usuarioId]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM disponibilidad WHERE partida_id = ?");
            $stmt->execute([$partidaId]);
        }
        return $stmt->fetchAll();
    }

    public function setDisponibilidad(int $partidaId, int $usuarioId, array $slots): bool
    {
        $this->db->beginTransaction();
        try {
            // Borrar disponibilidad previa del usuario para esta partida
            $stmt = $this->db->prepare("DELETE FROM disponibilidad WHERE partida_id = ? AND usuario_id = ?");
            $stmt->execute([$partidaId, $usuarioId]);

            // Insertar nuevos slots
            $stmt = $this->db->prepare("INSERT INTO disponibilidad (partida_id, usuario_id, franja_inicio, franja_fin) VALUES (?, ?, ?, ?)");
            foreach ($slots as $slot) {
                // Asumimos que $slot es 'YYYY-MM-DD HH:MM:SS'
                $inicio = $slot;
                $fin = date('Y-m-d H:i:s', strtotime($slot . ' +30 minutes'));
                $stmt->execute([$partidaId, $usuarioId, $inicio, $fin]);
            }

            $this->db->commit();
            
            // Comprobar si hay acuerdo
            $this->checkAgreement($partidaId);
            
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function checkAgreement(int $partidaId): void
    {
        // Buscar franjas donde coincidan ambos jugadores
        $stmt = $this->db->prepare(
            "SELECT d1.franja_inicio
             FROM disponibilidad d1
             JOIN disponibilidad d2 ON d1.partida_id = d2.partida_id 
                AND d1.franja_inicio = d2.franja_inicio
                AND d1.usuario_id != d2.usuario_id
             JOIN partidas p ON p.id = d1.partida_id
             WHERE d1.partida_id = ?
             LIMIT 1"
        );
        $stmt->execute([$partidaId]);
        $match = $stmt->fetch();

        if ($match) {
            $this->updateEstado($partidaId, 'acordada', $match['franja_inicio']);
        }
    }

    public function getOccupiedSlots(string $fecha): array
    {
        // Retorna las franjas horarias que ya tienen un partido acordado para un día concreto
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(fecha_acordada, '%H:%i:%s') as hora
             FROM partidas 
             WHERE DATE(fecha_acordada) = ? AND estado = 'acordada'"
        );
        $stmt->execute([$fecha]);
        return array_column($stmt->fetchAll(), 'hora');
    }

    public function setFechaAcordada(int $partidaId, string $dateTime): bool
    {
        return $this->updateEstado($partidaId, 'acordada', $dateTime);
    }

    public function cancelarAcuerdo(int $partidaId): bool
    {
        return $this->updateEstado($partidaId, 'pendiente', null);
    }
}
