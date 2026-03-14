<?php

class HorarioModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(?int $jornadaId = null): array
    {
        // 1. Fetch Global Slots
        $stmt = $this->db->prepare("SELECT *, 0 as is_specific FROM config_horarios WHERE jornada_id IS NULL ORDER BY dia_semana, hora_inicio");
        $stmt->execute();
        $global = $stmt->fetchAll();

        if (!$jornadaId) return $global;

        // 2. Fetch Specific Slots for this Jornada
        $stmt = $this->db->prepare("SELECT *, 1 as is_specific FROM config_horarios WHERE jornada_id = ? ORDER BY dia_semana, hora_inicio");
        $stmt->execute([$jornadaId]);
        $specific = $stmt->fetchAll();

        // 3. Merge: Specific overrides Global (same día/hora)
        $merged = [];
        foreach ($global as $g) {
            $key = $g['dia_semana'] . '_' . $g['hora_inicio'];
            $merged[$key] = $g;
        }
        foreach ($specific as $s) {
            $key = $s['dia_semana'] . '_' . $s['hora_inicio'];
            $merged[$key] = $s;
        }

        // Sort by day and time
        usort($merged, function($a, $b) {
            if ($a['dia_semana'] !== $b['dia_semana']) return $a['dia_semana'] <=> $b['dia_semana'];
            return strcmp($a['hora_inicio'], $b['hora_inicio']);
        });

        return $merged;
    }

    public function activeSlots(?int $jornadaId = null): array
    {
        $all = $this->all($jornadaId);
        return array_values(array_filter($all, fn($slot) => $slot['activo'] == 1));
    }

    public function toggle(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE config_horarios SET activo = 1 - activo WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function create(int $dia, string $hora, ?int $jornadaId = null): bool
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO config_horarios (dia_semana, hora_inicio, jornada_id) VALUES (?, ?, ?)");
        return $stmt->execute([$dia, $hora, $jornadaId]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM config_horarios WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
