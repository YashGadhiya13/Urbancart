<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (is_logged_in()) {
    redirect(is_admin() ? base_url('admin/dashboard.php') : base_url('member/dashboard.php'));
}

$pageTitle = 'Login | UrbanCart';
$pageDescription = 'Log in to your UrbanCart account.';
$activeNav = 'login';
$robotsMeta = 'noindex, follow';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!is_valid_email($email) || $password === '') {
        $errors['general'] = 'Invalid email or password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Deliberately use the same generic message whether the email
        // doesn't exist or the password is wrong, so we don't leak which
        // emails are registered.
        if (!$user || !password_verify($password, $user['password'])) {
            $errors['general'] = 'Invalid email or password.';
        } else {
            session_regenerate_id(true); // prevent session fixation
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            set_flash('success', 'Welcome back, ' . $user['name'] . '!');

            $redirectTo = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);

            if ($redirectTo) {
                redirect($redirectTo);
            }
            redirect($user['role'] === 'admin' ? base_url('admin/dashboard.php') : base_url('member/dashboard.php'));
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>

  <section class="page-banner">
    <h1>Login</h1>
    <p class="subheading">Welcome back to UrbanCart</p>
  </section>

  <section class="contact-section">
    <form method="post" action="<?= h(base_url('auth/login.php')) ?>" class="contact-form" novalidate>
      <?= csrf_field() ?>
      <h2>Log In</h2>

      <?php if (!empty($errors['general'])): ?>
        <p class="error-message"><?= h($errors['general']) ?></p>
      <?php endif; ?>

      <label for="email">Email Address <span aria-hidden="true">*</span></label>
      <input type="email" id="email" name="email" required value="<?= h($email) ?>">

      <label for="password">Password <span aria-hidden="true">*</span></label>
      <input type="password" id="password" name="password" required>

      <button type="submit" class="btn">Log In</button>

      <p>Don't have an account? <a href="<?= h(base_url('auth/register.php')) ?>">Register</a></p>
      <p class="footer-links">Demo accounts — Admin: admin@urbancart.com / Admin@123 &nbsp;|&nbsp; Member: jane@urbancart.com / Member@123</p>
    </form>
  </section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
