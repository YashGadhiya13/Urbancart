<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Shopping Cart | UrbanCart';
$pageDescription = 'View items in your UrbanCart shopping cart.';
$activeNav = 'cart';
$robotsMeta = 'noindex, nofollow';

$_SESSION['cart'] = $_SESSION['cart'] ?? []; // [product_id => quantity]

// Handle add / update / remove actions (all POST, all CSRF-protected).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if ($action === 'add' && $productId) {
        $qty = max(1, (int) ($_POST['quantity'] ?? 1));

        // Confirm the product exists and check stock before adding.
        $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id');
        $stmt->execute(['id' => $productId]);
        $stock = $stmt->fetchColumn();

        if ($stock === false) {
            set_flash('error', 'That product could not be found.');
        } else {
            $newQty = ($_SESSION['cart'][$productId] ?? 0) + $qty;
            $_SESSION['cart'][$productId] = min($newQty, (int) $stock);
            set_flash('success', 'Item added to your cart.');
        }
    } elseif ($action === 'update' && $productId) {
        $qty = (int) ($_POST['quantity'] ?? 1);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $qty;
        }
        set_flash('success', 'Cart updated.');
    } elseif ($action === 'remove' && $productId) {
        unset($_SESSION['cart'][$productId]);
        set_flash('success', 'Item removed from your cart.');
    }

    redirect(base_url('cart.php'));
}

// Load full product details for whatever is currently in the cart.
$cartItems = [];
$subtotal = 0;
if (!empty($_SESSION['cart'])) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, image, stock FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $row) {
        $qty = $_SESSION['cart'][$row['id']];
        $lineTotal = $qty * $row['price'];
        $subtotal += $lineTotal;
        $cartItems[] = $row + ['quantity' => $qty, 'line_total' => $lineTotal];
    }
}
$shippingCost = $subtotal > 0 ? 5.00 : 0.00;
$total = $subtotal + $shippingCost;

require __DIR__ . '/includes/header.php';
?>

  <section class="page-banner">
    <h1>Your Shopping Cart</h1>
    <p class="subheading">Review your items before checkout</p>
  </section>

  <section class="cart-section">

    <?php if (empty($cartItems)): ?>
      <div class="empty-state">
        <span class="empty-state-icon" aria-hidden="true">&#128722;</span>
        <h2>Your Cart is Empty</h2>
        <p>Looks like you haven't added anything to your cart yet.</p>
        <a href="<?= h(base_url('shop.php')) ?>" class="btn">Continue Shopping</a>
      </div>
    <?php else: ?>
      <table class="cart-table">
        <thead>
          <tr>
            <th scope="col">Product</th>
            <th scope="col">Price</th>
            <th scope="col">Quantity</th>
            <th scope="col">Subtotal</th>
            <th scope="col">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartItems as $item): ?>
            <tr>
              <td class="cart-product">
                <img src="<?= h(base_url('assets/images/' . $item['image'])) ?>" alt="<?= h($item['name']) ?>" loading="lazy">
                <span><?= h($item['name']) ?></span>
              </td>
              <td><?= money($item['price']) ?></td>
              <td>
                <form method="post" action="<?= h(base_url('cart.php')) ?>" class="cart-qty-form">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                  <label for="qty-<?= (int) $item['id'] ?>" class="visually-hidden">Quantity for <?= h($item['name']) ?></label>
                  <input type="number" id="qty-<?= (int) $item['id'] ?>" name="quantity" value="<?= (int) $item['quantity'] ?>" min="1" max="<?= (int) $item['stock'] ?>">
                  <button type="submit" class="btn-small">Update</button>
                </form>
              </td>
              <td><?= money($item['line_total']) ?></td>
              <td>
                <form method="post" action="<?= h(base_url('cart.php')) ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="remove">
                  <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                  <button type="submit" class="btn-small btn-remove">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="cart-summary">
        <h2>Order Summary</h2>
        <p>Subtotal: <span><?= money($subtotal) ?></span></p>
        <p>Shipping: <span><?= money($shippingCost) ?></span></p>
        <p class="total">Total: <span><?= money($total) ?></span></p>
        <a href="<?= h(base_url('checkout.php')) ?>" class="btn">Proceed to Checkout</a>
      </div>
    <?php endif; ?>

  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>