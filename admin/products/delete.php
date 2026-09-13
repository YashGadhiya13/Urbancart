<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(base_url('admin/products/index.php'));
}
verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    set_flash('success', 'Product deleted.');
} else {
    set_flash('error', 'Invalid product.');
}

redirect(base_url('admin/products/index.php'));
