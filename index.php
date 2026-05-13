<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch the list of featured products
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
    <?php foreach ($products as $product): ?>
        <div class="card product">
            <img src="<?php echo image_url($product['ImageURL']); ?>" alt="<?php echo sanitize($product['Name']); ?>">
            <h3><?php echo sanitize($product['Name']); ?></h3>
            <p class="muted"><?php echo sanitize($product['CategoryName'] ?? ''); ?></p>
            <p><strong><?php echo format_price((float)$product['Price']); ?></strong></p>
            <a class="btn primary" href="customer/product_details.php?id=<?php echo $product['ProductID']; ?>">View</a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

