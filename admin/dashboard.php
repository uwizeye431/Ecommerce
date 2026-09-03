<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$pdo = getPDO();
$totals = [
    'users' => $pdo->query('SELECT COUNT(*) FROM Users')->fetchColumn(),
    'products' => $pdo->query('SELECT COUNT(*) FROM Products')->fetchColumn(),
    'orders' => $pdo->query('SELECT COUNT(*) FROM Orders')->fetchColumn(),
    'sales' => $pdo->query('SELECT IFNULL(SUM(TotalPrice),0) FROM Orders')->fetchColumn(),
];
?>
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px,1fr));">
    <div class="card"><h3>Users</h3><p class="muted"><?php echo $totals['users']; ?></p></div>
    <div class="card"><h3>Products</h3><p class="muted"><?php echo $totals['products']; ?></p></div>
    <div class="card"><h3>Orders</h3><p class="muted"><?php echo $totals['orders']; ?></p></div>
    <div class="card"><h3>Sales</h3><p class="muted"><?php echo format_price((float)$totals['sales']); ?></p></div>
</div>

<div class="card">
    <h2>Admin shortcuts</h2>
    <div class="flex">
        <a class="btn primary" href="manage_products.php">Products</a>
        <a class="btn primary" href="manage_orders.php">Orders</a>
        <a class="btn primary" href="manage_users.php">Users</a>
        <a class="btn primary" href="manage_payments.php">Payments</a>
        <a class="btn secondary" href="view_reports.php">Reports</a>
    </div>
</div>
</main>
</body>
</html>

