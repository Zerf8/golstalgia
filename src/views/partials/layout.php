<?php // Trigger deploy sync: 2026-03-14 21:50 ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Golstalgia Liga' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto+Condensed:wght@400;700&display=swap" rel="stylesheet">
  <?php
    $cssPath = $_SERVER['DOCUMENT_ROOT'] . '/public/css/app.css';
    $cssV    = file_exists($cssPath) ? filemtime($cssPath) : time();
  ?>
  <link rel="stylesheet" href="/public/css/app.css?v=<?= $cssV ?>">
</head>
<body>

<!-- Header -->
<header class="site-header">
  <div class="header-inner">
    <a href="/" class="logo">
      <img src="/public/img/logo_oficial.jpg" alt="Golstalgia Logo" class="logo-img">
      <div class="logo-text-group">
        <span class="logo-text">GOLSTALGIA</span>
        <span class="logo-sub">LIGA TRIVIAL</span>
      </div>
    </a>

    <nav class="main-nav" id="main-nav">
      <a href="/" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">Inicio</a>
      <a href="/trivial" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/trivial' ? 'active' : '' ?>">Liga Trivial</a>
      <?php if ($_SERVER['REQUEST_URI'] !== '/'): ?>
        <a href="/trivial/calendario" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/trivial/calendario' ? 'active' : '' ?>">Calendario</a>
        <a href="/trivial/reglas" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/trivial/reglas') ? 'active' : '' ?>">Reglas</a>
      <?php endif; ?>

      <a href="https://www.ivoox.com/podcast-golstalgia_sq_f1287524_1.html" class="nav-link" target="_blank" rel="noopener">Podcast 🎙️</a>
      
      <?php if ($_SERVER['REQUEST_URI'] !== '/' && Auth::check()): ?>
        <a href="/trivial/dashboard" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/trivial/dashboard') ? 'active' : '' ?>">Mi Liga</a>
        <?php if (Auth::isAdmin()): ?>
          <a href="/trivial/admin/usuarios" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/trivial/admin') ? 'active' : '' ?>">Panel Admin</a>
        <?php endif; ?>
      <?php endif; ?>

      <!-- Auth links inside mobile menu -->
      <?php if ($_SERVER['REQUEST_URI'] !== '/'): ?>
      <div class="mobile-auth-links">
        <?php if (Auth::check()): ?>
          <span class="user-name" style="padding:0.5rem 1.2rem;"><?= htmlspecialchars(Auth::user()['nombre']) ?></span>
          <a href="/trivial/auth/logout" class="btn-logout" style="margin:0.5rem 1rem;">Salir</a>
        <?php else: ?>
          <a href="/trivial/auth/login" class="nav-link">Entrar</a>
          <a href="/trivial/auth/registro" class="btn btn-primary btn-sm">Regístrate</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </nav>

    <div class="header-actions">
      <?php if ($_SERVER['REQUEST_URI'] !== '/'): ?>
        <?php if (Auth::check()): 
            $notifModel = new NotificationModel();
            $unreadCount = $notifModel->getUnreadCount(Auth::user()['id']);
            $recentNotifs = $notifModel->getByUser(Auth::user()['id'], 5);
        ?>
          <!-- Notificaciones (solo fuera de la Home) -->
          <div class="notifications-wrapper" id="notif-wrapper">
          <button class="notif-btn" id="notif-btn" title="Notificaciones">
            <span class="bell-icon">🔔</span>
            <?php if ($unreadCount > 0): ?>
              <span class="notif-badge"><?= $unreadCount ?></span>
            <?php endif; ?>
          </button>
          
          <div class="notif-dropdown" id="notif-dropdown">
            <div class="notif-header">
              <span>Notificaciones</span>
              <?php if ($unreadCount > 0): ?>
                <button id="mark-all-read" class="btn-text">Marcar todas como leídas</button>
              <?php endif; ?>
            </div>
            <div class="notif-body">
              <?php if (empty($recentNotifs)): ?>
                <p class="notif-empty">No tienes notificaciones</p>
              <?php else: ?>
                <?php foreach ($recentNotifs as $n): 
                   $notifUrl = $n['partida_id'] ? "/trivial/dashboard#match-" . $n['partida_id'] : "#";
                ?>
                  <a href="<?= $notifUrl ?>" class="notif-item-link">
                    <div class="notif-item <?= $n['leida'] ? '' : 'is-unread' ?>" data-id="<?= $n['id'] ?>">
                      <div class="notif-type notif-type-<?= $n['tipo'] ?>"></div>
                      <div class="notif-content">
                        <p>
                          <?php if ($n['jornada_numero']): ?>
                            <strong class="text-amarillo">J<?= $n['jornada_numero'] ?>:</strong> 
                          <?php endif; ?>
                          <?= htmlspecialchars($n['mensaje']) ?>
                        </p>
                        <small><?= date('d/m H:i', strtotime($n['created_at'])) ?></small>
                      </div>
                    </div>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          </div>
        <?php endif; ?>

        <div class="header-user">
          <?php if (Auth::check()): ?>
            <span class="user-name"><?= htmlspecialchars(Auth::user()['nombre']) ?></span>
            <?php if (Auth::isAdmin()): ?>
              <span class="badge-admin">ADMIN</span>
            <?php endif; ?>
            <a href="/trivial/auth/logout" class="btn-logout">Salir</a>
          <?php else: ?>
            <a href="/trivial/auth/login" class="nav-link">Entrar</a>
            <a href="/trivial/auth/registro" class="btn btn-primary btn-sm">Regístrate</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Hamburger button -->
    <button class="hamburger" id="hamburger" aria-label="Menú">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- Mobile nav overlay -->
<div class="nav-overlay" id="nav-overlay"></div>

<!-- Flash messages -->
<?php if (!empty($_SESSION['flash_success'])): ?>
  <div class="flash flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
  <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="flash flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Main content -->
<main class="main-content <?= !empty($fullWidth) ? 'is-fullwidth' : '' ?>">
  <?= $content ?? '' ?>
</main>

<footer class="site-footer">
  <p>Golstalgia Liga Trivial &copy; <?= date('Y') ?> &mdash; by Josep y Sagra</p>
  <p style="font-size: 0.7rem; opacity: 0.6; margin-top: 5px;">Sitio Web creado por Zerf v. 1.01 2026</p>
</footer>

<?php
  $jsPath = $_SERVER['DOCUMENT_ROOT'] . '/public/js/app.js';
  $jsV    = file_exists($jsPath) ? filemtime($jsPath) : time();
?>
<script src="/public/js/app.js?v=<?= $jsV ?>"></script>
<script>
const hamburger = document.getElementById('hamburger');
const nav       = document.getElementById('main-nav');
const overlay   = document.getElementById('nav-overlay');
function toggleMenu(open) {
  hamburger.classList.toggle('active', open);
  nav.classList.toggle('open', open);
  overlay.classList.toggle('open', open);
  document.body.style.overflow = open ? 'hidden' : '';
}
hamburger.addEventListener('click', () => toggleMenu(!nav.classList.contains('open')));
overlay.addEventListener('click', () => toggleMenu(false));
</script>
</body>
</html>
