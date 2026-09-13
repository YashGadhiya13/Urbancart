<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Manage Products';
$adminActive = 'products';

$products = $pdo->query(
    'SELECT p.id, p.name, p.price, p.stock, p.image, c.name AS category_name
     FROM products p LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.created_at DESC'
)->fetchAll();

require __DIR__ . '/../../includes/admin-header.php';
?>
    <p><a href="<?= h(base_url('admin/products/create.php')) ?>" class="btn-small">+ Add New Product</a></p>

    <table class="cart-table">
      <thead>
        <tr>
          <th scope="col">Image</th>
          <th scope="col">Name</th>
          <th scope="col">Category</th>
          <th scope="col">Price</th>
          <th scope="col">Stock</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product): ?>
          <tr>
            <td><img src="<?= h(base_url('assets/images/' . $product['image'])) ?>" alt="<?= h($product['name']) ?>" loading="lazy" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"></td>
            <td><?= h($product['name']) ?></td>
            <td><?= h($product['category_name'] ?? '—') ?></td>
            <td><?= money($product['price']) ?></td>
            <td><?= (int) $product['stock'] ?></td>
            <td>
              <a href="<?= h(base_url('admin/products/edit.php?id=' . $product['id'])) ?>" class="btn-small">Edit</a>
              <form method="post" action="<?= h(base_url('admin/products/delete.php')) ?>" style="display:inline" onsubmit="return confirm('Delete this product? This cannot be undone.');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                <button type="submit" class="btn-small btn-remove">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>