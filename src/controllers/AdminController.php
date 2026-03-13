<?php

class AdminController
{
    // ─── USUARIOS ─────────────────────────────────────────
    public function usuarios(): void
    {
        Auth::requireAdmin();
        $model   = new UsuarioModel();
        $usuarios = $model->all();
        require_once __DIR__ . '/../views/admin/usuarios.php';
    }

    public function usuarioCreate(): void
    {
        Auth::requireAdmin();
        require_once __DIR__ . '/../views/admin/usuario_form.php';
    }

    public function usuarioStore(): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $model = new UsuarioModel();
        $email = trim($_POST['email'] ?? '');

        if ($model->emailExists($email)) {
            $_SESSION['flash_error'] = 'Ese email ya está registrado.';
            header('Location: /admin/usuarios/crear');
            exit;
        }

        $model->create([
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'email'    => $email,
            'password' => $_POST['password'] ?? '',
            'nivel'    => $_POST['nivel'] ?? 'participante',
        ]);

        $_SESSION['flash_success'] = 'Usuario creado correctamente.';
        header('Location: /admin/usuarios');
        exit;
    }

    public function usuarioEdit(int $id): void
    {
        Auth::requireAdmin();
        $model   = new UsuarioModel();
        $usuario = $model->findById($id);
        if (!$usuario) { $this->notFound(); return; }
        require_once __DIR__ . '/../views/admin/usuario_form.php';
    }

    public function usuarioUpdate(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $model = new UsuarioModel();
        $email = trim($_POST['email'] ?? '');

        if ($model->emailExists($email, $id)) {
            $_SESSION['flash_error'] = 'Ese email ya está en uso.';
            header("Location: /admin/usuarios/{$id}/editar");
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
        header('Location: /admin/usuarios');
        exit;
    }

    public function usuarioDelete(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        (new UsuarioModel())->delete($id);
        $_SESSION['flash_success'] = 'Usuario desactivado.';
        header('Location: /admin/usuarios');
        exit;
    }

    // ─── PARTICIPANTES ────────────────────────────────────
    public function participantes(): void
    {
        Auth::requireAdmin();
        $participantes = (new ParticipanteModel())->all();
        require_once __DIR__ . '/../views/admin/participantes.php';
    }

    public function participanteCreate(): void
    {
        Auth::requireAdmin();
        $usuarios = (new UsuarioModel())->all();
        require_once __DIR__ . '/../views/admin/participante_form.php';
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
        header('Location: /admin/participantes');
        exit;
    }

    public function participanteEdit(int $id): void
    {
        Auth::requireAdmin();
        $model = new ParticipanteModel();
        $participante = $model->findById($id);
        if (!$participante) { $this->notFound(); return; }
        $usuarios = (new UsuarioModel())->all();
        require_once __DIR__ . '/../views/admin/participante_form.php';
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
        header('Location: /admin/participantes');
        exit;
    }

    public function participanteDelete(int $id): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        (new ParticipanteModel())->delete($id);
        $_SESSION['flash_success'] = 'Participante eliminado.';
        header('Location: /admin/participantes');
        exit;
    }

    // ─── LIGAS ────────────────────────────────────────────
    public function ligas(): void
    {
        Auth::requireAdmin();
        $ligas = (new LigaModel())->all();
        require_once __DIR__ . '/../views/admin/ligas.php';
    }

    public function ligaCreate(): void
    {
        Auth::requireAdmin();
        $model    = new LigaModel();
        $todosParticipantes = (new ParticipanteModel())->all();
        $inscritosIds = [];
        require_once __DIR__ . '/../views/admin/liga_form.php';
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
        header('Location: /admin/ligas');
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
        require_once __DIR__ . '/../views/admin/liga_form.php';
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
        header('Location: /admin/ligas');
        exit;
    }

    // ─── JORNADAS ─────────────────────────────────────────
    public function jornadas(int $ligaId): void
    {
        Auth::requireAdmin();
        $ligaModel  = new LigaModel();
        $liga       = $ligaModel->findById($ligaId);
        if (!$liga) { $this->notFound(); return; }
        $jornadas   = (new JornadaModel())->allByLiga($ligaId);
        require_once __DIR__ . '/../views/admin/jornadas.php';
    }

    public function jornadaCreate(int $ligaId): void
    {
        Auth::requireAdmin();
        $liga         = (new LigaModel())->findById($ligaId);
        $nextNumero   = (new JornadaModel())->nextNumero($ligaId);
        $participantes = (new LigaModel())->getParticipantes($ligaId);
        require_once __DIR__ . '/../views/admin/jornada_form.php';
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
        header("Location: /admin/ligas/{$ligaId}/jornadas");
        exit;
    }

    // ─── RESULTADOS ───────────────────────────────────────
    public function resultadoForm(int $partidaId): void
    {
        Auth::requireAdmin();
        $model   = new PartidaModel();
        $partida = $model->findById($partidaId);
        if (!$partida) { $this->notFound(); return; }
        require_once __DIR__ . '/../views/admin/resultado_form.php';
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
        header("Location: /admin/ligas/{$jornada['liga_id']}/jornadas");
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
        require_once __DIR__ . '/../views/404.php';
    }
}
