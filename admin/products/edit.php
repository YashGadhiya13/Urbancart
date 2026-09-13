<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Product not found.');
    redirect(base_url('admin/products/index.php'));
}

$pageTitle = 'Edit Product: ' . $product['name'];
$adminActive = 'products';
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$errors = [];
$values = [
    'name' => $product['name'],
    'category_id' => $product['category_id'],
    'price' => $product['price'],
    'stock' => $product['stock'],
    'description' => $product['description'],
    'featured' => (bool) $product['featured'],
];
$imagePath = $product['image'];

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
    if (!ctype_digit((string) $values['stock'])) {
        $errors['stock'] = 'Stock must be a whole number.';
    }
    if (mb_strlen($values['description']) < 5) {
        $errors['description'] = 'Please add a short description.';
    }

    if (!empty($_FILES['image']['name'])) {
        $result = handle_product_image_upload($_FILES['image'], __DIR__ . '/../../assets/images/');
        if ($result['error']) {
            $errors['image'] = $result['error'];
        } else {
            $imagePath = $result['path'];
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE products SET category_id = :category_id, name = :name, description = :description,
             price = :price, image = :image, stock = :stock, featured = :featured WHERE id = :id'
        );
        $stmt->execute([
            'category_id' => $values['category_id'],
            'name'        => $values['name'],
            'description' => $values['description'],
            'price'       => $values['price'],
            'image'       => $imagePath,
            'stock'       => (int) $values['stock'],
            'featured'    => $values['featured'] ? 1 : 0,
            'id'          => $id,
        ]);
        set_flash('success', 'Product updated.');
        redirect(base_url('admin/products/index.php'));
    }
}

require __DIR__ . '/../../includes/admin-header.php';
?>
    <form method="post" action="<?= h(base_url('admin/products/edit.php?id=' . $id)) ?>" class="contact-form" enctype="multipart/form-data" novalidate>
      <?= csrf_field() ?>

      <img src="<?= h(base_url('assets/images/' . $imagePath)) ?>" alt="Current image for <?= h($values['name']) ?>" style="width:120px;border-radius:8px;margin-bottom:1rem;">

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

      <label for="image">Replace Image (optional, JPG/PNG/WEBP, max 2MB)</label>
      <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
      <span class="error-message"><?= h($errors['image'] ?? '') ?></span>

      <label>
        <input type="checkbox" name="featured" <?= $values['featured'] ? 'checked' : '' ?> style="width:auto;display:inline-block;">
        Show on homepage as a featured product
      </label>

      <button type="submit" class="btn">Update Product</button>
    </form>
<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>