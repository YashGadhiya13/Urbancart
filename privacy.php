<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Privacy Notice | UrbanCart';
$pageDescription = 'How UrbanCart collects, uses, and protects your personal information.';
$activeNav = '';

require __DIR__ . '/includes/header.php';
?>

  <section class="page-banner">
    <h1>Privacy Notice</h1>
    <p class="subheading">How we collect, use, and protect your information</p>
  </section>

  <section class="about-content" style="display:block;">
    <div class="about-text">

      <h2>What Information We Collect</h2>
      <p>When you use UrbanCart, we may collect the following information:</p>
      <ul class="product-features">
        <li>Your name and email address when you register for an account</li>
        <li>Your delivery address and phone number when you place an order</li>
        <li>Order and purchase history</li>
        <li>Messages you send us through the Contact form</li>
      </ul>

      <h2>Why We Collect It</h2>
      <p>We use this information only for legitimate business purposes, including:</p>
      <ul class="product-features">
        <li>Creating and managing your account</li>
        <li>Processing and delivering your orders</li>
        <li>Responding to your enquiries and support requests</li>
        <li>Improving our website and product range</li>
      </ul>

      <h2>How We Protect Your Information</h2>
      <ul class="product-features">
        <li>Passwords are never stored in plain text — they are hashed with PHP's <code>password_hash()</code> (bcrypt) before being saved.</li>
        <li>All database queries use parameterised/prepared statements to prevent SQL injection.</li>
        <li>Account areas are protected by server-side session checks and role-based access control, so customers cannot access admin pages and vice versa.</li>
        <li>We only collect the minimum information needed to provide our service.</li>
      </ul>

      <h2>Your Rights</h2>
      <p>
        You may update your account details at any time from "My Account", and you may contact us
        at <a href="mailto:support@urbancart.com">support@urbancart.com</a> if you would like your
        data corrected or removed.
      </p>

      <h2>Cookies &amp; Sessions</h2>
      <p>
        UrbanCart uses a session cookie only to keep you logged in and to remember the contents of
        your shopping cart. We do not use tracking or advertising cookies.
      </p>

      <p><em>Last updated: <?= date('F Y') ?></em></p>
    </div>
  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
