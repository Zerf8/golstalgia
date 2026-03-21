<?php
Auth::requireAdmin();
ob_start();

$horarios  = $horarios ?? [];
$jornadaId = $jornadaId ?? null;
$jornada   = $jornada ?? null;
$jornadas  = $jornadas ?? [];

$dias = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main">
        <div class="top-bar">
            <h1 class="page-title"><span>🕒</span> Configuración de Horarios</h1>
            <a href="/trivial/admin/ligas" class="btn btn-dark">Volver a Ligas</a>
        </div>

        <!-- Selección de Jornada y Fechas -->
        <div class="card" style="margin-bottom: 2rem; border-color: var(--amarillo-retro);">
            <div class="card-header" style="background: rgba(241, 196, 15, 0.1);">1. Jornada y Período</div>
            <div class="card-body">
                <div class="admin-grid-2" style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: flex-end;">
                    <!-- Combo de Jornadas -->
                    <div class="form-group">
                        <label>Seleccionar Jornada</label>
                        <select class="form-control" onchange="window.location.href='/admin/horarios?jornada_id=' + this.value">
                            <option value="">-- Predeterminado (Global) --</option>
                            <?php foreach ($jornadas as $j): ?>
                                <option value="<?= $j['id'] ?>" <?= ($jornadaId == $j['id']) ? 'selected' : '' ?>>
                                    Jornada <?= $j['numero'] ?> (<?= $j['fecha_inicio'] ? date('d/m', strtotime($j['fecha_inicio'])) : '---' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Edición de Fechas de la Jornada -->
                    <?php if ($jornada): ?>
                        <form action="/trivial/admin/jornadas/<?= $jornada['id'] ?>/fechas" method="POST" class="admin-flex-form" style="display: flex; gap: 1rem; align-items: flex-end; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 10px;">
                            <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">
                            <div class="form-group">
                                <label>Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control" value="<?= $jornada['fecha_inicio'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control" value="<?= $jornada['fecha_fin'] ?>">
                            </div>
                            <button type="submit" class="btn btn-secondary btn-sm">Guardar Fechas</button>
                        </form>
                    <?php else: ?>
                        <p style="opacity: 0.6; padding-bottom: 1rem;">Selecciona una jornada para editar sus fechas y tramos específicos.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Gestión de Tramos -->
        <form action="/trivial/admin/horarios/batch" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">
            <input type="hidden" name="jornada_id" value="<?= $jornadaId ?>">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-family: var(--font-display); color: var(--amarillo-retro);">2. Gestión de Tramos de Juego</h2>
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);">
                    ✅ GUARDAR CAMBIOS
                </button>
            </div>

            <div class="card" style="margin-bottom: 2rem; border-style: dashed; border-color: rgba(255,255,255,0.1); background: transparent;">
                <div class="card-body" style="padding: 1rem; text-align: center; font-size: 0.9rem; opacity: 0.8; color: var(--verde-vivo);">
                    <p>Los recuadros **VERDES** son los tramos activos. Haz clic en uno para desmarcarlo (se volverá transparente) y pulsa **Guardar Cambios**.</p>
                </div>
            </div>

            <div class="grid-2 admin-grid-2" style="gap: 1.5rem;">
                <?php foreach ($dias as $num => $nombre): ?>
                    <div class="card" style="background: rgba(0,0,0,0.2);">
                        <div class="card-header" style="background: rgba(255,255,255,0.05); text-align: center; letter-spacing: 2px;">
                            <?= strtoupper($nombre) ?>
                        </div>
                        <div class="card-body">
                            <div class="slots-grid">
                                <?php 
                                $slotsDia = array_filter($horarios, fn($h) => $h['dia_semana'] == $num);
                                if (empty($slotsDia)): ?>
                                    <p style="text-align: center; opacity: 0.3; padding: 1rem; width: 100%;">Sin tramos.</p>
                                <?php else: ?>
                                    <?php foreach ($slotsDia as $h): ?>
                                        <label class="slot-checkbox <?= $h['activo'] ? 'is-admin-active' : '' ?>">
                                            <input type="checkbox" name="slots[]" value="<?= $h['id'] ?>" <?= $h['activo'] ? 'checked' : '' ?> onchange="this.parentElement.classList.toggle('is-admin-active')">
                                            <span class="slot-day" style="font-size: 0.5rem; opacity: 0.6;"><?= $h['is_specific'] ? 'Personalizado' : 'Global' ?></span>
                                            <span class="slot-time"><?= date('H:i', strtotime($h['hora_inicio'])) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                 <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);">
                    ✅ GUARDAR CAMBIOS EN TRAMOS
                </button>
            </div>
        </form>

        <!-- Añadir Tramo (Fuera del batch form para ser acción rápida) -->
        <div class="card" style="margin-top: 3rem; border-color: var(--verde-cesped); background: rgba(0,0,0,0.3);">
            <div class="card-header" style="color: var(--verde-cesped);">➕ Añadir Nuevo Tramo (Acción Rápida)</div>
            <div class="card-body">
                <form action="/trivial/admin/horarios/guardar" method="POST" class="admin-flex-form" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">
                    <input type="hidden" name="jornada_id" value="<?= $jornadaId ?>">
                    
                    <div class="form-group" style="flex: 1; min-width: 150px;">
                        <label>Día de la Semana</label>
                        <select name="dia_semana" class="form-control">
                            <?php foreach ($dias as $num => $nombre): ?>
                                <option value="<?= $num ?>"><?= $nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="flex: 1; min-width: 150px;">
                        <label>Hora de Inicio</label>
                        <input type="time" name="hora_inicio" class="form-control" step="1800" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="min-width: 200px;">Añadir Ahora</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Horarios — Admin';
require_once __DIR__ . '/../../partials/layout.php';
?>
