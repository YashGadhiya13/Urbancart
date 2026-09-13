<?php
require_once __DIR__ . '/../includes/auth.php';

// Preserve the cart across logout, but clear all authentication state.
$cart = $_SESSION['cart'] ?? null;
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

session_start();
if ($cart) {
    $_SESSION['cart'] = $cart;
}
set_flash('success', 'You have been logged out.');
redirect(base_url('index.php'));
