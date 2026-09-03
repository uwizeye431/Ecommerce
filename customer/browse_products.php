<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';

$categories = get_categories();
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = sanitize($_GET['q'] ?? '');
$products = get_products($categoryId, $search);
?>
<div class="card">
    <form method="get" class="row">
        <div>
            <label>Search</label>
            <input type="text" name="q" value="<?php echo $search; ?>" placeholder="Search products">
        </div>
        <div>
            <label>Category</label>
            <select name="category">
                <option value="">All</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['CategoryID']; ?>" <?php if ($categoryId === (int)$c['CategoryID']) echo 'selected'; ?>>
                        <?php echo sanitize($c['CategoryName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="align-self:end;">
            <button class="btn primary" type="submit">Filter</button>
        </div>
    </form>
</div>

<div class="grid products">
    <?php foreach ($products as $p): ?>
        <div class="card product">
            <img src="<?php echo image_url($p['ImageURL']); ?>" alt="<?php echo sanitize($p['Name']); ?>">
            <div class="flex space-between">
                <div>
                    <h3><?php echo sanitize($p['Name']); ?></h3>
                    <span class="badge"><?php echo sanitize($p['CategoryName'] ?? ''); ?></span>
                </div>
                <strong><?php echo format_price((float)$p['Price']); ?></strong>
            </div>
            <div class="flex space-between">
                <a class="btn secondary" href="product_details.php?id=<?php echo $p['ProductID']; ?>">Details</a>
                <button class="btn primary" data-add-to-cart data-id="<?php echo $p['ProductID']; ?>">Add</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</main>
</body>
</html>

