<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Manage Users';
$adminActive = 'users';

$users = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/../../includes/admin-header.php';
?>
    <table class="cart-table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Role</th>
          <th scope="col">Joined</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= h($user['name']) ?></td>
            <td><?= h($user['email']) ?></td>
            <td><?= h(ucfirst($user['role'])) ?></td>
            <td><?= h(date('d M Y', strtotime($user['created_at']))) ?></td>
            <td>
              <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <form method="post" action="<?= h(base_url('admin/users/toggle-role.php')) ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                  <button type="submit" class="btn-small">
                    Make <?= $user['role'] === 'admin' ? 'Member' : 'Admin' ?>
                  </button>
                </form>
                <form method="post" action="<?= h(base_url('admin/users/delete.php')) ?>" style="display:inline" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                  <button type="submit" class="btn-small btn-remove">Delete</button>
                </form>
              <?php else: ?>
                <em>(you)</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>