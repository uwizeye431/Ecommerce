<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$pdo = getPDO();
$message = '';
$categories = get_categories();

if (is_post()) {
    $action = $_POST['action'] ?? '';
    // handle file upload if present
    $imagePath = trim($_POST['image'] ?? '');
    if (!empty($_FILES['image_file']['name'])) {
        $uploadDir = __DIR__ . '/../assets/images/';
        @mkdir($uploadDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image_file']['name']);
        $target = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target)) {
            $imagePath = 'assets/images/' . $filename;
        }
    }

    if ($action === 'create') {
        $pdo->prepare('INSERT INTO Products (Name, Price, Stock, CategoryID, ImageURL) VALUES (?, ?, ?, ?, ?)')
            ->execute([sanitize($_POST['name']), (float)$_POST['price'], (int)$_POST['stock'], (int)$_POST['category_id'], $imagePath]);
        $message = 'Product added.';
    } elseif ($action === 'update') {
        if ($imagePath === '' && isset($_POST['existing_image'])) {
            $imagePath = sanitize($_POST['existing_image']);
        }
        $pdo->prepare('UPDATE Products SET Name=?, Price=?, Stock=?, CategoryID=?, ImageURL=? WHERE ProductID=?')
            ->execute([sanitize($_POST['name']), (float)$_POST['price'], (int)$_POST['stock'], (int)$_POST['category_id'], $imagePath, (int)$_POST['id']]);
        $message = 'Product updated.';
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM Products WHERE ProductID=?')->execute([(int)$_POST['id']]);
        $message = 'Product deleted.';
    }
}
$products = get_products();
?>
<div class="card">
    <h2>Manage products</h2>
    <?php if ($message): ?><div class="alert success"><?php echo $message; ?></div><?php endif; ?>
    <form method="post" class="row" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div><label>Name</label><input name="name" required></div>
        <div><label>Price (RWF)</label><input type="number" step="0.01" name="price" required></div>
        <div><label>Stock</label><input type="number" name="stock" required></div>
        <div><label>Category</label>
            <select name="category_id">
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['CategoryID']; ?>"><?php echo sanitize($c['CategoryName']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Image</label>
            <input name="image" placeholder="assets/images/..." />
            <input type="file" name="image_file" accept="image/*">
        </div>
        <div style="align-self:end;"><button class="btn primary">Add</button></div>
    </form>
</div>

<div class="card">
    <h3>Existing products</h3>
    <table>
        <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Category</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><img src="<?php echo image_url($p['ImageURL']); ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px;"></td>
                <td><?php echo sanitize($p['Name']); ?></td>
                <td><?php echo format_price((float)$p['Price']); ?></td>
                <td><?php echo (int)$p['Stock']; ?></td>
                <td><?php echo sanitize($p['CategoryName']); ?></td>
                <td>
                    <form method="post" class="flex" style="gap:6px;" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $p['ProductID']; ?>">
                        <input type="hidden" name="name" value="<?php echo sanitize($p['Name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $p['Price']; ?>">
                        <input type="hidden" name="stock" value="<?php echo $p['Stock']; ?>">
                        <input type="hidden" name="category_id" value="<?php echo $p['CategoryID']; ?>">
                        <input type="hidden" name="existing_image" value="<?php echo sanitize($p['ImageURL']); ?>">
                        <button name="action" value="update" class="btn secondary">Save</button>
                        <button name="action" value="delete" class="btn danger" onclick="return confirm('Delete product?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>
</body>
</html>

