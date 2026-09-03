<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';

$id = (int)($_GET['id'] ?? 0);
$product = get_product($id);
if (!$product) {
    echo "<div class='card'>Product not found.</div></main></body></html>";
    exit;
}
?>
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(320px,1fr));">
    <div class="card">
        <img src="<?php echo image_url($product['ImageURL']); ?>" alt="<?php echo sanitize($product['Name']); ?>" style="width:100%;border-radius:12px;">
    </div>
    <div class="card">
        <h2><?php echo sanitize($product['Name']); ?></h2>
        <p class="muted">Stock: <?php echo (int)$product['Stock']; ?></p>
        <p><strong><?php echo format_price((float)$product['Price']); ?></strong></p>
        <p><?php echo sanitize($product['Description'] ?? 'Quality product.'); ?></p>
        <div class="flex">
            <button class="btn primary" data-add-to-cart data-id="<?php echo $product['ProductID']; ?>">Add to cart</button>
            <a class="btn secondary" href="browse_products.php">Back</a>
        </div>
    </div>
</div>
<script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</main>
</body>
</html>

