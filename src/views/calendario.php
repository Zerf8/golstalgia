<?php
ob_start();
$pageTitle = "Calendario Oficial – Golstalgia";
?>

<div class="calendar-page">
    <h1 style="font-family: var(--font-display); font-size: 4rem; text-align: center; color: var(--amarillo-retro); margin-bottom: 3rem; text-shadow: 4px 4px 0 #000;">📅 CALENDARIO DE LA LIGA</h1>

    <div class="grid-2">
        <?php foreach ($jornadasCompletas as $item): ?>
            <?php $j = $item['jornada']; $partidas = $item['partidas']; ?>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        JORNADA <?= $j['numero'] ?>
                        <?php if ($j['fecha_inicio'] && $j['fecha_fin']): ?>
                            <small style="font-weight:400; opacity:0.8; margin-left:0.5rem;">
                                <?= date('d/m', strtotime($j['fecha_inicio'])) ?> – <?= date('d/m', strtotime($j['fecha_fin'])) ?>
                            </small>
                        <?php endif; ?>
                    </span>
                    <?php
                        $hoy   = date('Y-m-d');
                        $enCurso = $j['activa']
                            && (!empty($j['fecha_inicio']) && !empty($j['fecha_fin']))
                            && ($hoy >= $j['fecha_inicio'] && $hoy <= $j['fecha_fin']);
                    ?>
                    <?php if ($enCurso): ?>
                        <span class="badge" style="background: var(--verde-vivo); color: var(--verde-oscuro); font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px;">EN CURSO</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="match-list">
                        <?php if (empty($partidas)): ?>
                            <p style="text-align: center; opacity: 0.5; padding: 1rem;">Partidos por asignar.</p>
                        <?php else: ?>
                            <?php foreach ($partidas as $p): ?>
                                <div class="match-row" style="padding: 0.75rem;">
                                    <div class="match-team" style="font-size: 0.95rem;"><?= htmlspecialchars($p['nombre_local']) ?></div>
                                    <?php if ($p['estado'] === 'jugada'): ?>
                                        <div class="match-score" style="font-size: 1rem; width: 60px;"><?= $p['puntos_local'] ?> - <?= $p['puntos_visitante'] ?></div>
                                    <?php else: ?>
                                        <div class="match-vs" style="font-size:0.75rem; text-align:center; line-height:1.3; min-width:60px;">
                                            <?php if (!empty($p['fecha_acordada'])): ?>
                                                <?php 
                                                  $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                                                  $w = date('w', strtotime($p['fecha_acordada']));
                                                ?>
                                                <?= $dias[$w] ?> <?= date('d/m', strtotime($p['fecha_acordada'])) ?><br>
                                                <strong><?= date('H:i', strtotime($p['fecha_acordada'])) ?></strong>
                                            <?php else: ?>
                                                <span style="opacity:0.45; font-size:0.7rem;">Sin<br>asignar</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="match-team right" style="font-size: 0.95rem;"><?= htmlspecialchars($p['nombre_visitante']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/partials/layout.php';
?>
