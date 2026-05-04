<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$pdo = getPDO();
// sales by day
$salesDaily = $pdo->query('SELECT DATE(OrderDate) as d, SUM(TotalPrice) total FROM Orders GROUP BY DATE(OrderDate) ORDER BY d DESC LIMIT 7')->fetchAll();
// low stock
$stock = $pdo->query('SELECT Name, Stock FROM Products ORDER BY Stock ASC LIMIT 5')->fetchAll();
// payments summary
$payments = $pdo->query('SELECT Status, COUNT(*) count FROM Payments GROUP BY Status')->fetchAll();
// top-selling products
$topProducts = $pdo->query('SELECT Products.Name, SUM(OrderItems.Quantity) qty
    FROM OrderItems 
    JOIN Products ON OrderItems.ProductID = Products.ProductID
    GROUP BY Products.ProductID, Products.Name
    ORDER BY qty DESC
    LIMIT 5')->fetchAll();
// customer purchases
$topCustomers = $pdo->query('SELECT Users.Username, COUNT(Orders.OrderID) orders, SUM(Orders.TotalPrice) total
    FROM Orders
    JOIN Users ON Orders.CustomerID = Users.UserID
    GROUP BY Users.UserID, Users.Username
    ORDER BY total DESC
    LIMIT 5')->fetchAll();
?>
<div class="grid">
    <div class="card">
        <h3>Sales (daily)</h3>
        <?php foreach ($salesDaily as $row): ?>
            <div class="flex space-between"><span><?php echo $row['d']; ?></span><strong><?php echo format_price((float)$row['total']); ?></strong></div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h3>Low stock</h3>
        <?php foreach ($stock as $s): ?>
            <div class="flex space-between"><span><?php echo sanitize($s['Name']); ?></span><span class="badge"><?php echo $s['Stock']; ?> left</span></div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h3>Payments</h3>
        <?php foreach ($payments as $p): ?>
            <div class="flex space-between"><span><?php echo sanitize($p['Status']); ?></span><span class="badge"><?php echo $p['count']; ?></span></div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h3>Top products</h3>
        <?php foreach ($topProducts as $tp): ?>
            <div class="flex space-between"><span><?php echo sanitize($tp['Name']); ?></span><span class="badge"><?php echo $tp['qty']; ?> sold</span></div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h3>Top customers</h3>
        <?php foreach ($topCustomers as $tc): ?>
            <div class="flex space-between">
                <span><?php echo sanitize($tc['Username']); ?></span>
                <span class="badge"><?php echo $tc['orders']; ?> orders · <?php echo format_price((float)$tc['total']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

