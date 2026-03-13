<?php
$user = Auth::user();
ob_start();
?>

<h1 class="page-title"><span>🏟️</span> Dashboard</h1>

<!-- Stats row -->
<div class="grid-4" style="margin-bottom:2rem;">
  <div class="stat-box">
    <div class="stat-number"><?= count($clasificacion) ?></div>
    <div class="stat-label">Participantes</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= count($jornadas) ?></div>
    <div class="stat-label">Jornadas</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= array_reduce($jornadas, fn($c, $j) => $c + $j['total_partidas'], 0) ?></div>
    <div class="stat-label">Partidas</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= $liga ? $liga['temporada'] : '—' ?></div>
    <div class="stat-label">Temporada</div>
  </div>
</div>

<div class="grid-2">

  <!-- Clasificación -->
  <div class="card">
    <div class="card-header">
      🏆 Clasificación <?= $liga ? '— ' . htmlspecialchars($liga['nombre']) : '' ?>
    </div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($clasificacion)): ?>
        <p style="padding:1rem; color:#888; font-size:0.9rem;">Aún no hay resultados registrados.</p>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Jugador</th>
              <th>PJ</th>
              <th>Pts</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clasificacion as $i => $row): ?>
            <?php $isMe = ($miParticipante && $row['participante_id'] == $miParticipante['id']); ?>
            <tr class="<?= $isMe ? 'row-me' : '' ?>">
              <td>
                <span class="<?= $isMe ? 'pos-me' : 'pos-' . ($i + 1) ?>"><?= $i + 1 ?></span>
              </td>
              <td style="font-weight:600;">
                <?= htmlspecialchars($row['nombre']) ?>
                <?php if ($isMe): ?>
                  <span class="badge-me">TÚ</span>
                <?php endif; ?>
              </td>
              <td><?= $row['partidas_jugadas'] ?></td>
              <td style="font-family:var(--font-head); font-weight:700; color:var(--amarillo-retro);">
                <?= $row['puntos_clasificacion'] ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Mis Partidas / Jornadas -->
  <div class="card">
    <div class="card-header">
      <?php if ($miParticipante): ?>
        ⚽ Mis Partidas (<?= htmlspecialchars($miParticipante['nombre']) ?>)
      <?php else: ?>
        📅 Jornadas de la Liga
      <?php endif; ?>
    </div>
    <div class="card-body" style="padding:1.5rem;">
      <?php if ($miParticipante): ?>
        <?php if (empty($misPartidas)): ?>
          <p style="color:#888; font-size:0.9rem;">No tienes partidas programadas en esta liga.</p>
        <?php else: ?>
          <div class="match-list">
            <?php foreach ($misPartidas as $p): ?>
              <?php $esLocal = ($p['local_id'] == $miParticipante['id']); ?>
              <div class="match-row <?= $p['estado'] === 'jugada' ? 'is-played' : '' ?>">
                <div class="match-team <?= $esLocal ? 'row-me-text' : '' ?>">
                  J<?= $p['jornada_numero'] ?>: <?= htmlspecialchars($p['nombre_local']) ?>
                </div>
                <div class="match-score">
                  <?php if ($p['estado'] === 'jugada'): ?>
                    <?= $p['puntos_local'] ?> - <?= $p['puntos_visitante'] ?>
                  <?php else: ?>
                    VS
                  <?php endif; ?>
                </div>
                <div class="match-team right <?= !$esLocal ? 'row-me-text' : '' ?>">
                  <?= htmlspecialchars($p['nombre_visitante']) ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <!-- Vista para administrador o usuario no participante -->
        <ul style="list-style:none; padding:0;">
          <?php foreach ($jornadas as $j): ?>
            <li style="padding:0.75rem; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center;">
              <span style="font-weight:600;">Jornada <?= $j['numero'] ?></span>
              <span class="badge <?= $j['activa'] ? 'badge-verde' : 'badge-gris' ?>">
                <?= $j['activa'] ? 'Activa' : 'Finalizada' ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

</div>

<style>
.row-me-text { color: var(--amarillo-retro); font-weight: 700; }
.match-row.is-played { opacity: 0.8; }
.badge-me { 
  background: var(--amarillo-retro); 
  color: var(--negro-carbon); 
  padding: 0.1rem 0.4rem; 
  border-radius: 4px; 
  font-size: 0.7rem; 
  font-weight: 900; 
  margin-left: 0.5rem;
  box-shadow: 2px 2px 0 #000;
}
.pos-me {
  background: var(--amarillo-retro);
  color: var(--negro-carbon);
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-weight: 800;
  box-shadow: 2px 2px 0 #000;
}
.badge-verde { background: var(--verde-vivo); color: var(--verde-oscuro); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
.badge-gris { background: #333; color: #aaa; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
</style>

<?php
$content = ob_get_clean();
$pageTitle = 'Dashboard — Golstalgia';
require_once __DIR__ . '/partials/layout.php';
?>
