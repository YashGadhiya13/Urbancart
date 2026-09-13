<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();

$pageTitle = 'Admin Dashboard';
$adminActive = 'dashboard';

$productCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$orderCount   = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$userCount    = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'member'")->fetchColumn();
$revenue      = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$msgCount     = $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();

require __DIR__ . '/../includes/admin-header.php';
?>
    <p class="dashboard-welcome">Welcome back, <?= h($_SESSION['name']) ?>. Here's how UrbanCart is doing today.</p>

    <div class="admin-stats">
      <div class="stat-card">
        <h3><?= (int) $productCount ?></h3>
        <p>Products</p>
      </div>
      <div class="stat-card">
        <h3><?= (int) $orderCount ?></h3>
        <p>Total Orders</p>
      </div>
      <div class="stat-card">
        <h3><?= (int) $pendingCount ?></h3>
        <p>Pending Orders</p>
      </div>
      <div class="stat-card">
        <h3><?= (int) $userCount ?></h3>
        <p>Registered Members</p>
      </div>
      <div class="stat-card">
        <h3><?= money($revenue) ?></h3>
        <p>Total Revenue</p>
      </div>
      <div class="stat-card">
        <h3><?= (int) $msgCount ?></h3>
        <p>Contact Messages</p>
      </div>
    </div>

    <p>Use the sidebar to manage products, orders, users, and contact messages.</p>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>