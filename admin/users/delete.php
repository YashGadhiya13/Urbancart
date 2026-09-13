<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(base_url('admin/users/index.php'));
}
verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id && $id != $_SESSION['user_id']) {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
    set_flash('success', 'User deleted.');
} else {
    set_flash('error', 'You cannot delete your own account while logged in.');
}

redirect(base_url('admin/users/index.php'));
