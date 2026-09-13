<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $status = $_POST['status'] ?? '';
    $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (in_array($status, $validStatuses, true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        set_flash('success', 'Order status updated to "' . $status . '".');
    } else {
        set_flash('error', 'Invalid status.');
    }
    redirect(base_url('admin/orders/view.php?id=' . $id));
}

$stmt = $pdo->prepare('SELECT o.*, u.name AS customer_name, u.email AS customer_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = :id');
$stmt->execute(['id' => $id]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('error', 'Order not found.');
    redirect(base_url('admin/orders/index.php'));
}

$pageTitle = 'Order #' . $order['id'];
$adminActive = 'orders';

$itemsStmt = $pdo->prepare('SELECT product_name, price, quantity FROM order_items WHERE order_id = :id');
$itemsStmt->execute(['id' => $id]);
$items = $itemsStmt->fetchAll();

require __DIR__ . '/../../includes/admin-header.php';
?>
    <table class="cart-table">
      <thead>
        <tr><th scope="col">Product</th><th scope="col">Price</th><th scope="col">Qty</th><th scope="col">Subtotal</th></tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= h($item['product_name']) ?></td>
            <td><?= money($item['price']) ?></td>
            <td><?= (int) $item['quantity'] ?></td>
            <td><?= money($item['price'] * $item['quantity']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="cart-summary">
      <h2>Customer &amp; Delivery</h2>
      <p>Customer: <span><?= h($order['customer_name']) ?> (<?= h($order['customer_email']) ?>)</span></p>
      <p>Name on order: <span><?= h($order['full_name']) ?></span></p>
      <p>Address: <span><?= h($order['address']) ?></span></p>
      <p>Phone: <span><?= h($order['phone']) ?></span></p>
      <p>Subtotal: <span><?= money($order['subtotal']) ?></span></p>
      <p>Shipping: <span><?= money($order['shipping']) ?></span></p>
      <p class="total">Total: <span><?= money($order['total']) ?></span></p>
    </div>

    <form method="post" action="<?= h(base_url('admin/orders/view.php?id=' . $id)) ?>" class="contact-form" novalidate>
      <?= csrf_field() ?>
      <label for="status">Update Order Status</label>
      <select id="status" name="status">
        <?php foreach (['pending', 'processing', 'completed', 'cancelled'] as $status): ?>
          <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn">Update Status</button>
    </form>
<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>