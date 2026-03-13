<?php

class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();
        $user = Auth::user();

        $ligaModel         = new LigaModel();
        $partidaModel      = new PartidaModel();
        $participanteModel = new ParticipanteModel();

        $liga            = $ligaModel->activa();
        $clasificacion   = $liga ? $partidaModel->getClasificacion($liga['id']) : [];
        $participantesCount = $liga ? $ligaModel->countParticipantes($liga['id']) : 0;
        $jornadas        = $liga ? (new JornadaModel())->allByLiga($liga['id']) : [];

        // Datos personalizados si el usuario es un participante
        $miParticipante = $participanteModel->findByUsuarioId($user['id']);
        $misPartidas    = [];
        if ($miParticipante) {
            // Buscamos partidas donde sea local o visitante
            $stmt = Database::connect()->prepare(
                "SELECT p.*, 
                        ul.nombre AS nombre_local, 
                        uv.nombre AS nombre_visitante,
                        r.puntos_local, r.puntos_visitante,
                        j.numero AS jornada_numero
                 FROM partidas p
                 JOIN participantes ul ON ul.id = p.local_id
                 JOIN participantes uv ON uv.id = p.visitante_id
                 JOIN jornadas j ON j.id = p.jornada_id
                 LEFT JOIN resultados r ON r.partida_id = p.id
                 WHERE (p.local_id = ? OR p.visitante_id = ?) 
                   AND j.liga_id = ?
                 ORDER BY j.numero ASC"
            );
            $stmt->execute([$miParticipante['id'], $miParticipante['id'], $liga['id']]);
            $misPartidas = $stmt->fetchAll();
        }

        require_once __DIR__ . '/../views/dashboard.php';
    }
}
