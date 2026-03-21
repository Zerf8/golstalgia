<?php

class AdminController
{
    public function index(): void
    {
        Auth::requireAdmin();
        $db = Database::connect();
        
        $stats = [
            'usuarios'      => $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
            'participantes' => $db->query("SELECT COUNT(*) FROM participantes")->fetchColumn(),
            'ligas'         => $db->query("SELECT COUNT(*) FROM ligas")->fetchColumn(),
            'partidas_pend' => $db->query("SELECT COUNT(*) FROM partidas WHERE estado = 'pendiente'")->fetchColumn(),
        ];

        require_once __DIR__ . '/../views/trivial/admin/dashboard.php';
    }

    // ─── USUARIOS ─────────────────────────────────────────
    public function usuarios(): void
    {
        Auth::requireAdmin();
        $model   = new UsuarioModel();
        $usuarios = $model->all();
        require_once __DIR__ . '/../views/trivial/admin/usuarios.php';
    }

    public function usuarioCreate(): void
    {
        Auth::requireAdmin();
        require_once __DIR__ . '/../views/trivial/admin/usuario_form.php';
    }

    public function usuarioStore(): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $model = new UsuarioModel();
        $email = trim($_POST['email'] ?? '');

        if ($model->emailExists($email)) {
            $_SESSION['flash_error'] = 'Ese email ya está registrado.';
            header('Location: /trivial/admin/usuarios/crear');
            exit;
        }

        $model->create([
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'email'    => $email,
            'password' => $_POST['password'] ?? '',
            'nivel'    => $_POST['nivel'] ?? 'participante',
        ]);

