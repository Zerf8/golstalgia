<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">
  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div class="admin-main">
    <div class="top-bar">
      <h1 class="page-title"><span>🚀</span> Panel de Control</h1>
      <div class="admin-stats">
        <div class="stat-item">
          <span class="stat-value"><?= $stats['usuarios'] ?></span>
          <span class="stat-label">Usuarios</span>
        </div>
        <div class="stat-item">
          <span class="stat-value"><?= $stats['participantes'] ?></span>
          <span class="stat-label">Participantes</span>
        </div>
        <div class="stat-item">
          <span class="stat-value"><?= $stats['ligas'] ?></span>
          <span class="stat-label">Ligas</span>
        </div>
        <div class="stat-item highlight">
          <span class="stat-value"><?= $stats['partidas_pend'] ?></span>
          <span class="stat-label">Partidas Pend.</span>
        </div>
      </div>
    </div>

    <div class="admin-grid-cards">
      <a href="/trivial/admin/usuarios" class="admin-card-link">
        <div class="card-icon">👤</div>
        <h3>Gestión de Usuarios</h3>
        <p>Añade, edita o desactiva las cuentas de los participantes y administradores.</p>
        <div class="card-action">Ir ahora →</div>
      </a>

      <a href="/trivial/admin/participantes" class="admin-card-link">
        <div class="card-icon">🏃</div>
        <h3>Jugadores y Perfiles</h3>
        <p>Vincula usuarios a la liga y gestiona sus datos de participación.</p>
        <div class="card-action">Ir ahora →</div>
      </a>

      <a href="/trivial/admin/ligas" class="admin-card-link">
        <div class="card-icon">🏆</div>
        <h3>Ligas y Jornadas</h3>
        <p>Configura las competiciones activas y el calendario de enfrentamientos.</p>
        <div class="card-action">Ir ahora →</div>
      </a>

      <a href="/trivial/admin/horarios" class="admin-card-link">
        <div class="card-icon">🕒</div>
        <h3>Configuración de Horarios</h3>
        <p>Define los tramos predefinidos y específicos por jornada.</p>
        <div class="card-action">Ir ahora →</div>
      </a>
    </div>

    <div class="card" style="margin-top:2rem; border-color:var(--verde-vivo); background:rgba(46, 204, 113, 0.05);">
        <div class="card-header" style="background:rgba(46, 204, 113, 0.1);">💡 Consejo de Admin</div>
        <div class="card-body">
            <p>Desde la sección de <strong>Ligas → Jornadas</strong> puedes ver el estado directo de los acuerdos de partidos y forzar resultados si es necesario.</p>
        </div>
    </div>
  </div>
</div>

<style>
.admin-stats { display: flex; gap: 1rem; }
.stat-item { background: rgba(255,255,255,0.05); padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); display: flex; flex-direction: column; align-items: center; min-width: 100px; }
.stat-item.highlight { border-color: var(--amarillo-retro); background: rgba(241, 196, 15, 0.05); }
.stat-value { font-family: var(--font-display); font-size: 1.5rem; color: var(--amarillo-retro); line-height: 1; }
.stat-label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; margin-top: 4px; }

.admin-grid-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
.admin-card-link { 
    background: var(--bg-card); border: 1px solid var(--glass-border); border-radius: 12px; padding: 2rem; 
    text-decoration: none; transition: var(--transition); display: flex; flex-direction: column; gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.admin-card-link:hover { transform: translateY(-5px); border-color: var(--amarillo-retro); background: rgba(255,255,255,0.03); }
.card-icon { font-size: 2.5rem; margin-bottom: 0.5rem; }
.admin-card-link h3 { color: var(--amarillo-retro); font-family: var(--font-display); font-size: 1.3rem; margin: 0; }
.admin-card-link p { font-size: 0.9rem; opacity: 0.7; color: white; line-height: 1.5; }
.card-action { margin-top: auto; font-size: 0.8rem; font-weight: bold; color: var(--amarillo-retro); text-transform: uppercase; letter-spacing: 1px; }

@media (max-width: 768px) {
  .admin-stats { flex-wrap: wrap; }
  .stat-item { flex: 1 1 40%; }
}
</style>

<?php
$content = ob_get_clean();
$pageTitle = 'Administración — Golstalgia';
require_once __DIR__ . '/../../partials/layout.php';
?>
