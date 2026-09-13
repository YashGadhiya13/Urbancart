<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'About Us | UrbanCart';
$pageDescription = 'Learn about UrbanCart - our story, mission, and commitment to quality online shopping.';
$activeNav = 'about';

require __DIR__ . '/includes/header.php';
?>

  <section class="page-banner">
    <h1>About Us</h1>
    <p class="subheading">Get to know the team behind UrbanCart</p>
  </section>

  <section class="about-content">
    <div class="about-text">
      <h2>Our Story</h2>
      <p>
        UrbanCart started with a simple idea: make online shopping easy, affordable,
        and enjoyable for everyone. Today, we offer a wide range of fashion, electronics,
        and accessories, carefully selected to bring quality and style to our customers
        at the best possible prices.
      </p>
    </div>
    <div class="about-img">
      <div class="about-photo-frame">
        <img src="<?= h(base_url('assets/images/about/team.jpg')) ?>" alt="UrbanCart team working in office" width="1200" height="800" loading="lazy">
      </div>
    </div>
  </section>

  <section class="values">
    <div class="value-card">
      <h3>Our Mission</h3>
      <p>To provide quality products with fast delivery and excellent customer service.</p>
    </div>
    <div class="value-card">
      <h3>Our Vision</h3>
      <p>To become a trusted online shopping destination known for value and reliability.</p>
    </div>
    <div class="value-card">
      <h3>Our Values</h3>
      <p>Quality, honesty, customer satisfaction, and continuous improvement.</p>
    </div>
  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>