        $_SESSION['flash_success'] = 'Usuario creado correctamente.';
        header('Location: /trivial/admin/usuarios');
        exit;
    }

    public function usuarioEdit(int $id): void
    {
        Auth::requireAdmin();
        $model   = new UsuarioModel();
        $usuario = $model->findById($id);
        if (!$usuario) { $this->notFound(); return; }
        require_once __DIR__ . '/../views/trivial/admin/usuario_form.php';
    }

    public function usuarioUpdate(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $model = new UsuarioModel();
        $email = trim($_POST['email'] ?? '');

        if ($model->emailExists($email, $id)) {
            $_SESSION['flash_error'] = 'Ese email ya está en uso.';
            header("Location: /trivial/admin/usuarios/{$id}/editar");
            exit;
        }

        $model->update($id, [
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'email'    => $email,
            'nivel'    => $_POST['nivel'] ?? 'participante',
            'activo'   => $_POST['activo'] ?? 1,
            'password' => $_POST['password'] ?? '',
        ]);

        $_SESSION['flash_success'] = 'Usuario actualizado.';
        header('Location: /trivial/admin/usuarios');
        exit;
    }

    public function usuarioDelete(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        (new UsuarioModel())->delete($id);
        $_SESSION['flash_success'] = 'Usuario desactivado.';
        header('Location: /trivial/admin/usuarios');
        exit;
    }

    // ─── PARTICIPANTES ────────────────────────────────────
    public function participantes(): void
    {
        Auth::requireAdmin();
        $participantes = (new ParticipanteModel())->all();
        require_once __DIR__ . '/../views/trivial/admin/participantes.php';
    }

    public function participanteCreate(): void
    {
        Auth::requireAdmin();
        $usuarios = (new UsuarioModel())->all();
        require_once __DIR__ . '/../views/trivial/admin/participante_form.php';
    }

    public function participanteStore(): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $model = new ParticipanteModel();
        $model->create([
            'nombre'     => trim($_POST['nombre'] ?? ''),
            'usuario_id' => !empty($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : null,
        ]);
        $_SESSION['flash_success'] = 'Participante creado.';
        header('Location: /trivial/admin/participantes');
        exit;
    }

    public function participanteEdit(int $id): void
    {
        Auth::requireAdmin();
        $model = new ParticipanteModel();
        $participante = $model->findById($id);
        if (!$participante) { $this->notFound(); return; }
        $usuarios = (new UsuarioModel())->all();
        require_once __DIR__ . '/../views/trivial/admin/participante_form.php';
    }

    public function participanteUpdate(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $model = new ParticipanteModel();
        $model->update($id, [
            'nombre'     => trim($_POST['nombre'] ?? ''),
            'usuario_id' => !empty($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : null,
        ]);
        $_SESSION['flash_success'] = 'Participante actualizado.';
        header('Location: /trivial/admin/participantes');
        exit;
    }

    public function participanteDelete(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        (new ParticipanteModel())->delete($id);
        $_SESSION['flash_success'] = 'Participante eliminado.';
        header('Location: /trivial/admin/participantes');
        exit;
    }

    // ─── LIGAS ────────────────────────────────────────────
    public function ligas(): void
    {
        Auth::requireAdmin();
        $ligas = (new LigaModel())->all();
        require_once __DIR__ . '/../views/trivial/admin/ligas.php';
    }

    public function ligaCreate(): void
    {
        Auth::requireAdmin();
        $model    = new LigaModel();
        $todosParticipantes = (new ParticipanteModel())->all();
        $inscritosIds = [];
        require_once __DIR__ . '/../views/trivial/admin/liga_form.php';
    }

    public function ligaStore(): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $model = new LigaModel();
        $id = $model->create([
            'nombre'      => trim($_POST['nombre'] ?? ''),
            'temporada'   => (int)($_POST['temporada'] ?? 1),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'activa'      => $_POST['activa'] ?? 0,
            'fecha_inicio'=> $_POST['fecha_inicio'] ?? null,
            'fecha_fin'   => $_POST['fecha_fin'] ?? null,
        ]);

        // Assign participants
        foreach ($_POST['participantes'] ?? [] as $uid) {
            $model->addParticipante($id, (int)$uid);
        }

        $_SESSION['flash_success'] = 'Liga creada correctamente.';
        header('Location: /trivial/admin/ligas');
        exit;
    }

    public function ligaEdit(int $id): void
    {
        Auth::requireAdmin();
        $model    = new LigaModel();
        $liga     = $model->findById($id);
        $todosParticipantes = (new ParticipanteModel())->all();
        $inscritosIds = array_column($model->getParticipantes($id), 'id');
        if (!$liga) { $this->notFound(); return; }
        require_once __DIR__ . '/../views/trivial/admin/liga_form.php';
    }

    public function ligaUpdate(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $model = new LigaModel();
        $model->update($id, [
            'nombre'      => trim($_POST['nombre'] ?? ''),
            'temporada'   => (int)($_POST['temporada'] ?? 1),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'activa'      => (int)($_POST['activa'] ?? 0),
            'fecha_inicio'=> $_POST['fecha_inicio'] ?? null,
            'fecha_fin'   => $_POST['fecha_fin'] ?? null,
        ]);

        // Sync participants
        $current = array_column($model->getParticipantes($id), 'id');
        $nuevo   = array_map('intval', $_POST['participantes'] ?? []);

        foreach (array_diff($nuevo, $current) as $uid) {
            $model->addParticipante($id, $uid);
        }
        foreach (array_diff($current, $nuevo) as $uid) {
            $model->removeParticipante($id, $uid);
        }

        $_SESSION['flash_success'] = 'Liga actualizada.';
        header('Location: /trivial/admin/ligas');
        exit;
    }

    // ─── JORNADAS ─────────────────────────────────────────
    public function jornadas($ligaId = null): void
    {
        Auth::requireAdmin();
        $db = Database::connect();

        // One-time schema update for classification
        try {
            // We use a simple query to check if the column exists first to be safer
            $check = $db->query("SHOW COLUMNS FROM clasificacion LIKE 'empates'")->fetch();
            if (!$check) {
                $db->exec("ALTER TABLE clasificacion ADD COLUMN empates INT DEFAULT 0 AFTER victorias");
                // Rebuild all since we just added the column
                $ligas = (new LigaModel())->all();
                $pm = new PartidaModel();
                foreach ($ligas as $l) { $pm->rebuildClasificacion((int)$l['id']); }
            }
        } catch (Exception $e) { /* ignore to prevent 500 */ }
        
        if ($ligaId === null || $ligaId === '') {
            $ligas = (new LigaModel())->all();
            require_once __DIR__ . '/../views/trivial/admin/ligas.php';
            return;
        }

        $liga = (new LigaModel())->findById((int)$ligaId);
        if (!$liga) { $this->notFound(); return; }
        
        $jornadas = (new JornadaModel())->allByLiga((int)$ligaId);
        require_once __DIR__ . '/../views/trivial/admin/jornadas.php';
    }
    }

    public function jornadaCreate(int $ligaId): void
    {
        Auth::requireAdmin();
        $liga         = (new LigaModel())->findById($ligaId);
        $nextNumero   = (new JornadaModel())->nextNumero($ligaId);
        $participantes = (new LigaModel())->getParticipantes($ligaId);
        require_once __DIR__ . '/../views/trivial/admin/jornada_form.php';
    }

    public function jornadaStore(int $ligaId): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $jornadaModel = new JornadaModel();
        $jornadaId = $jornadaModel->create([
            'liga_id'      => $ligaId,
            'numero'       => (int)($_POST['numero'] ?? 1),
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin'    => $_POST['fecha_fin'] ?? null,
            'activa'       => (int)($_POST['activa'] ?? 0),
        ]);

        // Create matches from pairs
        $partidaModel = new PartidaModel();
        $locales     = $_POST['local'] ?? [];
        $visitantes  = $_POST['visitante'] ?? [];

        foreach ($locales as $i => $localId) {
            if (!empty($localId) && !empty($visitantes[$i])) {
                $partidaModel->create([
                    'jornada_id'  => $jornadaId,
                    'local_id'    => (int)$localId,
                    'visitante_id'=> (int)$visitantes[$i],
                ]);
            }
        }

        $_SESSION['flash_success'] = 'Jornada creada con partidas.';
        header("Location: /trivial/admin/ligas/{$ligaId}/jornadas");
        exit;
    }

    // ─── RESULTADOS ───────────────────────────────────────
    public function resultadoForm(int $partidaId): void
    {
        Auth::requireAdmin();
        $model   = new PartidaModel();
        $partida = $model->findById($partidaId);
        if (!$partida) { $this->notFound(); return; }
        require_once __DIR__ . '/../views/trivial/admin/resultado_form.php';
    }

    public function resultadoStore(int $partidaId): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $model = new PartidaModel();
        $ptsLocal = (int)($_POST['puntos_local'] ?? 0);
        $ptsVisit = (int)($_POST['puntos_visitante'] ?? 0);

        $partida   = $model->findById($partidaId);
        $ganadorId = $ptsLocal > $ptsVisit ? $partida['local_id'] : ($ptsVisit > $ptsLocal ? $partida['visitante_id'] : null);

        $model->saveResultado($partidaId, [
            'puntos_local'      => $ptsLocal,
            'puntos_visitante'  => $ptsVisit,
            'ganador_id'        => $ganadorId,
            'registrado_por'    => Auth::user()['id'],
        ]);

        $_SESSION['flash_success'] = 'Resultado guardado.';
        $jornada = (new JornadaModel())->findById($partida['jornada_id']);
        header("Location: /trivial/admin/ligas/{$jornada['liga_id']}/jornadas");
        exit;
    }

    public function partidaPostpone(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $model = new PartidaModel();
        $partida = $model->findById($id);
        if (!$partida) { $this->notFound(); return; }

        $model->updateEstado($id, 'aplazada');

        $_SESSION['flash_success'] = 'Partido marcado como APLAZADO.';
        $jornada = (new JornadaModel())->findById($partida['jornada_id']);
        header("Location: /trivial/admin/ligas/{$jornada['liga_id']}/jornadas");
        exit;
    }

    public function partidas(): void
    {
        Auth::requireAdmin();
        $ligaId         = isset($_GET['liga_id']) ? (int)$_GET['liga_id'] : null;
        $jornadaId      = isset($_GET['jornada_id']) ? (int)$_GET['jornada_id'] : null;
        $participanteId = isset($_GET['participante_id']) ? (int)$_GET['participante_id'] : null;

        $model    = new PartidaModel();
        $partidas = $model->all($ligaId, $jornadaId, $participanteId);
        
        $ligas        = (new LigaModel())->all();
        $participantes = (new ParticipanteModel())->all();
        
        $jornadas = [];
        if ($ligaId) {
            $jornadas = (new JornadaModel())->allByLiga($ligaId);
        }

        require_once __DIR__ . '/../views/trivial/admin/partidas.php';
    }

    public function partidaEdit(int $id): void
    {
        Auth::requireAdmin();
        $model = new PartidaModel();
        $partida = $model->findById($id);
        if (!$partida) { $this->notFound(); return; }
        require_once __DIR__ . '/../views/trivial/admin/partida_form.php';
    }

    public function partidaUpdate(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $model = new PartidaModel();
        $partida = $model->findById($id);
        if (!$partida) { $this->notFound(); return; }

        $estado = $_POST['estado'] ?? 'pendiente';
        $fecha  = $_POST['fecha'] ?? null;
        $hora   = $_POST['hora'] ?? null;
        $fechaAcordada = ($fecha && $hora) ? "$fecha $hora" : null;

        // Actualizar datos básicos del partido
        $model->update($id, [
            'estado'         => $estado,
            'fecha_acordada' => $fechaAcordada
        ]);

        // Si el estado es 'jugada', guardar/actualizar resultado
        if ($estado === 'jugada') {
            $ptsLocal = (int)($_POST['puntos_local'] ?? 0);
            $ptsVisit = (int)($_POST['puntos_visitante'] ?? 0);
            // Determinar ganador basado en puntos (o nul si empate)
            $ganadorId = null;
            if ($ptsLocal > $ptsVisit) $ganadorId = $partida['local_id'];
            elseif ($ptsVisit > $ptsLocal) $ganadorId = $partida['visitante_id'];

            $model->saveResultado($id, [
                'puntos_local'     => $ptsLocal,
                'puntos_visitante' => $ptsVisit,
                'ganador_id'       => $ganadorId,
                'registrado_por'   => Auth::user()['id'],
            ]);
        } else {
            // Si el estado ya no es 'jugada', nos aseguramos de borrar el resultado si existía
            // y reconstruir la clasificación de la liga
            $db = Database::connect();
            $stmt = $db->prepare("DELETE FROM resultados WHERE partida_id = ?");
            $stmt->execute([$id]);
            $model->rebuildClasificacion($partida['liga_id']);
        }

        $_SESSION['flash_success'] = 'Partido actualizado correctamente.';
        header('Location: /trivial/admin/partidas');
        exit;
    }

    // ─── HORARIOS ─────────────────────────────────────────
    public function horarios(): void
    {
        Auth::requireAdmin();
        $ligaActiva = (new LigaModel())->activa();
        $jornadaId  = isset($_GET['jornada_id']) ? (int)$_GET['jornada_id'] : null;
        
        $jornadaModel = new JornadaModel();
        $jornadas = $ligaActiva ? $jornadaModel->allByLiga($ligaActiva['id']) : [];
        
        // Si no hay jornada_id seleccionada, intentar coger la actual o la primera
        if (!$jornadaId && !empty($jornadas)) {
             $siguiente = $jornadaModel->findSiguiente($ligaActiva['id']);
             $jornadaId = $siguiente ? $siguiente['id'] : $jornadas[0]['id'];
        }

        $jornada = $jornadaId ? $jornadaModel->findById($jornadaId) : null;
        
        $model = new HorarioModel();
        $horarios = $model->all($jornadaId);
        require_once __DIR__ . '/../views/trivial/admin/config_horarios.php';
    }

    public function horarioStore(): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $dia = (int)($_POST['dia_semana'] ?? 1);
        $hora = trim($_POST['hora_inicio'] ?? '');
        $jornadaId = !empty($_POST['jornada_id']) ? (int)$_POST['jornada_id'] : null;
        
        if (!empty($hora)) {
            (new HorarioModel())->create($dia, $hora . ':00', $jornadaId);
        }
        
        $qs = $jornadaId ? "?jornada_id={$jornadaId}" : "";
        header('Location: /trivial/admin/horarios' . $qs);
        exit;
    }

    public function jornadaUpdateDates(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        
        $model = new JornadaModel();
        $jornada = $model->findById($id);
        if (!$jornada) { $this->notFound(); return; }

        $data = [
            'numero'       => $jornada['numero'],
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin'    => $_POST['fecha_fin'] ?? null,
            'activa'       => (int)($_POST['activa'] ?? $jornada['activa'])
        ];
        
        $model->update($id, $data);
        
        $_SESSION['flash_success'] = "Fechas de la Jornada {$jornada['numero']} actualizadas.";
        header("Location: /trivial/admin/horarios?jornada_id={$id}");
        exit;
    }

    public function horarioToggle(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $jornadaId = isset($_GET['jornada_id']) ? (int)$_GET['jornada_id'] : null;
        
        $model = new HorarioModel();
        $stmt = Database::connect()->prepare("SELECT * FROM config_horarios WHERE id = ?");
        $stmt->execute([$id]);
        $slot = $stmt->fetch();

        if ($slot) {
            // Si estamos en una jornada y intentamos tocar un tramo GLOBAL (jornada_id is null)
            // debemos CREAR un shadow de ese tramo para esa jornada en lugar de editar el global.
            if ($jornadaId && $slot['jornada_id'] === null) {
                // Crear copia específica con estado invertido
                $stmt = Database::connect()->prepare(
                    "INSERT INTO config_horarios (dia_semana, hora_inicio, activo, jornada_id) 
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $slot['dia_semana'],
                    $slot['hora_inicio'],
                    !$slot['activo'],
                    $jornadaId
                ]);
            } else {
                // Edición normal (específico o global-en-vista-global)
                $model->toggle($id);
            }
        }
        
        $qs = $jornadaId ? "?jornada_id={$jornadaId}" : "";
        header('Location: /trivial/admin/horarios' . $qs);
        exit;
    }

    public function horariosUpdateBatch(): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        
        $jornadaId = !empty($_POST['jornada_id']) ? (int)$_POST['jornada_id'] : null;
        $activeSlots = $_POST['slots'] ?? []; // Array of slot IDs that should be active
        
        $model = new HorarioModel();
        $db = Database::connect();
        
        // Obtenemos todos los slots actuales (mergelist si hay jornadaId)
        $allSlots = $model->all($jornadaId);
        
        foreach ($allSlots as $slot) {
            $isActiveInInput = in_array($slot['id'], $activeSlots);
            
            if ($jornadaId) {
                // VISTA JORNADA
                if ($slot['is_specific']) {
                    // Si ya es específico (añadido a mano o es un shadow de desactivación)
                    if (!$isActiveInInput) {
                        // Borrar si se desmarca
                        $db->prepare("DELETE FROM config_horarios WHERE id = ?")->execute([$slot['id']]);
                    } elseif ($slot['activo'] == 0) {
                        // Si era un shadow de desactivación y se ha MARCADO -> Lo borramos para que vuelva a verse el global
                        $db->prepare("DELETE FROM config_horarios WHERE id = ?")->execute([$slot['id']]);
                    }
                } else {
                    // Es un tramo GLOBAL
                    if (!$isActiveInInput) {
                        // Si se desmarca -> Crear shadow de desactivación
                        $stmt = $db->prepare("SELECT id FROM config_horarios WHERE dia_semana = ? AND hora_inicio = ? AND jornada_id = ?");
                        $stmt->execute([$slot['dia_semana'], $slot['hora_inicio'], $jornadaId]);
                        if (!$stmt->fetch()) {
                            $db->prepare("INSERT INTO config_horarios (jornada_id, dia_semana, hora_inicio, activo) VALUES (?, ?, ?, 0)")
                               ->execute([$jornadaId, $slot['dia_semana'], $slot['hora_inicio']]);
                        }
                    }
                }
            } else {
                // VISTA GLOBAL
                if (!$isActiveInInput) {
                    $db->prepare("DELETE FROM config_horarios WHERE id = ?")->execute([$slot['id']]);
                }
            }
        }
        
        $qs = $jornadaId ? "?jornada_id={$jornadaId}" : "";
        header('Location: /trivial/admin/horarios' . $qs);
        exit;
    }

    public function horarioDelete(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $jornadaId = isset($_GET['jornada_id']) ? (int)$_GET['jornada_id'] : null;
        (new HorarioModel())->delete($id);
        $qs = $jornadaId ? "?jornada_id={$jornadaId}" : "";
        header('Location: /trivial/admin/horarios' . $qs);
        exit;
    }

    // ─── HELPERS ──────────────────────────────────────────
    private function verifyCsrf(): void
    {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('Token CSRF inválido');
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        require_once __DIR__ . '/../views/trivial/404.php';
    }
}
