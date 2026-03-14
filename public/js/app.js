// Golstalgia — app.js

// Auto-dismiss flash messages after 4s
document.addEventListener('DOMContentLoaded', function () {
  const flashes = document.querySelectorAll('.flash');
  flashes.forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.4s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 4000);
  });

  // NOTIFICACIONES
  const notifBtn = document.getElementById('notif-btn');
  const notifDropdown = document.getElementById('notif-dropdown');
  const markAllReadBtn = document.getElementById('mark-all-read');

  if (notifBtn && notifDropdown) {
    notifBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      notifDropdown.classList.toggle('show');
    });

    document.addEventListener('click', function (e) {
      if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
        notifDropdown.classList.remove('show');
      }
    });

    if (markAllReadBtn) {
      markAllReadBtn.addEventListener('click', function () {
        fetch('/dashboard/notificaciones/leidas', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).then(response => response.json())
          .then(data => {
            if (data.success) {
              // Limpiar insignias y estilos de no leído
              const badge = document.querySelector('.notif-badge');
              if (badge) badge.remove();
              document.querySelectorAll('.notif-item.is-unread').forEach(el => {
                el.classList.remove('is-unread');
              });
              markAllReadBtn.remove();
            }
          });
      });
    }
  }
});
