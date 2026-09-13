<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'UrbanCart | Home';
$pageDescription = 'UrbanCart - buy fashion, electronics, and accessories online with fast delivery and secure checkout.';
$activeNav = 'home';

// Pull featured products dynamically from MySQL instead of hardcoding them.
$stmt = $pdo->prepare('SELECT id, name, price, image FROM products WHERE featured = 1 ORDER BY created_at DESC');
$stmt->execute();
$featuredProducts = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

  <!-- Hero Section -->
  <section class="hero">
    <img src="<?= h(base_url('assets/images/hero-banner.jpg')) ?>" alt="Online shopping banner with shopping bags" class="hero-img" width="1920" height="1282" loading="eager" fetchpriority="high">
    <div class="hero-text">
      <h1>UrbanCart</h1>
      <p class="tagline">Shop the latest trends in fashion, electronics, and accessories — all in one place</p>
      <a href="<?= h(base_url('shop.php')) ?>" class="btn">Shop Now</a>
    </div>
  </section>

  <!-- Short Intro About Business -->
  <section class="intro" aria-labelledby="introHeading">
    <h2 id="introHeading">Who We Are</h2>
    <p>
      UrbanCart is an online store bringing you quality fashion, electronics, and accessories
      at affordable prices. We are committed to fast delivery, secure shopping, and excellent
      customer service, making your online shopping experience simple and enjoyable.
    </p>
  </section>

  <!-- Categories Section -->
  <section class="categories">
    <?php
    $catStmt = $pdo->query('SELECT id, name, image FROM categories ORDER BY id');
    foreach ($catStmt->fetchAll() as $cat):
    ?>
      <a class="category-card" href="<?= h(base_url('shop.php?category=' . $cat['id'])) ?>">
        <img src="<?= h(base_url('assets/images/' . $cat['image'])) ?>" alt="<?= h($cat['name']) ?> category" loading="lazy">
        <h3><?= h($cat['name']) ?></h3>
      </a>
    <?php endforeach; ?>
  </section>

  <!-- Featured Products (dynamic, from MySQL) -->
  <section class="featured-products">
    <h2>Featured Products</h2>
    <div class="product-grid">
      <?php foreach ($featuredProducts as $product): ?>
        <div class="product-card">
          <img src="<?= h(base_url('assets/images/' . $product['image'])) ?>" alt="<?= h($product['name']) ?> product photo" loading="lazy">
          <h3><?= h($product['name']) ?></h3>
          <p class="price"><?= money($product['price']) ?></p>
          <a href="<?= h(base_url('product.php?id=' . $product['id'])) ?>" class="btn-small">View Details</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
