<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div>
    <div class="top-bar">
      <h1 class="page-title" style="margin:0;"><span>⚽</span> Gestión de Partidos</h1>
      
      <form method="GET" action="/admin/partidas" style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
        <select name="liga_id" class="form-control" onchange="this.form.submit()" style="width:auto;">
          <option value="">Todas las ligas</option>
          <?php foreach ($ligas as $l): ?>
            <option value="<?= $l['id'] ?>" <?= $ligaId == $l['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($l['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <?php if ($ligaId): ?>
        <select name="jornada_id" class="form-control" onchange="this.form.submit()" style="width:auto;">
          <option value="">Todas las jornadas</option>
          <?php foreach ($jornadas as $j): ?>
            <option value="<?= $j['id'] ?>" <?= $jornadaId == $j['id'] ? 'selected' : '' ?>>
              Jornada <?= $j['numero'] ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>

        <select name="participante_id" class="form-control" onchange="this.form.submit()" style="width:auto;">
          <option value="">Todos los jugadores</option>
          <?php foreach ($participantes as $part): ?>
            <option value="<?= $part['id'] ?>" <?= $participanteId == $part['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($part['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <a href="/admin/partidas" class="btn btn-dark" title="Limpiar filtros">🧹</a>
      </form>
    </div>

    <?php if (empty($partidas)): ?>
      <div class="card">
        <div class="card-body" style="text-align:center; padding:3rem 1rem;">
          <p style="color:#888;">No hay partidas registradas.</p>
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Liga / Jornada</th>
                <th>Partido</th>
                <th>Estado</th>
                <th>Fecha / Hora</th>
                <th>Resultado</th>
                <th style="text-align:right;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($partidas as $p): ?>
                <tr>
                  <td style="font-size:0.8rem; color:#888;">#<?= $p['id'] ?></td>
                  <td>
                    <div style="font-weight:600; font-size:0.9rem;"><?= htmlspecialchars($p['liga_nombre']) ?></div>
                    <div style="font-size:0.75rem; color:#aaa;">Jornada <?= $p['jornada_numero'] ?></div>
                  </td>
                  <td>
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                      <span style="font-weight:<?= $p['ganador_id'] == $p['local_id'] ? '700' : '400' ?>;">
                        <?= htmlspecialchars($p['nombre_local']) ?>
                      </span>
                      <span style="color:#888; font-size:0.8rem;">vs</span>
                      <span style="font-weight:<?= $p['ganador_id'] == $p['visitante_id'] ? '700' : '400' ?>;">
                        <?= htmlspecialchars($p['nombre_visitante']) ?>
                      </span>
                    </div>
                  </td>
                  <td>
                    <?php
                    $badgeClass = match($p['estado']) {
                        'pendiente' => 'badge-gris',
                        'acordada'  => 'badge-amarillo',
                        'jugada'    => 'badge-verde',
                        'aplazada'  => 'badge-naranja',
                        'cancelada' => 'badge-rojo',
                        default     => 'badge-gris'
                    };
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= strtoupper($p['estado']) ?></span>
                  </td>
                  <td style="font-size:0.85rem;">
                    <?php if ($p['fecha_acordada']): ?>
                      <div><?= date('d/m/Y', strtotime($p['fecha_acordada'])) ?></div>
                      <div style="color:#888;"><?= date('H:i', strtotime($p['fecha_acordada'])) ?></div>
                    <?php else: ?>
                      <span style="color:#aaa;">—</span>
                    <?php endif; ?>
                  </td>
                  <td style="font-family:var(--font-head); font-weight:700;">
                    <?php if ($p['estado'] === 'jugada'): ?>
                      <?= $p['puntos_local'] ?> – <?= $p['puntos_visitante'] ?>
                    <?php else: ?>
                      <span style="color:#aaa;">—</span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align:right;">
                    <a href="/admin/partidas/<?= $p['id'] ?>/editar" class="btn btn-sm btn-dark">✏️ Editar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Partidos — Admin';
require_once __DIR__ . '/../partials/layout.php';
?>
