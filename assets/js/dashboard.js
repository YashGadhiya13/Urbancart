// ===== Dashboard sidebar toggle (Admin + Member areas) =====
// Mirrors the accessible pattern already used by the public site's
// mobile nav toggle in script.js: aria-expanded is kept in sync with
// the actual open/closed state for screen reader users.
document.addEventListener('DOMContentLoaded', function () {

  const toggle = document.getElementById('dashboardMenuToggle');
  const sidebar = document.getElementById('dashboardSidebar');
  const backdrop = document.getElementById('dashboardSidebarBackdrop');

  if (!toggle || !sidebar) {
    return;
  }

  function openSidebar() {
    sidebar.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
    if (backdrop) {
      backdrop.classList.add('open');
    }
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    if (backdrop) {
      backdrop.classList.remove('open');
    }
  }

  toggle.addEventListener('click', function () {
    if (sidebar.classList.contains('open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  });

  if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
  }

  // Escape closes the sidebar when it's open, same convention as the
  // gallery lightbox elsewhere on the site.
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) {
      closeSidebar();
      toggle.focus();
    }
  });

  // Closing on link click keeps mobile navigation between dashboard
  // sections feeling instant rather than leaving the panel open.
  sidebar.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', closeSidebar);
  });
});