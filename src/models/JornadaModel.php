<?php

class JornadaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function allByLiga(int $ligaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT j.*, COUNT(p.id) as total_partidas 
             FROM jornadas j
             LEFT JOIN partidas p ON p.jornada_id = j.id
             WHERE j.liga_id = ?
             GROUP BY j.id
             ORDER BY j.numero"
        );
        $stmt->execute([$ligaId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM jornadas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO jornadas (liga_id, numero, fecha_inicio, fecha_fin, activa) VALUES (?,?,?,?,?)"
        );
        $stmt->execute([
            $data['liga_id'],
            $data['numero'],
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null,
            $data['activa'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE jornadas SET numero=?, fecha_inicio=?, fecha_fin=?, activa=? WHERE id=?"
        );
        return $stmt->execute([
            $data['numero'],
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null,
            $data['activa'],
            $id,
        ]);
    }

    public function nextNumero(int $ligaId): int
    {
        $stmt = $this->db->prepare("SELECT MAX(numero) FROM jornadas WHERE liga_id=?");
        $stmt->execute([$ligaId]);
        return ((int) $stmt->fetchColumn()) + 1;
    }

    public function findFeatured(int $ligaId): ?array
    {
        // Buscar jornada marcada con activa = 2
        $stmt = $this->db->prepare("SELECT * FROM jornadas WHERE liga_id = ? AND activa = 2 LIMIT 1");
        $stmt->execute([$ligaId]);
        return $stmt->fetch() ?: null;
    }

    public function findUltimaDisputada(int $ligaId): ?array
    {
        // 1. Prioridad: Si hay una marcada como activa = 2, ESA es la que mostramos
        $featured = $this->findFeatured($ligaId);
        if ($featured) return $featured;

        // 2. Fallback: Jornada con el número más alto que tenga al menos una partida jugada
        $stmt = $this->db->prepare(
            "SELECT j.* FROM jornadas j
             JOIN partidas p ON p.jornada_id = j.id
             WHERE j.liga_id = ? AND p.estado = 'jugada'
             ORDER BY j.numero DESC LIMIT 1"
        );
        $stmt->execute([$ligaId]);
        return $stmt->fetch() ?: null;
    }

    public function findSiguiente(int $ligaId): ?array
    {
        // La primera jornada que no tenga partidas jugadas y cuyo número sea mayor a la última disputada
        $ultima = $this->findUltimaDisputada($ligaId);
        $numUltima = $ultima ? $ultima['numero'] : 0;
        
        $stmt = $this->db->prepare(
            "SELECT * FROM jornadas 
             WHERE liga_id = ? AND numero > ? 
             ORDER BY numero ASC LIMIT 1"
        );
        $stmt->execute([$ligaId, $numUltima]);
        return $stmt->fetch() ?: null;
    }
}
