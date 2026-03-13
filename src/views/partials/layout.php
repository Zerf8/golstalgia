<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Golstalgia Liga' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto+Condensed:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/css/app.css">
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

    <nav class="main-nav">
      <a href="/" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">Inicio</a>
      <a href="/calendario" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/calendario' ? 'active' : '' ?>">Calendario</a>
      <a href="/reglas" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/reglas') ? 'active' : '' ?>">Reglas</a>
      
      <?php if (Auth::check()): ?>
        <a href="/dashboard" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">Mi Liga</a>
        <?php if (Auth::isAdmin()): ?>
          <a href="/admin/usuarios" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin') ? 'active' : '' ?>">Panel Admin</a>
        <?php endif; ?>
      <?php endif; ?>
    </nav>

    <div class="header-user">
      <?php if (Auth::check()): ?>
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
  </div>
</header>

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
</footer>

<script src="/public/js/app.js"></script>
</body>
</html>
