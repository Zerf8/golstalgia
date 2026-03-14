<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">

  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div class="admin-main">
    <div class="top-bar">
      <h1 class="page-title"><span>👤</span> Usuarios</h1>
      <a href="/admin/usuarios/crear" class="btn btn-primary">+ Nuevo usuario</a>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Nivel</th>
                <th>Estado</th>
                <th>Alta</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $u): ?>
              <tr>
                <td style="color:#aaa; font-size:0.8rem;"><?= $u['id'] ?></td>
                <td style="font-weight:600;"><?= htmlspecialchars($u['nombre']) ?></td>
                <td style="color:#555;"><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <?php if ($u['nivel'] === 'admin'): ?>
                    <span class="badge badge-amarillo">Admin</span>
                  <?php else: ?>
                    <span class="badge badge-gris">Participante</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($u['activo']): ?>
                    <span class="badge badge-verde">Activo</span>
                  <?php else: ?>
                    <span class="badge badge-rojo">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td style="font-size:0.85rem; color:#888;">
                  <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                </td>
                <td>
                  <a href="/admin/usuarios/<?= $u['id'] ?>/editar" class="btn btn-sm btn-dark">Editar</a>
                  <?php if ($u['id'] !== Auth::user()['id'] && $u['activo']): ?>
                  <form method="POST" action="/admin/usuarios/<?= $u['id'] ?>/eliminar" style="display:inline;"
                        onsubmit="return confirm('¿Desactivar a <?= htmlspecialchars($u['nombre']) ?>?')">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">
                    <button type="submit" class="btn btn-sm btn-rojo">Baja</button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Usuarios — Admin';
require_once __DIR__ . '/../partials/layout.php';
?>
