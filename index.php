<?php
// Trigger deployment: 2026-03-14 22:19
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/Database.php';

$router = new Router();

// ─── Nuevo Inicio Principal ──────────────────────────
$router->get('/', [HomeController::class, 'mainHome']);

// ─── Trivial (Subdirectorio /trivial) ─────────────────
// Auth
$router->get( '/trivial/auth/login',           [AuthController::class, 'loginForm']);
$router->post('/trivial/auth/login',           [AuthController::class, 'login']);
$router->get( '/trivial/auth/registro',        [AuthController::class, 'registroForm']);
$router->post('/trivial/auth/registro',        [AuthController::class, 'registro']);
$router->get( '/trivial/auth/google/callback', [AuthController::class, 'googleCallback']);
// Redirect fallbacks for legacy URLs (Google OAuth etc.)
$router->get('/auth/google/callback', [AuthController::class, 'googleCallback']);
$router->get('/auth/logout',          [AuthController::class, 'logout']);

// Public
$router->get('/trivial',            [HomeController::class, 'index']);
$router->get('/trivial/reglas',     [HomeController::class, 'reglas']);
$router->get('/trivial/calendario', [HomeController::class, 'calendario']);

// Dashboard
$router->get('/trivial/dashboard', [DashboardController::class, 'index']);

// Admin — Usuarios
$router->get( '/trivial/admin',                           [AdminController::class, 'index']);
$router->get( '/trivial/admin/',                          [AdminController::class, 'index']);
$router->get( '/trivial/admin/usuarios',                  [AdminController::class, 'usuarios']);
$router->get( '/trivial/admin/usuarios/crear',            [AdminController::class, 'usuarioCreate']);
$router->post('/trivial/admin/usuarios/guardar',          [AdminController::class, 'usuarioStore']);
$router->get( '/trivial/admin/usuarios/:id/editar',       [AdminController::class, 'usuarioEdit']);
$router->post('/trivial/admin/usuarios/:id/actualizar',   [AdminController::class, 'usuarioUpdate']);
$router->post('/trivial/admin/usuarios/:id/eliminar',     [AdminController::class, 'usuarioDelete']);

// Admin — Participantes
$router->get( '/trivial/admin/participantes',             [AdminController::class, 'participantes']);
$router->get( '/trivial/admin/participantes/crear',       [AdminController::class, 'participanteCreate']);
$router->post('/trivial/admin/participantes/guardar',     [AdminController::class, 'participanteStore']);
$router->get( '/trivial/admin/participantes/:id/editar',  [AdminController::class, 'participanteEdit']);
$router->post('/trivial/admin/participantes/:id/actualizar', [AdminController::class, 'participanteUpdate']);
$router->post('/trivial/admin/participantes/:id/eliminar', [AdminController::class, 'participanteDelete']);

// Admin — Ligas
$router->get( '/trivial/admin/ligas',                     [AdminController::class, 'ligas']);
$router->get( '/trivial/admin/ligas/crear',               [AdminController::class, 'ligaCreate']);
$router->post('/trivial/admin/ligas/guardar',             [AdminController::class, 'ligaStore']);
$router->get( '/trivial/admin/ligas/:id/editar',          [AdminController::class, 'ligaEdit']);
$router->post('/trivial/admin/ligas/:id/actualizar',      [AdminController::class, 'ligaUpdate']);

// Admin — Jornadas
$router->get( '/trivial/admin/jornadas',                       [AdminController::class, 'jornadas']);
$router->get( '/trivial/admin/jornadas/',                      [AdminController::class, 'jornadas']);
$router->get( '/trivial/admin/ligas/:ligaId/jornadas',         [AdminController::class, 'jornadas']);
$router->get( '/trivial/admin/ligas/:ligaId/jornadas/crear',   [AdminController::class, 'jornadaCreate']);
$router->post('/trivial/admin/ligas/:ligaId/jornadas/guardar', [AdminController::class, 'jornadaStore']);
$router->post('/trivial/admin/jornadas/:id/fechas',            [AdminController::class, 'jornadaUpdateDates']);

// Admin — Partidas
$router->get( '/trivial/admin/partidas',                       [AdminController::class, 'partidas']);
$router->get( '/trivial/admin/partidas/:id/editar',            [AdminController::class, 'partidaEdit']);
$router->post('/trivial/admin/partidas/:id/actualizar',        [AdminController::class, 'partidaUpdate']);

// Admin — Resultados
$router->get( '/trivial/admin/partidas/:id/resultado',         [AdminController::class, 'resultadoForm']);
$router->post('/trivial/admin/partidas/:id/resultado/guardar', [AdminController::class, 'resultadoStore']);
$router->post('/trivial/admin/partidas/:id/aplazar',          [AdminController::class, 'partidaPostpone']);

// Admin — Horarios
$router->get( '/trivial/admin/horarios',                       [AdminController::class, 'horarios']);
$router->post('/trivial/admin/horarios/guardar',               [AdminController::class, 'horarioStore']);
$router->post('/trivial/admin/horarios/batch',             [AdminController::class, 'horariosUpdateBatch']);
$router->post('/trivial/admin/horarios/:id/toggle',            [AdminController::class, 'horarioToggle']);
$router->post('/trivial/admin/horarios/:id/eliminar',          [AdminController::class, 'horarioDelete']);

// Participante — Disponibilidad
$router->post('/trivial/dashboard/disponibilidad/:partidaId',  [DashboardController::class, 'setDisponibilidad']);
$router->get('/trivial/dashboard/aceptar/:partidaId', [DashboardController::class, 'aceptarDisponibilidad']);
$router->get('/trivial/dashboard/cancelar-acuerdo/:partidaId', [DashboardController::class, 'cancelarAcuerdo']);
$router->post('/trivial/dashboard/notificaciones/leidas',      [DashboardController::class, 'markNotificationsRead']);

// ─── Dispatch ─────────────────────────────────────────
$router->dispatch();
