<?php
Auth::requireAdmin();
$isEdit = isset($participante);
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <!-- Content -->
  <div>
    <div class="top-bar">
      <h1 class="page-title" style="margin:0;">
        <span><?= $isEdit ? '✏️' : '➕' ?></span>
        <?= $isEdit ? 'Editar participante' : 'Nuevo participante' ?>
      </h1>
      <a href="/trivial/admin/participantes" class="btn btn-dark">← Volver</a>
    </div>

    <div class="card" style="max-width: 600px;">
      <div class="card-header">Datos del Participante</div>
      <div class="card-body">
        <form method="POST" action="<?= $isEdit ? "/admin/participantes/{$participante['id']}/actualizar" : '/admin/participantes/guardar' ?>">
          <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">

          <div class="form-group">
            <label class="form-label" for="nombre">Nombre del Jugador *</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($participante['nombre'] ?? '') ?>"
                   placeholder="Ej: Fabián, Zerf..." required>
          </div>

          <div class="form-group">
            <label class="form-label" for="usuario_id">Vincular con Usuario (Opcional)</label>
            <select id="usuario_id" name="usuario_id" class="form-control">
              <option value="">-- Sin vinculación --</option>
              <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ($participante['usuario_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['email']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <p style="font-size:0.75rem; color:#888; margin-top:0.5rem;">
              Relaciona este perfil de juego con una cuenta de acceso real.
            </p>
          </div>

          <div style="display:flex; gap:0.75rem; margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">
              <?= $isEdit ? '💾 Guardar cambios' : '➕ Crear participante' ?>
            </button>
            <a href="/trivial/admin/participantes" class="btn btn-dark">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
$pageTitle = ($isEdit ? 'Editar' : 'Nuevo') . ' participante — Admin';
require_once __DIR__ . '/../../partials/layout.php';
?>
