<?php

class LigaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM ligas ORDER BY temporada DESC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM ligas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function activa(): ?array
    {
        $stmt = $this->db->query("SELECT * FROM ligas WHERE activa = 1 ORDER BY id DESC LIMIT 1");
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO ligas (nombre, temporada, descripcion, activa, fecha_inicio, fecha_fin) VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['nombre'],
            $data['temporada'],
            $data['descripcion'] ?? null,
            $data['activa'] ?? 1,
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE ligas SET nombre=?, temporada=?, descripcion=?, activa=?, fecha_inicio=?, fecha_fin=? WHERE id=?"
        );
        return $stmt->execute([
            $data['nombre'],
            $data['temporada'],
            $data['descripcion'] ?? null,
            $data['activa'],
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null,
            $id,
        ]);
    }

    // Participantes de una liga
    public function getParticipantes(int $ligaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.nombre, p.usuario_id, pl.created_at AS inscrito_at
             FROM participantes_ligas pl
             JOIN participantes p ON p.id = pl.participante_id
             WHERE pl.liga_id = ?
             ORDER BY p.nombre"
        );
        $stmt->execute([$ligaId]);
        return $stmt->fetchAll();
    }

    public function addParticipante(int $ligaId, int $participanteId): bool
    {
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO participantes_ligas (liga_id, participante_id) VALUES (?,?)");
            return $stmt->execute([$ligaId, $participanteId]);
        } catch (PDOException) {
            return false;
        }
    }

    public function removeParticipante(int $ligaId, int $participanteId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM participantes_ligas WHERE liga_id=? AND participante_id=?");
        return $stmt->execute([$ligaId, $participanteId]);
    }

    public function countParticipantes(int $ligaId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM participantes_ligas WHERE liga_id=?");
        $stmt->execute([$ligaId]);
        return (int) $stmt->fetchColumn();
    }
}
