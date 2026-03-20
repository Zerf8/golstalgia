<aside class="admin-sidebar">
  <div class="sidebar-title">⚙️ Panel Admin</div>
  <nav class="sidebar-menu">
    <a href="/admin"                class="sidebar-link <?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">🚀 Inicio</a>
    <a href="/admin/usuarios"       class="sidebar-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/usuarios') ? 'active' : '' ?>">👤 Usuarios</a>
    <a href="/admin/participantes"  class="sidebar-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/participantes') ? 'active' : '' ?>">🏃 Participantes</a>
    <a href="/admin/ligas"          class="sidebar-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/ligas')    ? 'active' : '' ?>">🏆 Ligas</a>
    <a href="/admin/partidas"       class="sidebar-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/partidas') ? 'active' : '' ?>">⚽ Partidos</a>
    <a href="/admin/horarios"       class="sidebar-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/horarios') ? 'active' : '' ?>">🕒 Horarios</a>
  </nav>
</aside>
