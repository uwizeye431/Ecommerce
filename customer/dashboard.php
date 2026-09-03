<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$orders = get_orders_by_customer(current_user_id());
$cartCount = count(get_cart_items(current_user_id()));
?>
<div class="grid">
    <div class="card">
        <h2>Welcome, <?php echo sanitize($_SESSION['user']['Username']); ?></h2>
        <p class="muted">Track your activity and continue shopping.</p>
        <div class="flex">
            <a class="btn primary" href="browse_products.php">Browse products</a>
            <a class="btn secondary" href="cart.php">Cart (<?php echo $cartCount; ?>)</a>
        </div>
    </div>
    <div class="card">
        <h3>Recent orders</h3>
        <table>
            <thead><tr><th>ID</th><th>Status</th><th>Total</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?php echo $o['OrderID']; ?></td>
                    <td><?php echo sanitize($o['Status']); ?></td>
                    <td><?php echo format_price((float)$o['TotalPrice']); ?></td>
                    <td><?php echo $o['OrderDate']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>
</body>
</html>

