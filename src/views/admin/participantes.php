<?php
Auth::requireAdmin();
ob_start();
?>

<div class="admin-layout">

  <!-- Sidebar -->
  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div class="admin-main">
    <div class="top-bar">
      <h1 class="page-title"><span>🏃</span> Participantes</h1>
      <a href="/admin/participantes/crear" class="btn btn-primary">+ Nuevo participante</a>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Usuario Vinculado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($participantes as $p): ?>
              <tr>
                <td style="color:#aaa; font-size:0.8rem;"><?= $p['id'] ?></td>
                <td style="font-weight:600;"><?= htmlspecialchars($p['nombre']) ?></td>
                <td style="color:#555;">
                  <?php if ($p['usuario_id']): ?>
                    <span class="badge badge-verde">Vínculo OK</span>
                  <?php else: ?>
                    <span class="badge badge-gris">Sin cuenta</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="/admin/participantes/<?= $p['id'] ?>/editar" class="btn btn-sm btn-dark">Editar</a>
                  <form method="POST" action="/admin/participantes/<?= $p['id'] ?>/eliminar" style="display:inline;"
                        onsubmit="return confirm('¿Eliminar participante <?= htmlspecialchars($p['nombre']) ?>? Esto no borrará sus partidos pero sí su perfil vinculable.')">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">
                    <button type="submit" class="btn btn-sm btn-rojo">Borrar</button>
                  </form>
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
$pageTitle = 'Participantes — Admin';
require_once __DIR__ . '/../partials/layout.php';
?>
