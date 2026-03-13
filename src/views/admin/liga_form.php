<?php
Auth::requireAdmin();
$isEdit = isset($liga);
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div>
    <div class="top-bar">
      <h1 class="page-title" style="margin:0;">
        <span><?= $isEdit ? '✏️' : '➕' ?></span>
        <?= $isEdit ? 'Editar liga' : 'Nueva liga' ?>
      </h1>
      <a href="/admin/ligas" class="btn btn-dark">← Volver</a>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
      <div class="card-header">Datos de la liga</div>
      <div class="card-body">
        <form method="POST" action="<?= $isEdit ? "/admin/ligas/{$liga['id']}/actualizar" : '/admin/ligas/guardar' ?>">
          <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="nombre">Nombre de la liga *</label>
              <input type="text" id="nombre" name="nombre" class="form-control"
                     value="<?= htmlspecialchars($liga['nombre'] ?? 'Liga Golstalgia') ?>"
                     required>
            </div>
            <div class="form-group">
              <label class="form-label" for="temporada">Temporada / Número *</label>
              <input type="number" id="temporada" name="temporada" class="form-control"
                     value="<?= $liga['temporada'] ?? 1 ?>" min="1" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="2"
                      placeholder="Descripción opcional de la liga..."><?= htmlspecialchars($liga['descripcion'] ?? '') ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="fecha_inicio">Fecha inicio</label>
              <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control"
                     value="<?= $liga['fecha_inicio'] ?? '' ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="fecha_fin">Fecha fin</label>
              <input type="date" id="fecha_fin" name="fecha_fin" class="form-control"
                     value="<?= $liga['fecha_fin'] ?? '' ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="activa">Estado</label>
            <select id="activa" name="activa" class="form-control" style="max-width:200px;">
              <option value="1" <?= ($liga['activa'] ?? 1) == 1 ? 'selected' : '' ?>>Activa</option>
              <option value="0" <?= ($liga['activa'] ?? 1) == 0 ? 'selected' : '' ?>>Inactiva</option>
            </select>
          </div>

          <hr class="section-divider">

          <!-- Participantes -->
          <div class="form-group">
            <label class="form-label">Participantes (<?= count($todosParticipantes) ?> disponibles)</label>
            <p style="font-size:0.8rem; color:#888; margin-bottom:0.75rem;">
              Selecciona los 12 participantes de esta liga.
            </p>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap:0.5rem; max-height:320px; overflow-y:auto; padding:0.75rem; background:#f9f5ee; border:2px solid #ddd;">
              <?php foreach ($todosParticipantes as $p): ?>
              <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; padding:0.4rem 0.5rem; background:#fff; border:1px solid #ddd; border-radius:3px;">
                <input type="checkbox" name="participantes[]" value="<?= $p['id'] ?>"
                  <?= in_array($p['id'], $inscritosIds) ? 'checked' : '' ?>
                  style="accent-color:var(--verde); width:16px; height:16px;">
                <span style="font-size:0.85rem;"><?= htmlspecialchars($p['nombre']) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
            <div id="count-participantes" style="font-size:0.8rem; color:#888; margin-top:0.4rem;">
              <span id="selected-count">0</span> seleccionados
            </div>
          </div>

          <hr class="section-divider">

          <div style="display:flex; gap:0.75rem;">
            <button type="submit" class="btn btn-primary">
              <?= $isEdit ? '💾 Guardar cambios' : '➕ Crear liga' ?>
            </button>
            <a href="/admin/ligas" class="btn btn-dark">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<script>
(function () {
  const checkboxes = document.querySelectorAll('input[name="participantes[]"]');
  const countEl    = document.getElementById('selected-count');

  function update() {
    const n = [...checkboxes].filter(c => c.checked).length;
    countEl.textContent = n;
    countEl.style.color = n === 12 ? 'var(--verde)' : n > 12 ? 'var(--rojo)' : '#888';
  }

  checkboxes.forEach(c => c.addEventListener('change', update));
  update();
})();
</script>

<?php
$content = ob_get_clean();
$pageTitle = ($isEdit ? 'Editar' : 'Nueva') . ' liga — Admin';
require_once __DIR__ . '/../partials/layout.php';
?>
