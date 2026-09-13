<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Gallery | UrbanCart';
$pageDescription = "Browse product photos from UrbanCart's collection.";
$activeNav = 'gallery';

$products = $pdo->query('SELECT name, image FROM products ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

  <section class="page-banner">
    <h1>Product Gallery</h1>
    <p class="subheading">A closer look at our collection</p>
  </section>

  <!-- Image Gallery with click-to-enlarge (dynamic, from MySQL) -->
  <section class="gallery-grid">
    <?php foreach ($products as $product): ?>
      <button type="button" class="gallery-thumb-btn" aria-label="View larger photo of <?= h($product['name']) ?>">
        <img src="<?= h(base_url('assets/images/' . $product['image'])) ?>" alt="<?= h($product['name']) ?> product photo" class="gallery-thumb" loading="lazy">
      </button>
    <?php endforeach; ?>
  </section>

  <!-- Lightbox Modal (hidden by default, shown via JS) -->
  <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Enlarged product photo">
    <button type="button" class="close-btn" id="closeLightbox" aria-label="Close image">&times;</button>
    <img id="lightbox-img" src="" alt="Enlarged product photo">
  </div>

  <script src="<?= h(base_url('assets/js/gallery.js')) ?>"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>
