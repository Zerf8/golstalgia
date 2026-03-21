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
              <?php 
                $esLocal = ($p['local_id'] == $miParticipante['id']); 
                $diasLabels = ['DOM', 'LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB'];
                $fBase = !empty($p['fecha_inicio']) ? $p['fecha_inicio'] : ($p['fecha_base_jornada'] ?? null);
                $headerFecha = '';
                if ($fBase) {
                    $wBase = date('w', strtotime($fBase));
                    $headerFecha = $diasLabels[$wBase] . ' ' . date('d/m', strtotime($fBase));
                }
              ?>
              <div class="match-card <?= $p['estado'] === 'aplazada' ? 'is-postponed' : '' ?>" id="match-<?= $p['id'] ?>">
                <div class="match-header">
                  <?php if ($p['estado'] === 'aplazada'): ?>
                    <span class="match-jornada text-amarillo">PARTIDO APLAZADO</span>
                    <span class="notif-badge" style="position: static; vertical-align: middle; margin-left: 5px; background: var(--amarillo-retro); color: var(--negro-carbon); font-size: 0.7rem; padding: 2px 6px;">J<?= $p['jornada_numero'] ?></span>
                  <?php else: ?>
                    <span class="match-jornada">JORNADA <?= $p['jornada_numero'] ?></span>
                  <?php endif; ?>
                  
                  <?php if ($headerFecha): ?>
                    <span class="match-j-date"><?= $headerFecha ?></span>
                  <?php endif; ?>
                </div>

                <div class="match-row <?= $p['estado'] === 'jugada' ? 'is-played' : '' ?>">
                  <div class="match-team <?= $esLocal ? 'row-me-text' : '' ?>">
                    <?= htmlspecialchars($p['nombre_local']) ?>
                  </div>

                  <div class="match-score-wrap">
                    <?php if ($p['estado'] === 'jugada'): ?>
                      <div class="match-score-final"><?= $p['puntos_local'] ?> - <?= $p['puntos_visitante'] ?></div>
                    <?php else: ?>
                      <?php if (!empty($p['fecha_acordada'])): ?>
                        <?php 
                          $w = date('w', strtotime($p['fecha_acordada']));
                        ?>
                        <div class="match-schedule-link" onclick="toggleProposal(<?= $p['id'] ?>)" style="cursor: pointer; text-align: center;">
                          <div class="schedule-date"><?= $diasLabels[$w] ?> <?= date('d/m', strtotime($p['fecha_acordada'])) ?></div>
                          <div class="schedule-time" style="font-size: 1.4rem; color: var(--verde-vivo); font-family: var(--font-head); font-weight: 900; line-height: 1;"><?= date('H:i', strtotime($p['fecha_acordada'])) ?></div>
                        </div>
                      <?php else: ?>
                        <div class="match-schedule-pending">
                          <button class="btn-propose" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="toggleProposal(<?= $p['id'] ?>)">Proponer Horario</button>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>

                  <div class="match-team right <?= !$esLocal ? 'row-me-text' : '' ?>">
                    <?= htmlspecialchars($p['nombre_visitante']) ?>
                  </div>
                </div> <!-- .match-row -->
                
                <!-- Bloque de propuesta (AHORA FUERA DE MATCH-ROW PARA EVITAR FLEX CLASH) -->
                <?php if ($p['estado'] === 'pendiente' || $p['estado'] === 'acordada'): ?>
                  <?php 
                    $showExpanded = ($p['estado'] === 'acordada' || !empty($p['rival_slots']) || !empty($p['mis_slots']));
                  ?>
                  <div id="proposal-<?= $p['id'] ?>" class="proposal-container" style="<?= $showExpanded ? 'display:grid;' : 'display:none;' ?> width: 100%; padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.15);">
                    <form action="/trivial/dashboard/disponibilidad/<?= $p['id'] ?>" method="POST" id="form-disponibilidad-<?= $p['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.5rem;">
                            <p style="font-size: 0.8rem; font-weight: 700; color: var(--amarillo-retro); margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                                <?php if ($p['estado'] === 'acordada'): ?>
                                    🔄 Cambiar Horario (Acordado el <?= date('d/m H:i', strtotime($p['fecha_acordada'])) ?>)
                                <?php else: ?>
                                    📍 SELECCIONA TU DISPONIBILIDAD
                                <?php endif; ?>
                            </p>
                            <?php if ($p['estado'] === 'acordada'): ?>
                                <a href="/trivial/dashboard/cancelar-acuerdo/<?= $p['id'] ?>" class="btn-reschedule" style="font-size: 0.6rem; padding: 0.4rem 0.8rem; border-radius: 4px;">CANCELAR PARTIDO</a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="slots-grid">
                            <?php 
                            $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                            foreach ($p['slots_disponibles'] as $slot): 
                                $diaNum = $slot['dia_semana'];
                                $targetDate = date('Y-m-d', strtotime($p['fecha_base_jornada'] . " + " . ($diaNum - 1) . " days"));
                                $fullSlot = $targetDate . ' ' . $slot['hora_inicio'];
                                
                                $isSelected = in_array($fullSlot, $p['mis_slots']);
                                $isRivalSelected = in_array($fullSlot, $p['rival_slots'] ?? []);
                                $isOccupied = in_array($slot['hora_inicio'], $p['occupied_slots'][$targetDate] ?? []);
                                $isThisMatchAgreement = ($p['estado'] === 'acordada' && $p['fecha_acordada'] === $fullSlot);
                                
                                $class = '';
                                if ($isThisMatchAgreement) $class .= ' is-agreement is-match';
                                if ($isRivalSelected) $class .= ' is-rival';
                                if ($isSelected) $class .= ' is-selected';
                                if ($isOccupied && !$isThisMatchAgreement) $class .= ' is-occupied';
                                if ($isRivalSelected && $p['estado'] === 'pendiente') $class .= ' is-clickable-rival';

                                $rivalHref = "/trivial/dashboard/aceptar/{$p['id']}?fecha=" . urlencode($fullSlot);
                            ?>
                                <?php if ($isRivalSelected && $p['estado'] === 'pendiente'): ?>
                                    <a href="<?= $rivalHref ?>" class="slot-checkbox <?= $class ?>" title="Confirmar este horario">
                                        <span class="slot-day"><?= substr($diasSemana[$diaNum], 0, 3) ?> <?= date('d/m', strtotime($targetDate)) ?></span>
                                        <span class="slot-time"><?= date('H:i', strtotime($slot['hora_inicio'])) ?></span>
                                        <span class="slot-badge badge-rival">ACEPTAR</span>
                                    </a>
                                <?php else: ?>
                                    <div class="slot-checkbox <?= $class ?>">
                                        <?php if ($p['estado'] === 'pendiente'): ?>
                                            <input type="checkbox" name="slots[]" value="<?= $fullSlot ?>" 
                                                <?= $isSelected ? 'checked' : '' ?> 
                                                <?= $isOccupied ? 'disabled' : '' ?>
                                                id="slot-<?= $p['id'] ?>-<?= $fullSlot ?>"
                                                onchange="this.parentElement.classList.toggle('is-selected', this.checked)">
                                            <label for="slot-<?= $p['id'] ?>-<?= $fullSlot ?>" style="cursor:pointer; display:flex; flex-direction:column; align-items:center;">
                                                <span class="slot-day"><?= substr($diasSemana[$diaNum], 0, 3) ?> <?= date('d/m', strtotime($targetDate)) ?></span>
                                                <span class="slot-time"><?= date('H:i', strtotime($slot['hora_inicio'])) ?></span>
                                            </label>
                                        <?php else: ?>
                                            <span class="slot-day"><?= substr($diasSemana[$diaNum], 0, 3) ?> <?= date('d/m', strtotime($targetDate)) ?></span>
                                            <span class="slot-time"><?= date('H:i', strtotime($slot['hora_inicio'])) ?></span>
                                        <?php endif; ?>
                                        
                                        <?php if ($isThisMatchAgreement): ?>
                                            <span class="slot-badge badge-agreement">¡OK!</span>
                                        <?php endif; ?>
                                        <?php if ($isRivalSelected): ?>
                                            <span class="slot-badge badge-rival">Rival 👍</span>
                                        <?php endif; ?>
                                        <?php if ($isOccupied && !$isThisMatchAgreement): ?>
                                            <span class="slot-badge badge-occupied">OCUPADO</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($p['estado'] === 'pendiente'): ?>
                        <div style="margin-top: 1.5rem; text-align: right;">
                            <button type="submit" class="btn-save-dispo">Guardar mi disponibilidad</button>
                        </div>
                        <?php endif; ?>
                    </form>
                  </div>
                <?php endif; ?>
              </div> <!-- .match-card -->
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
.match-card {
  margin-bottom: 1.5rem;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.match-header {
  background: rgba(255, 255, 255, 0.05);
  padding: 0.6rem 1.2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.match-jornada {
  font-family: var(--font-head);
  font-weight: 900;
  color: var(--amarillo-retro);
  font-size: 0.8rem;
  letter-spacing: 1px;
}
.match-j-date {
  font-size: 0.75rem;
  opacity: 0.6;
  font-weight: 600;
  text-transform: uppercase;
}
.match-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem;
  gap: 1rem;
}
.match-team {
  font-size: 1.1rem;
  font-weight: 600;
  flex: 1;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.match-team.right { text-align: right; }
.match-score-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 120px;
}
.match-score-final {
  background: var(--amarillo-retro);
  color: var(--negro-carbon);
  font-family: var(--font-head);
  font-weight: 900;
  font-size: 1.4rem;
  padding: 0.3rem 1.2rem;
  border-radius: 30px;
  box-shadow: 3px 3px 0 #000;
  letter-spacing: 2px;
}
.match-schedule-btn {
  background: rgba(46, 204, 113, 0.1);
  border: 1px solid var(--verde-vivo);
  border-radius: 8px;
  padding: 0.5rem 1rem;
  cursor: pointer;
  width: 100%;
  transition: 0.2s;
}
.match-schedule-btn:hover {
  background: rgba(46, 204, 113, 0.2);
  transform: translateY(-2px);
}
.schedule-date-sm { font-size: 0.7rem; color: var(--verde-vivo); font-weight: 700; text-transform: uppercase; }
.schedule-time-lg { font-size: 1.3rem; color: var(--verde-vivo); font-weight: 900; }
.schedule-change-tip { font-size: 0.5rem; opacity: 0.6; text-transform: uppercase; margin-top: 2px; }

.row-me-text { color: var(--amarillo-retro); font-weight: 700; }
.match-card.is-played { opacity: 0.8; filter: grayscale(0.5); }
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

.btn-propose {
    background: transparent; border: 1px solid var(--amarillo-retro); color: var(--amarillo-retro);
    padding: 0.2rem 0.5rem; border-radius: 4px; cursor: pointer; font-size: 0.75rem; transition: 0.2s;
}
.btn-propose:hover { background: var(--amarillo-retro); color: var(--negro-carbon); }

</style>

<script>
function toggleProposal(partidaId) {
    const el = document.getElementById('proposal-' + partidaId);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'grid' : 'none';
}

// Auto-open if redirected with hash
window.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash) {
        const matchId = window.location.hash.replace('#match-', '');
        if (matchId) {
            const el = document.getElementById('proposal-' + matchId);
            if (el) el.style.display = 'grid';
        }
    }
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Dashboard — Golstalgia';
require_once __DIR__ . '/../partials/layout.php';
?>
