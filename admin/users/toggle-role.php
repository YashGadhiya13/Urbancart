<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(base_url('admin/users/index.php'));
}
verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Admins can never demote/promote themselves through this form (prevents
// an admin accidentally locking themselves out of the admin area).
if ($id && $id != $_SESSION['user_id']) {
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $currentRole = $stmt->fetchColumn();

    if ($currentRole) {
        $newRole = $currentRole === 'admin' ? 'member' : 'admin';
        $update = $pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
        $update->execute(['role' => $newRole, 'id' => $id]);
        set_flash('success', 'User role updated to ' . $newRole . '.');
    }
}

redirect(base_url('admin/users/index.php'));
