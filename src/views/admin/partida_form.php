<?php
Auth::requireAdmin();
ob_start();

$fecha = '';
$hora = '';
if (!empty($partida['fecha_acordada'])) {
    $dt = new DateTime($partida['fecha_acordada']);
    $fecha = $dt->format('Y-m-d');
    $hora  = $dt->format('H:i');
}
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div>
    <div class="top-bar">
      <h1 class="page-title" style="margin:0;"><span>✏️</span> Editar Partido #<?= $partida['id'] ?></h1>
      <button onclick="history.back()" class="btn btn-dark">← Volver</button>
    </div>

    <form method="POST" action="/admin/partidas/<?= $partida['id'] ?>/actualizar" class="form-container">
      <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">

      <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">Detalles del Encuentro</div>
        <div class="card-body">
          <div style="display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:2rem; text-align:center; margin-bottom:1.5rem;">
            <div>
              <div style="font-family:var(--font-head); font-size:1.5rem; font-weight:700;"><?= htmlspecialchars($partida['nombre_local']) ?></div>
              <div style="font-size:0.75rem; color:#888; text-transform:uppercase;">Local</div>
            </div>
            <div style="font-family:var(--font-head); font-size:2rem; font-weight:700; color:var(--amarillo);">VS</div>
            <div>
              <div style="font-family:var(--font-head); font-size:1.5rem; font-weight:700;"><?= htmlspecialchars($partida['nombre_visitante']) ?></div>
              <div style="font-size:0.75rem; color:#888; text-transform:uppercase;">Visitante</div>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
            <div class="form-group">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-control" id="select-estado">
                <option value="pendiente" <?= $partida['estado'] === 'pendiente' ? 'selected' : '' ?>>PENDIENTE</option>
                <option value="acordada"  <?= $partida['estado'] === 'acordada'  ? 'selected' : '' ?>>ACORDADA</option>
                <option value="jugada"    <?= $partida['estado'] === 'jugada'    ? 'selected' : '' ?>>JUGADA</option>
                <option value="aplazada"  <?= $partida['estado'] === 'aplazada'  ? 'selected' : '' ?>>APLAZADA</option>
                <option value="cancelada" <?= $partida['estado'] === 'cancelada' ? 'selected' : '' ?>>CANCELADA</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Fecha</label>
              <input type="date" name="fecha" class="form-control" value="<?= $fecha ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Hora</label>
              <input type="time" name="hora" class="form-control" value="<?= $hora ?>">
            </div>
          </div>
        </div>
      </div>

      <div id="section-resultado" class="card" style="display: <?= $partida['estado'] === 'jugada' ? 'block' : 'none' ?>;">
        <div class="card-header">Resultado Final</div>
        <div class="card-body">
          <div style="display:grid; grid-template-columns:1fr auto 1fr; gap:1.5rem; align-items:end;">
            <div class="form-group" style="text-align:center;">
              <label class="form-label"><?= htmlspecialchars($partida['nombre_local']) ?></label>
              <input type="number" name="puntos_local" class="form-control" 
                     min="0" max="100" value="<?= $partida['puntos_local'] ?? 0 ?>"
                     style="text-align:center; font-size:2rem; font-family:var(--font-head); font-weight:700;">
            </div>
            <div style="padding-bottom:0.75rem; font-size:2rem; color:#aaa;">—</div>
            <div class="form-group" style="text-align:center;">
              <label class="form-label"><?= htmlspecialchars($partida['nombre_visitante']) ?></label>
              <input type="number" name="puntos_visitante" class="form-control" 
                     min="0" max="100" value="<?= $partida['puntos_visitante'] ?? 0 ?>"
                     style="text-align:center; font-size:2rem; font-family:var(--font-head); font-weight:700;">
            </div>
          </div>
        </div>
      </div>

      <div style="margin-top:1.5rem; display:flex; justify-content:center;">
        <button type="submit" class="btn btn-primary" style="padding:0.75rem 3rem; font-size:1.1rem;">
          💾 Guardar Cambios
        </button>
      </div>
    </form>
  </div>

</div>

<script>
document.getElementById('select-estado').addEventListener('change', function() {
  const section = document.getElementById('section-resultado');
  section.style.display = this.value === 'jugada' ? 'block' : 'none';
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Editar Partido — Admin';
require_once __DIR__ . '/../partials/layout.php';
?>
