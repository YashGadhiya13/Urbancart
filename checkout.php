<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

require_login(); // members and admins only - guests must log in first

$pageTitle = 'Checkout | UrbanCart';
$pageDescription = 'Complete your UrbanCart order.';
$activeNav = 'cart';
$robotsMeta = 'noindex, nofollow';

if (empty($_SESSION['cart'])) {
    set_flash('error', 'Your cart is empty.');
    redirect(base_url('shop.php'));
}

$errors = [];
$fullName = $_SESSION['name'] ?? '';
$address = '';
$phone = '';

// Reload cart contents + current stock levels.
function load_cart_items(PDO $pdo, array $cart) {
    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $items = [];
    foreach ($stmt->fetchAll() as $row) {
        $qty = $cart[$row['id']];
        $items[] = $row + ['quantity' => $qty];
    }
    return $items;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $fullName = clean($_POST['full_name'] ?? '');
    $address  = clean($_POST['address'] ?? '');
    $phone    = clean($_POST['phone'] ?? '');

    if (mb_strlen($fullName) < 3) {
        $errors['full_name'] = 'Please enter your full name (at least 3 characters).';
    }
    if (mb_strlen($address) < 5) {
        $errors['address'] = 'Please enter a complete delivery address.';
    }
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    $items = load_cart_items($pdo, $_SESSION['cart']);
    if (empty($items)) {
        $errors['cart'] = 'Your cart is empty.';
    }
    foreach ($items as $item) {
        if ($item['quantity'] > $item['stock']) {
            $errors['cart'] = 'Sorry, "' . $item['name'] . '" no longer has enough stock. Please update your cart.';
        }
    }

    if (empty($errors)) {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $shipping = 5.00;
        $total = $subtotal + $shipping;

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO orders (user_id, full_name, address, phone, subtotal, shipping, total, status)
                 VALUES (:user_id, :full_name, :address, :phone, :subtotal, :shipping, :total, "pending")'
            );
            $stmt->execute([
                'user_id'  => $_SESSION['user_id'],
                'full_name' => $fullName,
                'address'  => $address,
                'phone'    => $phone,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total'    => $total,
            ]);
            $orderId = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
                 VALUES (:order_id, :product_id, :product_name, :price, :quantity)'
            );
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - :qty WHERE id = :id');

            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id'     => $orderId,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                ]);
                $stockStmt->execute(['qty' => $item['quantity'], 'id' => $item['id']]);
            }

            $pdo->commit();
            unset($_SESSION['cart']);

            set_flash('success', 'Thank you! Your order #' . $orderId . ' has been placed.');
            redirect(base_url('member/orders.php'));
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Checkout failed: ' . $e->getMessage());
            $errors['general'] = 'Something went wrong while placing your order. Please try again.';
        }
    }
}

$items = load_cart_items($pdo, $_SESSION['cart']);
$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
$shipping = 5.00;
$total = $subtotal + $shipping;

require __DIR__ . '/includes/header.php';
?>

  <section class="page-banner">
    <h1>Checkout</h1>
    <p class="subheading">Enter your delivery details to complete your order</p>
  </section>

  <section class="contact-section">
    <div class="cart-summary">
      <h2>Order Summary</h2>
      <ul>
        <?php foreach ($items as $item): ?>
          <li><?= h($item['name']) ?> &times; <?= (int) $item['quantity'] ?> — <?= money($item['price'] * $item['quantity']) ?></li>
        <?php endforeach; ?>
      </ul>
      <p>Subtotal: <span><?= money($subtotal) ?></span></p>
      <p>Shipping: <span><?= money($shipping) ?></span></p>
      <p class="total">Total: <span><?= money($total) ?></span></p>
    </div>

    <form method="post" action="<?= h(base_url('checkout.php')) ?>" class="contact-form" novalidate>
      <?= csrf_field() ?>
      <h2>Delivery Details</h2>

      <?php if (!empty($errors['general'])): ?>
        <p class="error-message"><?= h($errors['general']) ?></p>
      <?php endif; ?>
      <?php if (!empty($errors['cart'])): ?>
        <p class="error-message"><?= h($errors['cart']) ?></p>
      <?php endif; ?>

      <label for="full_name">Full Name <span aria-hidden="true">*</span></label>
      <input type="text" id="full_name" name="full_name" required minlength="3" value="<?= h($fullName) ?>">
      <span class="error-message"><?= h($errors['full_name'] ?? '') ?></span>

      <label for="address">Delivery Address <span aria-hidden="true">*</span></label>
      <textarea id="address" name="address" rows="3" required minlength="5"><?= h($address) ?></textarea>
      <span class="error-message"><?= h($errors['address'] ?? '') ?></span>

      <label for="phone">Phone Number <span aria-hidden="true">*</span></label>
      <input type="tel" id="phone" name="phone" required value="<?= h($phone) ?>">
      <span class="error-message"><?= h($errors['phone'] ?? '') ?></span>

      <button type="submit" class="btn">Place Order</button>
    </form>
  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
