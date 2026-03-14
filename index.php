<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/Database.php';

$router = new Router();

// ─── Auth ─────────────────────────────────────────────
$router->get( '/auth/login',           [AuthController::class, 'loginForm']);
$router->post('/auth/login',           [AuthController::class, 'login']);
$router->get( '/auth/registro',        [AuthController::class, 'registroForm']);
$router->post('/auth/registro',        [AuthController::class, 'registro']);
$router->get( '/auth/google/callback', [AuthController::class, 'googleCallback']);
$router->get( '/auth/logout',          [AuthController::class, 'logout']);

// ─── Root & Public ────────────────────────────────────
$router->get('/', [HomeController::class, 'index']);
$router->get('/reglas', [HomeController::class, 'reglas']);
$router->get('/calendario', [HomeController::class, 'calendario']);

// ─── Dashboard ────────────────────────────────────────
$router->get('/dashboard', [DashboardController::class, 'index']);

// ─── Admin — Usuarios ─────────────────────────────────
$router->get( '/admin',                           [AdminController::class, 'index']);
$router->get( '/admin/usuarios',                  [AdminController::class, 'usuarios']);
$router->get( '/admin/usuarios/crear',            [AdminController::class, 'usuarioCreate']);
$router->post('/admin/usuarios/guardar',          [AdminController::class, 'usuarioStore']);
$router->get( '/admin/usuarios/:id/editar',       [AdminController::class, 'usuarioEdit']);
$router->post('/admin/usuarios/:id/actualizar',   [AdminController::class, 'usuarioUpdate']);
$router->post('/admin/usuarios/:id/eliminar',     [AdminController::class, 'usuarioDelete']);

// ─── Admin — Participantes ───────────────────────────
$router->get( '/admin/participantes',             [AdminController::class, 'participantes']);
$router->get( '/admin/participantes/crear',       [AdminController::class, 'participanteCreate']);
$router->post('/admin/participantes/guardar',     [AdminController::class, 'participanteStore']);
$router->get( '/admin/participantes/:id/editar',  [AdminController::class, 'participanteEdit']);
$router->post('/admin/participantes/:id/actualizar', [AdminController::class, 'participanteUpdate']);
$router->post('/admin/participantes/:id/eliminar', [AdminController::class, 'participanteDelete']);

// ─── Admin — Ligas ────────────────────────────────────
$router->get( '/admin/ligas',                     [AdminController::class, 'ligas']);
$router->get( '/admin/ligas/crear',               [AdminController::class, 'ligaCreate']);
$router->post('/admin/ligas/guardar',             [AdminController::class, 'ligaStore']);
$router->get( '/admin/ligas/:id/editar',          [AdminController::class, 'ligaEdit']);
$router->post('/admin/ligas/:id/actualizar',      [AdminController::class, 'ligaUpdate']);

// ─── Admin — Jornadas ─────────────────────────────────
$router->get( '/admin/ligas/:ligaId/jornadas',         [AdminController::class, 'jornadas']);
$router->get( '/admin/ligas/:ligaId/jornadas/crear',   [AdminController::class, 'jornadaCreate']);
$router->post('/admin/ligas/:ligaId/jornadas/guardar', [AdminController::class, 'jornadaStore']);
$router->post('/admin/jornadas/:id/fechas',            [AdminController::class, 'jornadaUpdateDates']);

// ─── Admin — Resultados ───────────────────────────────
$router->get( '/admin/partidas/:id/resultado',         [AdminController::class, 'resultadoForm']);
$router->post('/admin/partidas/:id/resultado/guardar', [AdminController::class, 'resultadoStore']);
$router->post('/admin/partidas/:id/aplazar',          [AdminController::class, 'partidaPostpone']);

// ─── Admin — Horarios ─────────────────────────────────
$router->get( '/admin/horarios',                       [AdminController::class, 'horarios']);
$router->post('/admin/horarios/guardar',               [AdminController::class, 'horarioStore']);
$router->post('/admin/horarios/batch',             [AdminController::class, 'horariosUpdateBatch']);
$router->post('/admin/horarios/:id/toggle',            [AdminController::class, 'horarioToggle']);
$router->post('/admin/horarios/:id/eliminar',          [AdminController::class, 'horarioDelete']);

// ─── Participante — Disponibilidad ────────────────────
$router->post('/dashboard/disponibilidad/:partidaId',  [DashboardController::class, 'setDisponibilidad']);
$router->get('/dashboard/aceptar/:partidaId', [DashboardController::class, 'aceptarDisponibilidad']);
$router->get('/dashboard/cancelar-acuerdo/:partidaId', [DashboardController::class, 'cancelarAcuerdo']);
$router->post('/dashboard/notificaciones/leidas',      [DashboardController::class, 'markNotificationsRead']);

// ─── Dispatch ─────────────────────────────────────────
$router->dispatch();
