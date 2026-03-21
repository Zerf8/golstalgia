<?php
Auth::requireAdmin();
$isEdit = isset($usuario);
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div>
    <div class="top-bar">
      <h1 class="page-title" style="margin:0;">
        <span><?= $isEdit ? '✏️' : '➕' ?></span>
        <?= $isEdit ? 'Editar usuario' : 'Nuevo usuario' ?>
      </h1>
      <a href="/trivial/admin/usuarios" class="btn btn-dark">← Volver</a>
    </div>

    <div class="card">
      <div class="card-header"><?= $isEdit ? 'Modificar datos' : 'Datos del nuevo participante' ?></div>
      <div class="card-body">
        <form method="POST" action="<?= $isEdit ? "/admin/usuarios/{$usuario['id']}/actualizar" : '/admin/usuarios/guardar' ?>">
          <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="nombre">Nombre completo *</label>
              <input type="text" id="nombre" name="nombre" class="form-control"
                     value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                     placeholder="Nombre del participante" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="email">Email *</label>
              <input type="email" id="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                     placeholder="email@ejemplo.com" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="password">
                Contraseña <?= $isEdit ? '(dejar en blanco para no cambiar)' : '*' ?>
              </label>
              <input type="password" id="password" name="password" class="form-control"
                     placeholder="<?= $isEdit ? 'Nueva contraseña...' : 'Mínimo 8 caracteres' ?>"
                     <?= $isEdit ? '' : 'required minlength="8"' ?>>
            </div>
            <div class="form-group">
              <label class="form-label" for="nivel">Nivel de acceso</label>
              <select id="nivel" name="nivel" class="form-control">
                <option value="participante" <?= ($usuario['nivel'] ?? 'participante') === 'participante' ? 'selected' : '' ?>>Participante</option>
                <option value="admin"        <?= ($usuario['nivel'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
              </select>
            </div>
          </div>

          <?php if ($isEdit): ?>
          <div class="form-group">
            <label class="form-label" for="activo">Estado</label>
            <select id="activo" name="activo" class="form-control" style="max-width:200px;">
              <option value="1" <?= $usuario['activo'] ? 'selected' : '' ?>>Activo</option>
              <option value="0" <?= !$usuario['activo'] ? 'selected' : '' ?>>Inactivo</option>
            </select>
          </div>
          <?php endif; ?>

          <hr class="section-divider">

          <div style="display:flex; gap:0.75rem;">
            <button type="submit" class="btn btn-primary">
              <?= $isEdit ? '💾 Guardar cambios' : '➕ Crear usuario' ?>
            </button>
            <a href="/trivial/admin/usuarios" class="btn btn-dark">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
$pageTitle = ($isEdit ? 'Editar' : 'Nuevo') . ' usuario — Admin';
require_once __DIR__ . '/../../partials/layout.php';
?>
