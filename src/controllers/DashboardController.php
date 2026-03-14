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
                        j.numero AS jornada_numero,
                        j.fecha_inicio
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

            // Enriquecemos las partidas pendientes con disponibilidad y slots ocupados
            $horarioModel = new HorarioModel();
            
            foreach ($misPartidas as &$p) {
                if ($p['estado'] === 'pendiente' || $p['estado'] === 'acordada') {
                    // Calculate dates for this specific jornada
                    // IF APLAZADA: Use THIS WEEK as base so they can schedule now
                    if ($p['estado'] === 'aplazada') {
                        $fechaBase = date('Y-m-d', strtotime('monday this week'));
                    } else {
                        // Fallback: Monday of this week + (jornada_numero - 1) weeks (or fecha_inicio if set)
                        $fechaBase = !empty($p['fecha_inicio']) ? $p['fecha_inicio'] : date('Y-m-d', strtotime('monday this week + ' . ($p['jornada_numero'] - 1) . ' weeks'));
                    }
                    
                    // Closure to normalize stored datetimes (which might be from old calculations) to THIS jornada's week
                    $normalize = function($datetimes) use ($fechaBase) {
                        return array_map(function($dt) use ($fechaBase) {
                            if (!$dt) return null;
                            $time = date('H:i:s', strtotime($dt));
                            $w = date('N', strtotime($dt)); // 1 (Mon) to 7 (Sun)
                            return date('Y-m-d', strtotime("$fechaBase + " . ($w - 1) . " days")) . ' ' . $time;
                        }, (array)$datetimes);
                    };

                    // Availability for the current user (normalized for display)
                    $p['mis_slots'] = $normalize(array_column($partidaModel->getDisponibilidad($p['id']), 'franja_inicio'));
                    
                    // Availability for the rival (normalized for display)
                    $rivalId = ($p['local_id'] == $miParticipante['id']) ? $p['visitante_id'] : $p['local_id'];
                    $rivalUser = $participanteModel->findById($rivalId);
                    $rivalSlotsRaw = ($rivalUser && $rivalUser['usuario_id']) ? array_column($partidaModel->getDisponibilidad($p['id'], $rivalUser['usuario_id']), 'franja_inicio') : [];
                    $p['rival_slots'] = $normalize($rivalSlotsRaw);

                    // Normalize fecha_acordada for consistent display and matching in the grid
                    $p['fecha_acordada_original'] = $p['fecha_acordada'];
                    if ($p['fecha_acordada']) {
                        $p['fecha_acordada'] = $normalize($p['fecha_acordada'])[0];
                    }
                    
                    $slotsJornada = $horarioModel->activeSlots((int)$p['jornada_id']);
                    
                    $occupied = [];
                    foreach ($slotsJornada as $slot) {
                        // dia_semana (1=Lunes, 7=Domingo). If fechaBase is Monday, offset is dia_semana - 1.
                        $daysOffset = $slot['dia_semana'] - 1;
                        $targetDate = date('Y-m-d', strtotime("$fechaBase + $daysOffset days"));
                        
                        if (!isset($occupied[$targetDate])) {
                            $occupied[$targetDate] = $partidaModel->getOccupiedSlots($targetDate);
                        }
                    }
                    $p['occupied_slots'] = $occupied;
                    $p['slots_disponibles'] = $slotsJornada;
                    $p['fecha_base_jornada'] = $fechaBase;
                }
            }
        }

        require_once __DIR__ . '/../views/dashboard.php';
    }

    public function setDisponibilidad(int $partidaId): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $participante = (new ParticipanteModel())->findByUsuarioId($user['id']);
        if (!$participante) {
            header('Location: /dashboard');
            exit;
        }

        $model = new PartidaModel();
        $partida = $model->findById($partidaId);
        
        // Verificar que el usuario pertenece a la partida
        // (En el modelo hay que asegurar que participes_id se mapea bien, pero PartidaModel->findById usa participantes table)
        // OJO: PartidaModel usa participantes.id, no usuarios.id.
        // Pero PartidaModel->setDisponibilidad usa usuario_id (de la tabla usuarios)
        
        $slots = $_POST['slots'] ?? [];
        $model->setDisponibilidad($partidaId, $user['id'], $slots);

        // Notificar al rival
        $rivalId = ($partida['local_id'] == $participante['id']) ? $partida['visitante_id'] : $partida['local_id'];
        $rivalParticipante = (new ParticipanteModel())->findById($rivalId);
        if ($rivalParticipante && $rivalParticipante['usuario_id']) {
            $notifModel = new NotificationModel();
            $notifModel->create(
                $rivalParticipante['usuario_id'], 
                'propuesta', 
                "{$user['nombre']} ha propuesto horarios para vuestro partido.", 
                $partidaId
            );
        }

        $_SESSION['flash_success'] = 'Disponibilidad actualizada.';
        header('Location: /dashboard');
        exit;
    }

    public function aceptarDisponibilidad(int $partidaId): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $participante = (new ParticipanteModel())->findByUsuarioId($user['id']);
        if (!$participante) {
            header('Location: /dashboard');
            exit;
        }

        $dateTime = $_GET['fecha'] ?? null;
        if (!$dateTime) {
            header('Location: /dashboard');
            exit;
        }

        $model = new PartidaModel();
        // Ocultar medida de seguridad: verificar que el usuario pertenece a la partida
        $partida = $model->findById($partidaId);
        if (!$partida || ($partida['local_id'] != $participante['id'] && $partida['visitante_id'] != $participante['id'])) {
            header('Location: /dashboard');
            exit;
        }

        $model->setFechaAcordada($partidaId, $dateTime);

        // Notificar al rival
        $rivalId = ($partida['local_id'] == $participante['id']) ? $partida['visitante_id'] : $partida['local_id'];
        $rivalParticipante = (new ParticipanteModel())->findById($rivalId);
        if ($rivalParticipante && $rivalParticipante['usuario_id']) {
            $notifModel = new NotificationModel();
            $fechaFormateada = date('d/m H:i', strtotime($dateTime));
            $notifModel->create(
                $rivalParticipante['usuario_id'], 
                'confirmacion', 
                "¡MATCH! Vuestro partido ha sido confirmado para el $fechaFormateada.", 
                $partidaId
            );
        }

        $_SESSION['flash_success'] = '¡MATCH! Partida acordada para el ' . date('d/m H:i', strtotime($dateTime));
        header('Location: /dashboard');
        exit;
    }

    public function cancelarAcuerdo(int $partidaId): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $participante = (new ParticipanteModel())->findByUsuarioId($user['id']);
        if (!$participante) {
            header('Location: /dashboard');
            exit;
        }

        $model = new PartidaModel();
        $partida = $model->findById($partidaId);
        if (!$partida || ($partida['local_id'] != $participante['id'] && $partida['visitante_id'] != $participante['id'])) {
            header('Location: /dashboard');
            exit;
        }

        $model->cancelarAcuerdo($partidaId);

        // Notificar al rival
        $rivalId = ($partida['local_id'] == $participante['id']) ? $partida['visitante_id'] : $partida['local_id'];
        $rivalParticipante = (new ParticipanteModel())->findById($rivalId);
        if ($rivalParticipante && $rivalParticipante['usuario_id']) {
            $notifModel = new NotificationModel();
            $notifModel->create(
                $rivalParticipante['usuario_id'], 
                'cancelacion', 
                "{$user['nombre']} ha cancelado el horario acordado. Toca volver a proponer.", 
                $partidaId
            );
        }

        $_SESSION['flash_success'] = 'Acuerdo cancelado. El partido vuelve a estar pendiente.';
        header("Location: /dashboard#match-{$partidaId}");
        exit;
    }

    public function markNotificationsRead(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        (new NotificationModel())->markAllAsRead($user['id']);
        
        // Si es AJAX devolvemos JSON, si no, redirigimos
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/dashboard'));
        exit;
    }
}
