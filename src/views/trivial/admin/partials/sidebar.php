<aside class="admin-sidebar">
  <div class="sidebar-title">Menú Admin</div>
  <nav class="sidebar-menu">
    <a href="/trivial/admin/usuarios" class="sidebar-item <?= str_contains($_SERVER['REQUEST_URI'], 'usuarios') ? 'active' : '' ?>">👤 Usuarios</a>
    <a href="/trivial/admin/ligas" class="sidebar-item <?= str_contains($_SERVER['REQUEST_URI'], 'ligas') ? 'active' : '' ?>">🏆 Ligas</a>
    <a href="/trivial/admin/jornadas" class="sidebar-item <?= str_contains($_SERVER['REQUEST_URI'], 'jornadas') ? 'active' : '' ?>">📅 Jornadas</a>
    <a href="/trivial/admin/partidas" class="sidebar-item <?= str_contains($_SERVER['REQUEST_URI'], 'partidas') ? 'active' : '' ?>">⚽ Partidas</a>
    <a href="/trivial/admin/horarios" class="sidebar-item <?= str_contains($_SERVER['REQUEST_URI'], 'horarios') ? 'active' : '' ?>">⏰ Horarios</a>
    <a href="/" class="sidebar-item" style="margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1rem; color: var(--rojo-pasion);">🏠 Volver a la Home</a>
  </nav>
</aside>
