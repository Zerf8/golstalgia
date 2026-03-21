<aside class="admin-sidebar">
  <div class="sidebar-title">Menú Admin</div>
  <nav class="sidebar-menu">
    <a href="/trivial/admin/partidas" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], 'partidas') ? 'active' : '' ?>">⚽ Partidas</a>
    <a href="/trivial/admin/horarios" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], 'horarios') ? 'active' : '' ?>">⏰ Horarios</a>
    <a href="/trivial/admin/jornadas" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], 'jornadas') ? 'active' : '' ?>">📅 Jornadas</a>
    <a href="/trivial/admin/ligas" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], 'ligas') ? 'active' : '' ?>">🏆 Ligas</a>
    <a href="/trivial/admin/participantes" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], 'participantes') ? 'active' : '' ?>">🏃 Participantes</a>
    <a href="/trivial/admin/usuarios" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], 'usuarios') ? 'active' : '' ?>">👤 Usuarios</a>
  </nav>
</aside>
