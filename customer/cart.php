<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$items = get_cart_items(current_user_id());
$total = 0;
foreach ($items as $item) {
    $total += $item['Price'] * $item['Quantity'];
}
?>
<div class="card">
    <h2>Your cart</h2>
    <?php if (!$items): ?>
        <p class="muted">Cart is empty.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($items as $i): ?>
                <tr>
                    <td><?php echo sanitize($i['Name']); ?></td>
                    <td><?php echo (int)$i['Quantity']; ?></td>
                    <td><?php echo format_price((float)$i['Price']); ?></td>
                    <td><?php echo format_price((float)$i['Price'] * (int)$i['Quantity']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="flex space-between" style="margin-top:14px;">
            <strong>Total: <?php echo format_price((float)$total); ?></strong>
            <a class="btn primary" href="checkout.php">Checkout</a>
        </div>
    <?php endif; ?>
</div>
</main>
</body>
</html>

