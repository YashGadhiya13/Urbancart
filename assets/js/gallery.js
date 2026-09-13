// ===== Gallery Lightbox Feature =====
document.addEventListener('DOMContentLoaded', function () {

  const galleryThumbs = document.querySelectorAll('.gallery-thumb-btn');
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const closeBtn = document.getElementById('closeLightbox');

  galleryThumbs.forEach(function (thumbBtn) {
    thumbBtn.addEventListener('click', function () {
      const img = thumbBtn.querySelector('img');
      lightboxImg.src = img.src;
      lightboxImg.alt = img.alt;
      lightbox.classList.add('active');
      closeBtn.focus(); // move focus into the modal for keyboard users
    });
  });

  closeBtn.addEventListener('click', function () {
    lightbox.classList.remove('active');
  });

  lightbox.addEventListener('click', function (e) {
    if (e.target === lightbox) {
      lightbox.classList.remove('active');
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && lightbox.classList.contains('active')) {
      lightbox.classList.remove('active');
    }
  });

});