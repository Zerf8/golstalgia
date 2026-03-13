<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro — Golstalgia Liga</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto+Condensed:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/css/app.css">
</head>
<body>

<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="flash flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="login-wrap">
  <div class="login-box">
    <div class="login-header">
      <a href="/" class="logo">
      <img src="/public/img/logo_oficial.jpg" alt="Logo" class="logo-img">
      <div class="logo-text-group">
        <span class="logo-text">GOLSTALGIA</span>
        <span class="logo-sub">LIGA TRIVIAL</span>
      </div>
    </a>
      <span class="login-subtitle">Registro de Nuevo Participante</span>
    </div>
    <div class="login-body">
      <form method="POST" action="/auth/registro">
        <input type="hidden" name="csrf_token" value="<?= Auth::csrf() ?>">

        <div class="form-group">
          <label class="form-label" for="nombre">Nombre / Alias</label>
          <input
            type="text"
            id="nombre"
            name="nombre"
            class="form-control"
            placeholder="Cómo quieres que te llamen"
            required
            autocomplete="name"
          >
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            placeholder="tu@email.com"
            required
            autocomplete="email"
          >
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Contraseña</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            placeholder="••••••••"
            required
            autocomplete="new-password"
          >
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:0.5rem; font-size:1rem; padding:0.75rem;">
          🏆 Crear mi cuenta
        </button>

        <div class="login-separator">
          <span>O BIEN</span>
        </div>

        <a href="<?= $googleLoginUrl ?>" class="btn btn-google" style="width:100%; justify-content:center; font-size:1rem; padding:0.75rem;">
          <img src="https://www.google.com/favicon.ico" alt="Google" style="width:18px; margin-right:10px;">
          Regístrate con Google
        </a>

        <div class="login-footer-links">
          ¿Ya tienes cuenta? <a href="/auth/login">Inicia sesión</a>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
