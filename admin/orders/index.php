<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Manage Orders';
$adminActive = 'orders';

$orders = $pdo->query(
    'SELECT o.id, o.total, o.status, o.created_at, u.name AS customer_name
     FROM orders o JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC'
)->fetchAll();

require __DIR__ . '/../../includes/admin-header.php';
?>
    <table class="cart-table">
      <thead>
        <tr>
          <th scope="col">Order #</th>
          <th scope="col">Customer</th>
          <th scope="col">Date</th>
          <th scope="col">Total</th>
          <th scope="col">Status</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td>#<?= (int) $order['id'] ?></td>
            <td><?= h($order['customer_name']) ?></td>
            <td><?= h(date('d M Y', strtotime($order['created_at']))) ?></td>
            <td><?= money($order['total']) ?></td>
            <td><span class="status-badge status-<?= h($order['status']) ?>"><?= h(ucfirst($order['status'])) ?></span></td>
            <td><a href="<?= h(base_url('admin/orders/view.php?id=' . $order['id'])) ?>" class="btn-small">View / Update</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>