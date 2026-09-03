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

<script src="assets/js/script.js"></script>
</main>
<footer style="background:#020617;padding:24px 16px;margin-top:40px;border-top:1px solid #1f2937;">
    <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;align-items:flex-start;">
        <div>
            <h3>About ShopEasy</h3>
            <p class="muted">
                We connect small businesses with customers across Rwanda, making online shopping easy,
                secure, and accessible for everyone. Supporting local businesses since 2025.
            </p>
        </div>
        <div>
            <h3>Quick Links</h3>
            <p><a href="<?php echo APP_URL; ?>/index.php">Home</a></p>
            <p><a href="<?php echo APP_URL; ?>/products.php">Products</a></p>
            <p><a href="<?php echo APP_URL; ?>/about.php">About Us</a></p>
            <p><a href="<?php echo APP_URL; ?>/contact.php">Contact</a></p>
            <p><a href="<?php echo APP_URL; ?>/login.php">Login / Register</a></p>
        </div>
        <div>
            <h3>Contact</h3>
            <p>Email: <a href="mailto:support@platform.com">support@platform.com</a></p>
            <p>Phone: <a href="tel:+250788775937">+250788775937</a></p>
            <p>Address: RWANDA / GASABO</p>
        </div>
        <div style="grid-column:1 / -1;text-align:center;margin-top:12px;">
            <p class="muted">"Where businesses grow and customers shop with confidence."</p>
        </div>
    </div>
</footer>
</body>
</html>

