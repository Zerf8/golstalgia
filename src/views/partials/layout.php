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
      <a href="/calendario" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/calendario' ? 'active' : '' ?>">Calendario</a>
      <a href="/reglas" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/reglas') ? 'active' : '' ?>">Reglas</a>
      <a href="https://www.ivoox.com/podcast-golstalgia_sq_f1287524_1.html" class="nav-link" target="_blank" rel="noopener">Podcast 🎙️</a>
      <a href="https://www.patreon.com/cw/golstalgia_" class="nav-link" target="_blank" rel="noopener">Patreon 🧡</a>
      
      <?php if (Auth::check()): ?>
        <a href="/dashboard" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">Mi Liga</a>
        <?php if (Auth::isAdmin()): ?>
          <a href="/admin/usuarios" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin') ? 'active' : '' ?>">Panel Admin</a>
        <?php endif; ?>
      <?php endif; ?>

      <!-- Auth links inside mobile menu -->
      <div class="mobile-auth-links">
        <?php if (Auth::check()): ?>
          <span class="user-name" style="padding:0.5rem 1.2rem;"><?= htmlspecialchars(Auth::user()['nombre']) ?></span>
          <a href="/auth/logout" class="btn-logout" style="margin:0.5rem 1rem;">Salir</a>
        <?php else: ?>
          <a href="/auth/login" class="nav-link">Entrar</a>
          <a href="/auth/registro" class="btn btn-primary btn-sm">Regístrate</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="header-user">
      <?php if (Auth::check()): 
          $notifModel = new NotificationModel();
          $unreadCount = $notifModel->getUnreadCount(Auth::user()['id']);
          $recentNotifs = $notifModel->getByUser(Auth::user()['id'], 5);
      ?>
        <!-- Notificaciones -->
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
                <?php foreach ($recentNotifs as $n): ?>
                  <div class="notif-item <?= $n['leida'] ? '' : 'is-unread' ?>" data-id="<?= $n['id'] ?>">
                    <div class="notif-type notif-type-<?= $n['tipo'] ?>"></div>
                    <div class="notif-content">
                      <p><?= htmlspecialchars($n['mensaje']) ?></p>
                      <small><?= date('d/m H:i', strtotime($n['created_at'])) ?></small>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <span class="user-name"><?= htmlspecialchars(Auth::user()['nombre']) ?></span>
        <?php if (Auth::isAdmin()): ?>
          <span class="badge-admin">ADMIN</span>
        <?php endif; ?>
        <a href="/auth/logout" class="btn-logout">Salir</a>
      <?php else: ?>
        <a href="/auth/login" class="nav-link">Entrar</a>
        <a href="/auth/registro" class="btn btn-primary btn-sm">Regístrate</a>
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
  <p style="font-size: 0.7rem; opacity: 0.6; margin-top: 5px;">Sitio Web creado por Zerf 2026</p>
</footer>

<script src="/public/js/app.js"></script>
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
