<?php

class ParticipanteModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM participantes ORDER BY nombre");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM participantes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO participantes (nombre, usuario_id) VALUES (?, ?)");
        $stmt->execute([
            $data['nombre'],
            $data['usuario_id'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE participantes SET nombre = ?, usuario_id = ? WHERE id = ?");
        return $stmt->execute([
            $data['nombre'],
            $data['usuario_id'] ?? null,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM participantes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM participantes WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        return $stmt->fetch() ?: null;
    }
}
