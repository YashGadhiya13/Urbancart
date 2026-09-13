// ===== Mobile Menu Toggle =====
document.addEventListener('DOMContentLoaded', function () {

  const menuToggle = document.getElementById('menuToggle');
  const navMenu = document.getElementById('navMenu');

  if (menuToggle && navMenu) {
    menuToggle.addEventListener('click', function () {
      navMenu.classList.toggle('show');

      const isExpanded = navMenu.classList.contains('show');
      menuToggle.setAttribute('aria-expanded', isExpanded);
    });
  }

  // Close menu when a link is clicked (better mobile UX)
  const navLinks = document.querySelectorAll('#navMenu a');
  navLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      navMenu.classList.remove('show');
      menuToggle.setAttribute('aria-expanded', 'false');
    });
  });

});