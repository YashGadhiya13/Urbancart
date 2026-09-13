<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Add New Product';
$adminActive = 'products';

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$errors = [];
$values = ['name' => '', 'category_id' => '', 'price' => '', 'stock' => '', 'description' => '', 'featured' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $values['name'] = clean($_POST['name'] ?? '');
    $values['category_id'] = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $values['price'] = $_POST['price'] ?? '';
    $values['stock'] = $_POST['stock'] ?? '';
    $values['description'] = clean($_POST['description'] ?? '');
    $values['featured'] = isset($_POST['featured']);

    if (mb_strlen($values['name']) < 2) {
        $errors['name'] = 'Product name is required.';
    }
    if (!$values['category_id']) {
        $errors['category_id'] = 'Please choose a category.';
    }
    if (!is_numeric($values['price']) || $values['price'] < 0) {
        $errors['price'] = 'Price must be a positive number.';
    }
    if (!ctype_digit((string) $values['stock']) ) {
        $errors['stock'] = 'Stock must be a whole number.';
    }
    if (mb_strlen($values['description']) < 5) {
        $errors['description'] = 'Please add a short description.';
    }

    // Image upload (optional but validated when provided).
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $result = handle_product_image_upload($_FILES['image'], __DIR__ . '/../../assets/images/');
        if ($result['error']) {
            $errors['image'] = $result['error'];
        } else {
            $imagePath = $result['path'];
        }
    } else {
        $errors['image'] = 'Please choose a product image.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO products (category_id, name, description, price, image, stock, featured)
             VALUES (:category_id, :name, :description, :price, :image, :stock, :featured)'
        );
        $stmt->execute([
            'category_id' => $values['category_id'],
            'name'        => $values['name'],
            'description' => $values['description'],
            'price'       => $values['price'],
            'image'       => $imagePath,
            'stock'       => (int) $values['stock'],
            'featured'    => $values['featured'] ? 1 : 0,
        ]);
        set_flash('success', 'Product "' . $values['name'] . '" was added.');
        redirect(base_url('admin/products/index.php'));
    }
}

require __DIR__ . '/../../includes/admin-header.php';
?>
    <form method="post" action="<?= h(base_url('admin/products/create.php')) ?>" class="contact-form" enctype="multipart/form-data" novalidate>
      <?= csrf_field() ?>

      <label for="name">Product Name <span aria-hidden="true">*</span></label>
      <input type="text" id="name" name="name" required value="<?= h($values['name']) ?>">
      <span class="error-message"><?= h($errors['name'] ?? '') ?></span>

      <label for="category_id">Category <span aria-hidden="true">*</span></label>
      <select id="category_id" name="category_id" required>
        <option value="">-- Select a category --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int) $cat['id'] ?>" <?= $values['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= h($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <span class="error-message"><?= h($errors['category_id'] ?? '') ?></span>

      <label for="price">Price (USD) <span aria-hidden="true">*</span></label>
      <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= h($values['price']) ?>">
      <span class="error-message"><?= h($errors['price'] ?? '') ?></span>

      <label for="stock">Stock Quantity <span aria-hidden="true">*</span></label>
      <input type="number" id="stock" name="stock" min="0" required value="<?= h($values['stock']) ?>">
      <span class="error-message"><?= h($errors['stock'] ?? '') ?></span>

      <label for="description">Description <span aria-hidden="true">*</span></label>
      <textarea id="description" name="description" rows="4" required><?= h($values['description']) ?></textarea>
      <span class="error-message"><?= h($errors['description'] ?? '') ?></span>

      <label for="image">Product Image (JPG/PNG/WEBP, max 2MB) <span aria-hidden="true">*</span></label>
      <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
      <span class="error-message"><?= h($errors['image'] ?? '') ?></span>

      <label>
        <input type="checkbox" name="featured" <?= $values['featured'] ? 'checked' : '' ?> style="width:auto;display:inline-block;">
        Show on homepage as a featured product
      </label>

      <button type="submit" class="btn">Save Product</button>
    </form>
<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>