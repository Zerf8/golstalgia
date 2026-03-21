<aside class="admin-sidebar">
  <nav class="admin-nav">
    <a href="/trivial/admin/usuarios" class="<?= str_contains($_SERVER['REQUEST_URI'], 'usuarios') ? 'active' : '' ?>">👤 Usuarios</a>
    <a href="/trivial/admin/ligas" class="<?= str_contains($_SERVER['REQUEST_URI'], 'ligas') ? 'active' : '' ?>">🏆 Ligas</a>
    <a href="/trivial/admin/jornadas" class="<?= str_contains($_SERVER['REQUEST_URI'], 'jornadas') ? 'active' : '' ?>">📅 Jornadas</a>
    <a href="/trivial/admin/partidas" class="<?= str_contains($_SERVER['REQUEST_URI'], 'partidas') ? 'active' : '' ?>">⚽ Partidas</a>
    <a href="/trivial/admin/horarios" class="<?= str_contains($_SERVER['REQUEST_URI'], 'horarios') ? 'active' : '' ?>">⏰ Horarios</a>
    <a href="/" class="back-link">🏠 Volver a la Home</a>
  </nav>
</aside>
