<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$pdo = getPDO();
$orderId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT Orders.*, Users.Username, Users.Email FROM Orders JOIN Users ON Orders.CustomerID = Users.UserID WHERE Orders.OrderID=?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();
if (!$order) {
    echo "<div class='card'>Order not found.</div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
$items = get_order_items($orderId);
$paymentsStmt = $pdo->prepare('SELECT * FROM Payments WHERE OrderID=?');
$paymentsStmt->execute([$orderId]);
$payments = $paymentsStmt->fetchAll();
?>
<div class="card">
    <h2>Order #<?php echo $order['OrderID']; ?></h2>
    <p class="muted">Customer: <?php echo sanitize($order['Username']); ?> (<?php echo sanitize($order['Email']); ?>)</p>
    <p>Status: <?php echo sanitize($order['Status']); ?> · Total: <?php echo format_price((float)$order['TotalPrice']); ?></p>
    <p>Shipping: <?php echo sanitize($order['ShippingAddress']); ?></p>
</div>

<div class="card">
    <h3>Items</h3>
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
</div>

<div class="card">
    <h3>Payments</h3>
    <table>
        <thead><tr><th>Method</th><th>Amount</th><th>Status</th><th>Transaction</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
            <tr>
                <td><?php echo sanitize($p['PaymentMethod']); ?></td>
                <td><?php echo format_price((float)$p['Amount']); ?></td>
                <td><?php echo sanitize($p['Status']); ?></td>
                <td><?php echo sanitize($p['TransactionID']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


