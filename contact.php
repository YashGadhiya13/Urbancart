<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Contact Us | UrbanCart';
$pageDescription = 'Get in touch with UrbanCart for support, orders, or inquiries.';
$activeNav = 'contact';

$errors = [];
$values = ['name' => '', 'email' => '', 'orderNumber' => '', 'subject' => '', 'message' => ''];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $values['name'] = clean($_POST['name'] ?? '');
    $values['email'] = clean($_POST['email'] ?? '');
    $values['orderNumber'] = clean($_POST['orderNumber'] ?? '');
    $values['subject'] = clean($_POST['subject'] ?? '');
    $values['message'] = clean($_POST['message'] ?? '');

    // Server-side validation (never trust the browser alone).
    if (mb_strlen($values['name']) < 3) {
        $errors['name'] = 'Please enter your full name (at least 3 characters).';
    }
    if (!is_valid_email($values['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    $validSubjects = ['order', 'return', 'general'];
    if (!in_array($values['subject'], $validSubjects, true)) {
        $errors['subject'] = 'Please select a subject.';
    }
    if (mb_strlen($values['message']) < 10) {
        $errors['message'] = 'Your message must be at least 10 characters.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO contact_messages (name, email, order_number, subject, message)
             VALUES (:name, :email, :order_number, :subject, :message)'
        );
        $stmt->execute([
            'name'         => $values['name'],
            'email'        => $values['email'],
            'order_number' => $values['orderNumber'] ?: null,
            'subject'      => $values['subject'],
            'message'      => $values['message'],
        ]);
        $success = true;
        $values = ['name' => '', 'email' => '', 'orderNumber' => '', 'subject' => '', 'message' => ''];
    }
}

require __DIR__ . '/includes/header.php';
?>

  <section class="page-banner">
    <h1>Contact Us</h1>
    <p class="subheading">We're here to help with your orders and questions</p>
  </section>

  <section class="contact-section">

    <div class="contact-info">
      <h2>Get In Touch</h2>
      <p><strong>Email:</strong> <a href="mailto:support@urbancart.com">support@urbancart.com</a></p>
      <p><strong>Phone:</strong> +92 300 1234567</p>
      <p><strong>Address:</strong> 45 Market Street, Islamabad, Pakistan</p>
      <div class="social-links">
        <a href="#" aria-label="Facebook">Facebook</a>
        <a href="#" aria-label="Instagram">Instagram</a>
        <a href="#" aria-label="LinkedIn">LinkedIn</a>
      </div>
    </div>

    <form id="contactForm" method="post" action="<?= h(base_url('contact.php')) ?>" class="contact-form" novalidate>
      <?= csrf_field() ?>
      <h2>Send Us a Message</h2>

      <label for="name">Full Name <span aria-hidden="true">*</span></label>
      <input type="text" id="name" name="name" placeholder="Enter your full name" required minlength="3" value="<?= h($values['name']) ?>">
      <span class="error-message" id="nameError"><?= h($errors['name'] ?? '') ?></span>

      <label for="email">Email Address <span aria-hidden="true">*</span></label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?= h($values['email']) ?>">
      <span class="error-message" id="emailError"><?= h($errors['email'] ?? '') ?></span>

      <label for="orderNumber">Order Number (if applicable)</label>
      <input type="text" id="orderNumber" name="orderNumber" placeholder="e.g. UC-10234" value="<?= h($values['orderNumber']) ?>">
      <span class="error-message" id="orderNumberError"></span>

      <label for="subject">Subject <span aria-hidden="true">*</span></label>
      <select id="subject" name="subject" required>
        <option value="">-- Select a subject --</option>
        <option value="order" <?= $values['subject'] === 'order' ? 'selected' : '' ?>>Order Inquiry</option>
        <option value="return" <?= $values['subject'] === 'return' ? 'selected' : '' ?>>Return / Refund</option>
        <option value="general" <?= $values['subject'] === 'general' ? 'selected' : '' ?>>General Question</option>
      </select>
      <span class="error-message" id="subjectError"><?= h($errors['subject'] ?? '') ?></span>

      <label for="message">Message <span aria-hidden="true">*</span></label>
      <textarea id="message" name="message" rows="5" placeholder="Write your message..." required minlength="10"><?= h($values['message']) ?></textarea>
      <span class="error-message" id="messageError"><?= h($errors['message'] ?? '') ?></span>

      <button type="submit" class="btn">Submit</button>

      <p id="formSuccess" class="success-message" role="status">
        <?= $success ? 'Thank you! Your message has been sent — we will get back to you soon.' : '' ?>
      </p>
    </form>

  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
