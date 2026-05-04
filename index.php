<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/functions.php';

$products = get_products(null, '');
?>
<section class="hero card">
    <div>
        <h1>ShopEasy, Smart shopping built for Rwanda</h1>
        <p class="muted">
            ShopEasy brings you a simple and enjoyable way to shop online. Explore quality products, find great offers,
            and shop securely from sellers you can trust. Enjoy shopping with us.
        </p>
        <div class="flex">
            <a class="btn primary" href="products.php">Browse products</a>
            <a class="btn secondary" href="register.php">Create account</a>
        </div>
    </div>
</section>

<h2>Featured products</h2>
<div class="grid products">
    <?php foreach ($products as $p): ?>
        <div class="card product">
            <img src="<?php echo image_url($p['ImageURL']); ?>" alt="<?php echo sanitize($p['Name']); ?>">
            <h3><?php echo sanitize($p['Name']); ?></h3>
            <p class="muted"><?php echo sanitize($p['CategoryName'] ?? ''); ?></p>
            <p><strong><?php echo format_price((float)$p['Price']); ?></strong></p>
            <a class="btn primary" href="customer/product_details.php?id=<?php echo $p['ProductID']; ?>">View</a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

