<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div>
    <div class="top-bar">
      <h1 class="page-title" style="margin:0;"><span>📊</span> Registrar Resultado</h1>
      <button onclick="history.back()" class="btn btn-dark">← Volver</button>
    </div>

    <!-- Match preview -->
    <div class="card" style="margin-bottom:1.5rem;">
      <div class="card-header">Partida</div>
      <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:1rem; text-align:center;">
          <div>
            <div style="font-family:var(--font-head); font-size:1.5rem; font-weight:700; letter-spacing:1px;">
              <?= htmlspecialchars($partida['nombre_local']) ?>
            </div>
            <div style="font-size:0.75rem; color:#888; font-family:var(--font-head); letter-spacing:1px; text-transform:uppercase; margin-top:0.2rem;">Local</div>
          </div>
          <div style="font-family:var(--font-head); font-size:2rem; font-weight:700; color:var(--amarillo); text-shadow:2px 2px 0 var(--oscuro);">VS</div>
          <div>
            <div style="font-family:var(--font-head); font-size:1.5rem; font-weight:700; letter-spacing:1px;">
              <?= htmlspecialchars($partida['nombre_visitante']) ?>
            </div>
            <div style="font-size:0.75rem; color:#888; font-family:var(--font-head); letter-spacing:1px; text-transform:uppercase; margin-top:0.2rem;">Visitante</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Result form -->
    <div class="card">
      <div class="card-header">Introduce el resultado</div>
      <div class="card-body">
        <form method="POST" action="/admin/partidas/<?= $partida['id'] ?>/resultado/guardar">
          <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">

          <div style="display:grid; grid-template-columns:1fr auto 1fr; gap:1rem; align-items:end;">
            <div class="form-group" style="margin:0; text-align:center;">
              <label class="form-label" style="display:block;">
                Puntos — <?= htmlspecialchars($partida['nombre_local']) ?>
              </label>
              <input type="number" name="puntos_local" class="form-control"
                     min="0" max="100" value="0" required
                     style="text-align:center; font-size:2rem; font-family:var(--font-head); padding:0.75rem; font-weight:700;">
            </div>

            <div style="padding-bottom:0.5rem; font-family:var(--font-head); font-size:1.5rem; font-weight:700; color:#aaa;">—</div>

            <div class="form-group" style="margin:0; text-align:center;">
              <label class="form-label" style="display:block;">
                Puntos — <?= htmlspecialchars($partida['nombre_visitante']) ?>
              </label>
              <input type="number" name="puntos_visitante" class="form-control"
                     min="0" max="100" value="0" required
                     style="text-align:center; font-size:2rem; font-family:var(--font-head); padding:0.75rem; font-weight:700;">
            </div>
          </div>

          <div id="result-preview" style="text-align:center; padding:1rem 0; font-family:var(--font-head); font-size:1rem; color:#888; letter-spacing:1px;"></div>

          <hr class="section-divider">

          <div style="display:flex; gap:0.75rem; justify-content:center;">
            <button type="submit" class="btn btn-primary" style="font-size:1rem; padding:0.75rem 2rem;">
              ✅ Confirmar resultado
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<script>
(function () {
  const local     = document.querySelector('input[name="puntos_local"]');
  const visitante = document.querySelector('input[name="puntos_visitante"]');
  const preview   = document.getElementById('result-preview');
  const nombreLocal    = <?= json_encode($partida['nombre_local']) ?>;
  const nombreVisitante = <?= json_encode($partida['nombre_visitante']) ?>;

  function update() {
    const l = parseInt(local.value) || 0;
    const v = parseInt(visitante.value) || 0;
    if (l > v)       preview.innerHTML = `🏆 Gana <strong>${nombreLocal}</strong>`;
    else if (v > l)  preview.innerHTML = `🏆 Gana <strong>${nombreVisitante}</strong>`;
    else if (l === v && l > 0) preview.innerHTML = `🤝 Empate`;
    else preview.textContent = '';
  }

  local.addEventListener('input', update);
  visitante.addEventListener('input', update);
})();
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Resultado — Admin';
require_once __DIR__ . '/../partials/layout.php';
?>
