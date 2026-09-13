<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Contact Messages';
$adminActive = 'messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        set_flash('success', 'Message deleted.');
    }
    redirect(base_url('admin/messages/index.php'));
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/../../includes/admin-header.php';
?>
    <?php if (empty($messages)): ?>
      <p>No messages yet.</p>
    <?php endif; ?>

    <?php foreach ($messages as $msg): ?>
      <div class="value-card" style="text-align:left; margin-bottom:1rem;">
        <h3><?= h($msg['subject']) ?> — <?= h($msg['name']) ?></h3>
        <p><strong>Email:</strong> <?= h($msg['email']) ?><?php if ($msg['order_number']): ?> &nbsp;|&nbsp; <strong>Order #:</strong> <?= h($msg['order_number']) ?><?php endif; ?></p>
        <p><?= nl2br(h($msg['message'])) ?></p>
        <p><em><?= h(date('d M Y, g:i a', strtotime($msg['created_at']))) ?></em></p>
        <form method="post" action="<?= h(base_url('admin/messages/index.php')) ?>" onsubmit="return confirm('Delete this message?');">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int) $msg['id'] ?>">
          <button type="submit" class="btn-small btn-remove">Delete</button>
        </form>
      </div>
    <?php endforeach; ?>
<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>