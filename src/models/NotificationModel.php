<?php

class NotificationModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Crea una nueva notificación.
     */
    public function create(int $usuarioId, string $tipo, string $mensaje, ?int $partidaId = null): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO notificaciones (usuario_id, tipo, mensaje, partida_id) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$usuarioId, $tipo, $mensaje, $partidaId]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Obtiene las notificaciones de un usuario.
     */
    public function getByUser(int $usuarioId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT n.*, j.numero as jornada_numero 
             FROM notificaciones n
             LEFT JOIN partidas p ON p.id = n.partida_id
             LEFT JOIN jornadas j ON j.id = p.jornada_id
             WHERE n.usuario_id = ? 
             ORDER BY n.created_at DESC 
             LIMIT ?"
        );
        $stmt->execute([$usuarioId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene el número de notificaciones no leídas.
     */
    public function getUnreadCount(int $usuarioId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0");
        $stmt->execute([$usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Marca una notificación como leída.
     */
    public function markAsRead(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE notificaciones SET leida = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Marca todas las notificaciones de un usuario como leídas.
     */
    public function markAllAsRead(int $usuarioId): bool
    {
        $stmt = $this->db->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = ?");
        return $stmt->execute([$usuarioId]);
    }
}
