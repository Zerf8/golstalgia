<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div class="admin-main">
    <div class="top-bar">
      <h1 class="page-title"><span>🏆</span> Ligas</h1>
      <a href="/trivial/admin/ligas/crear" class="btn btn-primary">+ Nueva liga</a>
    </div>

    <?php if (empty($ligas)): ?>
      <div class="card">
        <div class="card-body">
          <p style="color:#888; text-align:center; padding:2rem 0;">
            No hay ligas creadas aún.<br>
            <a href="/trivial/admin/ligas/crear" class="btn btn-primary" style="margin-top:1rem;">Crear la primera liga</a>
          </p>
        </div>
      </div>
    <?php else: ?>
    <div style="display:flex; flex-direction:column; gap:1rem;">
      <?php foreach ($ligas as $liga): ?>
      <div class="card">
        <div class="card-header">
          🏆 <?= htmlspecialchars($liga['nombre']) ?>
          <div style="display:flex; gap:0.5rem; align-items:center;">
            <?php if ($liga['activa']): ?>
              <span class="badge badge-verde">Activa</span>
            <?php else: ?>
              <span class="badge badge-gris">Inactiva</span>
            <?php endif; ?>
            <a href="/trivial/admin/ligas/<?= $liga['id'] ?>/editar" class="btn btn-sm btn-dark">Editar</a>
            <a href="/trivial/admin/ligas/<?= $liga['id'] ?>/jornadas" class="btn btn-sm btn-verde">Jornadas</a>
          </div>
        </div>
        <div class="card-body">
          <div class="grid-4">
            <div>
              <div style="font-size:0.75rem; color:#888; font-family:var(--font-head); letter-spacing:1px; text-transform:uppercase; margin-bottom:0.2rem;">Temporada</div>
              <strong style="font-size:1.1rem;"><?= $liga['temporada'] ?></strong>
            </div>
            <div>
              <div style="font-size:0.75rem; color:#888; font-family:var(--font-head); letter-spacing:1px; text-transform:uppercase; margin-bottom:0.2rem;">Inicio</div>
              <strong><?= $liga['fecha_inicio'] ? date('d/m/Y', strtotime($liga['fecha_inicio'])) : '—' ?></strong>
            </div>
            <div>
              <div style="font-size:0.75rem; color:#888; font-family:var(--font-head); letter-spacing:1px; text-transform:uppercase; margin-bottom:0.2rem;">Fin</div>
              <strong><?= $liga['fecha_fin'] ? date('d/m/Y', strtotime($liga['fecha_fin'])) : '—' ?></strong>
            </div>
            <div>
              <div style="font-size:0.75rem; color:#888; font-family:var(--font-head); letter-spacing:1px; text-transform:uppercase; margin-bottom:0.2rem;">Descripción</div>
              <span style="font-size:0.85rem; color:#555;"><?= htmlspecialchars($liga['descripcion'] ?? '—') ?></span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Ligas — Admin';
require_once __DIR__ . '/../../partials/layout.php';
?>
