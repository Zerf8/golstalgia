<?php
ob_start();
$pageTitle = "Golstalgia – La Liga Trivial de Fútbol Retro";
?>

<div class="public-home-content">
  
  <section class="hero-section">
    <h1 class="hero-title">BIENVENIDO A LA ÉLITE</h1>
    <p class="hero-subtitle">¿Cuánto sabes realmente de fútbol? Demuéstralo en la liga de trivial más exclusiva para verdaderos <strong>Golstálgicos</strong>.</p>
  </section>

  <div class="grid-2">
    
    <!-- COLUMNA IZQUIERDA: RESULTADOS -->
    <div class="home-column-left">
      
      <?php if ($ultimaJornada): ?>
      <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
          ⚽ ÚLTIMOS RESULTADOS (JORNADA <?= $ultimaJornada['numero'] ?>)
        </div>
        <div class="card-body">
          <div class="match-list">
            <?php foreach ($partidasUltima as $p): ?>
              <div class="match-row">
                <div class="match-team"><?= htmlspecialchars($p['nombre_local']) ?></div>
                <div class="match-score"><?= $p['puntos_local'] ?> - <?= $p['puntos_visitante'] ?></div>
                <div class="match-team right"><?= htmlspecialchars($p['nombre_visitante']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($siguienteJornada): ?>
      <div class="card">
        <div class="card-header">
          📅 PRÓXIMA JORNADA (JORNADA <?= $siguienteJornada['numero'] ?>)
        </div>
        <div class="card-body">
          <?php if (empty($partidasProxima)): ?>
            <p style="text-align:center; opacity:0.6;">Calendario pendiente de confirmar.</p>
          <?php else: ?>
            <div class="match-list">
              <?php foreach ($partidasProxima as $p): ?>
                <div class="match-row">
                  <div class="match-team"><?= htmlspecialchars($p['nombre_local']) ?></div>
                  <div class="match-schedule">
                    <?php if (isset($p['puntos_local']) && $p['puntos_local'] !== null): ?>
                      <div class="match-score">
                        <?= $p['puntos_local'] ?> - <?= $p['puntos_visitante'] ?>
                      </div>
                      <small style="font-size: 0.6rem; opacity: 0.6; display: block; margin-top: 2px;">JUGADO</small>
                    <?php elseif ($p['fecha_acordada']): ?>
                      <?php 
                        $dias = ['DOMINGO', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];
                        $w = date('w', strtotime($p['fecha_acordada']));
                      ?>
                      <div class="schedule-date"><?= $dias[$w] ?> <?= date('d/m', strtotime($p['fecha_acordada'])) ?></div>
                      <div class="schedule-time"><?= date('H:i', strtotime($p['fecha_acordada'])) ?></div>
                    <?php else: ?>
                      <div class="schedule-none">Sin asignar</div>
                    <?php endif; ?>
                  </div>
                  <div class="match-team right"><?= htmlspecialchars($p['nombre_visitante']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- COLUMNA DERECHA: CLASIFICACIÓN -->
    <div class="home-column-right">
      <div class="card">
        <div class="card-header">
          🏆 CLASIFICACIÓN GENERAL
        </div>
        <div class="card-body" style="padding: 0;">
          <?php if (empty($clasificacion)): ?>
            <div style="padding: 2rem; text-align: center; opacity: 0.6;">Aún no hay datos de competición.</div>
          <?php else: ?>
            <table class="table-compact">
              <thead>
                <tr>
                  <th style="padding-left: 1.5rem;">POS</th>
                  <th>JUGADOR</th>
                  <th style="text-align: center;">PJ</th>
                  <th style="text-align: center;">V</th>
                  <th style="text-align: center;">E</th>
                  <th style="text-align: center;">D</th>
                  <th style="text-align: center;" title="Goles Favor">GF</th>
                  <th style="text-align: center;" title="Goles Contra">GC</th>
                  <th style="text-align: center; padding-right: 1.5rem;">PTS</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($clasificacion as $idx => $fila): ?>
                  <tr>
                    <td style="padding-left: 1.5rem; font-weight: 800; color: var(--amarillo-retro);">#<?= $idx + 1 ?></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <strong><?= htmlspecialchars($fila['nombre']) ?></strong>
                      </div>
                    </td>
                    <td style="text-align: center;"><?= $fila['partidas_jugadas'] ?></td>
                    <td style="text-align: center;"><?= $fila['victorias'] ?></td>
                    <td style="text-align: center;"><?= $fila['empates'] ?></td>
                    <td style="text-align: center;"><?= $fila['derrotas'] ?></td>
                    <td style="text-align: center; opacity: 0.7;"><?= $fila['puntos_favor'] ?></td>
                    <td style="text-align: center; opacity: 0.7;"><?= $fila['puntos_contra'] ?></td>
                    <td style="text-align: center; font-weight: 800; color: var(--verde-vivo); padding-right: 1.5rem;"><?= $fila['puntos_clasificacion'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../partials/layout.php';
?>
