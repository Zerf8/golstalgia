<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div>
    <div class="top-bar">
      <div>
        <a href="/trivial/admin/ligas/<?= $liga['id'] ?>/jornadas" style="font-size:0.8rem; color:#888; text-decoration:none;">
          ← <?= htmlspecialchars($liga['nombre']) ?>
        </a>
        <h1 class="page-title" style="margin:0.25rem 0 0;"><span>📅</span> Nueva Jornada <?= $nextNumero ?></h1>
      </div>
      <a href="/trivial/admin/ligas/<?= $liga['id'] ?>/jornadas" class="btn btn-dark">← Volver</a>
    </div>

    <div class="card">
      <div class="card-header">Configurar Jornada <?= $nextNumero ?></div>
      <div class="card-body">
        <form method="POST" action="/trivial/admin/ligas/<?= $liga['id'] ?>/jornadas/guardar" id="jornada-form">
          <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">
          <input type="hidden" name="numero" value="<?= $nextNumero ?>">

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Fecha inicio de la jornada</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>
            <div class="form-group">
              <label class="form-label">Fecha fin de la jornada</label>
              <input type="date" name="fecha_fin" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="activa" class="form-control" style="max-width:200px;">
              <option value="0">Cerrada (se abre manualmente)</option>
              <option value="1">Activa</option>
            </select>
          </div>

          <hr class="section-divider">

          <!-- Match pairing -->
          <div style="margin-bottom:0.75rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <h3 style="font-family:var(--font-head); font-size:1rem; letter-spacing:1px; text-transform:uppercase;">
                ⚽ Emparejamientos (6 partidas)
              </h3>
              <p style="font-size:0.8rem; color:#888; margin-top:0.2rem;">
                Participantes en esta liga: <?= count($participantes) ?>
              </p>
            </div>
            <button type="button" class="btn btn-verde btn-sm" id="btn-autoemparejar">
              🎲 Autoemparejar
            </button>
          </div>

          <div id="partidas-container">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="match-pair" style="display:grid; grid-template-columns:1fr auto 1fr; gap:0.5rem; align-items:center; margin-bottom:0.5rem;">
              <select name="local[]" class="form-control player-select">
                <option value="">— Local —</option>
                <?php foreach ($participantes as $p): ?>
                  <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
              <span style="font-family:var(--font-head); font-size:1.1rem; font-weight:700; color:#aaa; padding:0 0.5rem;">VS</span>
              <select name="visitante[]" class="form-control player-select">
                <option value="">— Visitante —</option>
                <?php foreach ($participantes as $p): ?>
                  <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php endfor; ?>
          </div>

          <div id="pair-warning" style="display:none; color:var(--rojo); font-size:0.85rem; font-family:var(--font-head); letter-spacing:0.5px; margin-top:0.5rem;">
            ⚠️ Hay jugadores repetidos en los emparejamientos.
          </div>

          <hr class="section-divider">

          <div style="display:flex; gap:0.75rem;">
            <button type="submit" class="btn btn-primary">💾 Crear jornada con partidas</button>
            <a href="/trivial/admin/ligas/<?= $liga['id'] ?>/jornadas" class="btn btn-dark">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<script>
(function () {
  const participantes = <?= json_encode(array_map(fn($p) => ['id' => $p['id'], 'nombre' => $p['nombre']], $participantes)) ?>;

  // Auto-pair: shuffle and pair sequentially
  document.getElementById('btn-autoemparejar').addEventListener('click', function () {
    const shuffled = [...participantes].sort(() => Math.random() - 0.5);
    const pairs = document.querySelectorAll('.match-pair');

    pairs.forEach((pair, i) => {
      const local     = pair.querySelector('select[name="local[]"]');
      const visitante = pair.querySelector('select[name="visitante[]"]');
      const a = shuffled[i * 2];
      const b = shuffled[i * 2 + 1];
      if (a && b) {
        local.value     = a.id;
        visitante.value = b.id;
      }
    });

    validatePairs();
  });

  // Validate no player appears twice
  function validatePairs() {
    const selects = document.querySelectorAll('.player-select');
    const values  = [...selects].map(s => s.value).filter(Boolean);
    const hasDupe = values.length !== new Set(values).size;
    document.getElementById('pair-warning').style.display = hasDupe ? 'block' : 'none';
  }

  document.querySelectorAll('.player-select').forEach(s => s.addEventListener('change', validatePairs));

  // Form submit validation
  document.getElementById('jornada-form').addEventListener('submit', function (e) {
    const selects = document.querySelectorAll('.player-select');
    const values  = [...selects].map(s => s.value).filter(Boolean);
    if (values.length !== new Set(values).size) {
      e.preventDefault();
      alert('Hay jugadores repetidos. Corrígelo antes de guardar.');
    }
  });
})();
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Nueva Jornada — Admin';
require_once __DIR__ . '/../../partials/layout.php';
?>
