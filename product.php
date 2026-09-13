<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    $pageTitle = 'Product Not Found | UrbanCart';
    $robotsMeta = 'noindex, follow';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-banner"><h1>Product Not Found</h1><p><a href="' . h(base_url('shop.php')) . '">Back to Shop</a></p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found | UrbanCart';
    $robotsMeta = 'noindex, follow';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-banner"><h1>Product Not Found</h1><p><a href="' . h(base_url('shop.php')) . '">Back to Shop</a></p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $product['name'] . ' | UrbanCart';
$pageDescription = 'Buy ' . $product['name'] . ' online at UrbanCart for ' . money($product['price']) . '. ' . mb_strimwidth(strip_tags($product['description']), 0, 120, '...');
$activeNav = 'shop';
$canonicalUrl = absolute_url('product.php?id=' . $product['id']);
$ogType = 'product';
$ogImage = absolute_url('assets/images/' . $product['image']);

// Product structured data (Schema.org) - only real, page-accurate values.
$structuredData = json_encode([
    '@context'    => 'https://schema.org/',
    '@type'       => 'Product',
    'name'        => $product['name'],
    'image'       => [$ogImage],
    'description' => strip_tags($product['description']),
    'sku'         => (string) $product['id'],
    'offers'      => [
        '@type'         => 'Offer',
        'url'           => $canonicalUrl,
        'priceCurrency' => 'USD',
        'price'         => number_format((float) $product['price'], 2, '.', ''),
        'availability'  => $product['stock'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
    ],
], JSON_UNESCAPED_SLASHES);

require __DIR__ . '/includes/header.php';
?>

  <nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
      <li><a href="<?= h(base_url('index.php')) ?>">Home</a></li>
      <li><a href="<?= h(base_url('shop.php')) ?>">Shop</a></li>
      <li aria-current="page"><?= h($product['name']) ?></li>
    </ol>
  </nav>

  <section class="product-detail-section">

    <div class="product-detail-img">
      <img src="<?= h(base_url('assets/images/' . $product['image'])) ?>" alt="<?= h($product['name']) ?> product photo">
    </div>

    <div class="product-detail-info">
      <h1><?= h($product['name']) ?></h1>
      <p class="price"><?= money($product['price']) ?></p>
      <p class="description"><?= nl2br(h($product['description'])) ?></p>

      <?php if ($product['stock'] > 0): ?>
        <form method="post" action="<?= h(base_url('cart.php')) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

          <label for="quantity">Quantity</label>
          <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= (int) $product['stock'] ?>">

          <button type="submit" class="btn" id="addToCartBtn">Add to Cart</button>
        </form>
        <p class="stock-status in-stock"><?= (int) $product['stock'] ?> in stock</p>
      <?php else: ?>
        <p class="stock-status out-of-stock">Currently out of stock</p>
      <?php endif; ?>

      <ul class="product-features">
        <li>Category:
          <?php if ($product['category_id']): ?>
            <a href="<?= h(base_url('shop.php?category=' . $product['category_id'])) ?>"><?= h($product['category_name']) ?></a>
          <?php else: ?>
            Uncategorized
          <?php endif; ?>
        </li>
        <li>Fast, secure checkout</li>
        <li>Quality guaranteed</li>
      </ul>
    </div>

  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
