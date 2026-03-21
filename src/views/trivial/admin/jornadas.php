<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div>
    <div class="top-bar">
      <div>
        <a href="/trivial/admin/ligas" style="font-size:0.8rem; color:#888; text-decoration:none;">← Ligas</a>
        <h1 class="page-title" style="margin:0.25rem 0 0;">
          <span>📅</span> Jornadas — <?= htmlspecialchars($liga['nombre']) ?>
          <span class="badge badge-amarillo" style="font-size:0.75rem; vertical-align:middle;">T<?= $liga['temporada'] ?></span>
        </h1>
      </div>
      <a href="/trivial/admin/ligas/<?= $liga['id'] ?>/jornadas/crear" class="btn btn-primary">+ Nueva jornada</a>
    </div>

    <?php if (empty($jornadas)): ?>
      <div class="card">
        <div class="card-body" style="text-align:center; padding:3rem 1rem;">
          <p style="color:#888; margin-bottom:1rem;">No hay jornadas creadas para esta liga.</p>
          <a href="/trivial/admin/ligas/<?= $liga['id'] ?>/jornadas/crear" class="btn btn-primary">Crear Jornada 1</a>
        </div>
      </div>
    <?php else: ?>

      <?php foreach ($jornadas as $jornada): ?>
      <?php
        $pm = new PartidaModel();
        $partidas = $pm->allByJornada($jornada['id']);
      ?>
      <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
          📅 Jornada <?= $jornada['numero'] ?>
          <?php if ($jornada['fecha_inicio']): ?>
            <span style="font-size:0.8rem; font-weight:400; color:#ccc;">
              <?= date('d/m/Y', strtotime($jornada['fecha_inicio'])) ?>
              <?= $jornada['fecha_fin'] ? ' — ' . date('d/m/Y', strtotime($jornada['fecha_fin'])) : '' ?>
            </span>
          <?php endif; ?>
          <div style="display:flex; gap:0.5rem; align-items:center; margin-left:auto;">
            <?php if ($jornada['activa']): ?>
              <span class="badge badge-verde">Activa</span>
            <?php else: ?>
              <span class="badge badge-gris">Cerrada</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body" style="padding:1rem;">
          <?php if (empty($partidas)): ?>
            <p style="color:#aaa; font-size:0.85rem;">Sin partidas en esta jornada.</p>
          <?php else: ?>
            <?php foreach ($partidas as $p): ?>
            <div class="match-card">
              <div class="match-team">
                <?= htmlspecialchars($p['nombre_local']) ?>
                <?php if ($p['ganador_id'] == $p['local_id']): ?>
                  <span style="color:var(--verde);">🏆</span>
                <?php endif; ?>
              </div>

              <?php if ($p['estado'] === 'jugada'): ?>
                <div class="match-score"><?= $p['puntos_local'] ?> – <?= $p['puntos_visitante'] ?></div>
              <?php else: ?>
                <div class="match-vs">
                  <span class="badge badge-<?= $p['estado'] === 'acordada' ? 'amarillo' : 'gris' ?>">
                    <?= strtoupper($p['estado']) ?>
                  </span>
                  <div style="margin-top:0.25rem; display:flex; gap:0.25rem; justify-content:center;">
                    <a href="/trivial/admin/partidas/<?= $p['id'] ?>/resultado" class="btn btn-sm btn-verde">Resultado</a>
                    <?php if ($p['estado'] !== 'aplazada'): ?>
                      <form action="/trivial/admin/partidas/<?= $p['id'] ?>/aplazar" method="POST" onsubmit="return confirm('¿Seguro que quieres aplazar este partido? Podrán elegir horario en cualquier semana.')">
                        <?= Auth::csrfInput() ?>
                        <button type="submit" class="btn btn-sm btn-gris" title="Aplazar partido">⏳</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>

              <div class="match-team right">
                <?php if ($p['ganador_id'] == $p['visitante_id']): ?>
                  <span style="color:var(--verde);">🏆</span>
                <?php endif; ?>
                <?= htmlspecialchars($p['nombre_visitante']) ?>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>

    <?php endif; ?>
  </div>

</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Jornadas — Admin';
require_once __DIR__ . '/../../partials/layout.php';
?>
