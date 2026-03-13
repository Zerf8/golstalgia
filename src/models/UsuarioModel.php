<?php

class UsuarioModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findByGoogleId(string $googleId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE google_id = ? AND activo = 1");
        $stmt->execute([$googleId]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, nombre, email, nivel, avatar, activo, created_at FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT id, nombre, email, nivel, activo, created_at FROM usuarios ORDER BY nombre");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nombre, email, password, nivel, google_id) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nombre'],
            $data['email'],
            isset($data['password']) ? Auth::hashPassword($data['password']) : null,
            $data['nivel'] ?? 'participante',
            $data['google_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach (['nombre', 'email', 'nivel', 'activo'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }

        if (isset($data['password']) && $data['password'] !== '') {
            $fields[] = "password = ?";
            $values[] = Auth::hashPassword($data['password']);
        }

        if (empty($fields)) return false;

        $values[] = $id;
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $excludeId]);
        return (bool) $stmt->fetch();
    }
}
