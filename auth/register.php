<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (is_logged_in()) {
    redirect(base_url('member/dashboard.php'));
}

$pageTitle = 'Register | UrbanCart';
$pageDescription = 'Create a free UrbanCart account to track your orders.';
$activeNav = 'register';
$robotsMeta = 'noindex, follow';

$errors = [];
$values = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $values['name'] = clean($_POST['name'] ?? '');
    $values['email'] = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // --- Server-side validation (authoritative; never trust the client) ---
    if (mb_strlen($values['name']) < 3) {
        $errors['name'] = 'Name must be at least 3 characters.';
    }
    if (!is_valid_email($values['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (mb_strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check for a duplicate email only if the format is already valid.
    if (empty($errors['email'])) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $values['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, "member")');
        $stmt->execute([
            'name'     => $values['name'],
            'email'    => $values['email'],
            'password' => $hash,
        ]);

        set_flash('success', 'Account created! You can now log in.');
        redirect(base_url('auth/login.php'));
    }
}

require __DIR__ . '/../includes/header.php';
?>

  <section class="page-banner">
    <h1>Create an Account</h1>
    <p class="subheading">Register to track your orders and check out faster</p>
  </section>

  <section class="contact-section">
    <form method="post" action="<?= h(base_url('auth/register.php')) ?>" class="contact-form" novalidate>
      <?= csrf_field() ?>
      <h2>Register</h2>

      <label for="name">Full Name <span aria-hidden="true">*</span></label>
      <input type="text" id="name" name="name" required minlength="3" value="<?= h($values['name']) ?>">
      <span class="error-message"><?= h($errors['name'] ?? '') ?></span>

      <label for="email">Email Address <span aria-hidden="true">*</span></label>
      <input type="email" id="email" name="email" required value="<?= h($values['email']) ?>">
      <span class="error-message"><?= h($errors['email'] ?? '') ?></span>

      <label for="password">Password <span aria-hidden="true">*</span></label>
      <input type="password" id="password" name="password" required minlength="8">
      <span class="error-message"><?= h($errors['password'] ?? '') ?></span>

      <label for="confirm_password">Confirm Password <span aria-hidden="true">*</span></label>
      <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
      <span class="error-message"><?= h($errors['confirm_password'] ?? '') ?></span>

      <button type="submit" class="btn">Create Account</button>

      <p>Already have an account? <a href="<?= h(base_url('auth/login.php')) ?>">Log in</a></p>
    </form>
  </section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
